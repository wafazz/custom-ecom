<?php

/**
 * BayarcashGateway — Standalone, framework-agnostic Bayarcash payment gateway.
 * No DB, no ORM, no external dependencies. Just require_once + pass credentials.
 *
 * Usage:
 *   require_once 'BayarcashGateway.php';
 *   $gw = new BayarcashGateway($apiToken, $secretKey, $portalKey, true); // sandbox
 *   $intent = $gw->createPaymentIntent($orderNo, $amount, $name, $email, $phone, $callbackUrl, $returnUrl);
 *   $url = $gw->getRedirectUrl($intent);
 */
class BayarcashGateway
{
    public const CHANNEL_FPX = 1;
    public const CHANNEL_BANK_TRANSFER = 2;
    public const CHANNEL_DIRECT_DEBIT = 3;
    public const CHANNEL_FPX_LOC = 4;
    public const CHANNEL_DUITNOW_ONLINE = 5;
    public const CHANNEL_DUITNOW_QR = 6;
    public const CHANNEL_SPAYLATER = 7;
    public const CHANNEL_BOOST_PAYFLEX = 8;
    public const CHANNEL_QRIS_BANKING = 9;
    public const CHANNEL_QRIS_EWALLET = 10;
    public const CHANNEL_NETS_SG = 11;

    public const STATUS_NEW = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_UNSUCCESSFUL = 2;
    public const STATUS_SUCCESSFUL = 3;
    public const STATUS_CANCELLED = 4;

    private $apiToken;
    private $secretKey;
    private $portalKey;
    private $sandbox;
    private $baseUrl;

    public function __construct($apiToken, $secretKey, $portalKey, $sandbox = false)
    {
        $this->apiToken = trim($apiToken);
        $this->secretKey = trim($secretKey);
        $this->portalKey = trim($portalKey);
        $this->sandbox = $sandbox;
        $this->baseUrl = $sandbox
            ? 'https://api.console.bayarcash-sandbox.com/v3'
            : 'https://api.console.bayar.cash/v3';
    }

    public function createPaymentIntent($orderNumber, $amount, $payerName, $payerEmail, $payerPhone, $callbackUrl, $returnUrl, $channel = self::CHANNEL_FPX)
    {
        $data = [
            'portal_key'             => $this->portalKey,
            'order_number'           => $orderNumber,
            'amount'                 => number_format($amount, 2, '.', ''),
            'payer_name'             => $payerName,
            'payer_email'            => $payerEmail,
            'payer_telephone_number' => $payerPhone,
            'callback_url'           => $callbackUrl,
            'return_url'             => $returnUrl,
            'payment_channel'        => $channel,
        ];

        $paymentChannel = $data['payment_channel'];
        $paymentChannel = is_array($paymentChannel) ? $paymentChannel : [$paymentChannel];
        $paymentChannel = implode(',', $paymentChannel);

        $checksumData = [
            'amount'          => $data['amount'],
            'order_number'    => $data['order_number'],
            'payer_email'     => $data['payer_email'],
            'payer_name'      => $data['payer_name'],
            'payment_channel' => $paymentChannel,
        ];

        ksort($checksumData);
        $payloadString = implode('|', $checksumData);
        $data['checksum'] = hash_hmac('sha256', $payloadString, $this->secretKey);

        error_log("Bayarcash checksum debug: payload=[{$payloadString}] secret_start=[" . substr($this->secretKey, 0, 4) . "...] checksum=[{$data['checksum']}]");

        return $this->apiRequest('POST', '/payment-intents', $data);
    }

    public function getPaymentIntent($paymentIntentId)
    {
        return $this->apiRequest('GET', '/payment-intents/' . $paymentIntentId);
    }

    public function getTransactionByOrderNumber($orderNumber)
    {
        return $this->apiRequest('GET', '/transactions', ['order_number' => $orderNumber]);
    }

    public function getTransactionByRef($refNumber)
    {
        return $this->apiRequest('GET', '/transactions', ['reference_number' => $refNumber]);
    }

    public function verifyCallbackChecksum($callbackData)
    {
        if (empty($callbackData['checksum']) || empty($this->secretKey)) {
            return false;
        }

        $receivedChecksum = $callbackData['checksum'];

        $payload = [
            'record_type'              => $callbackData['record_type'] ?? '',
            'transaction_id'           => $callbackData['transaction_id'] ?? '',
            'exchange_reference_number' => $callbackData['exchange_reference_number'] ?? '',
            'exchange_transaction_id'  => $callbackData['exchange_transaction_id'] ?? '',
            'order_number'             => $callbackData['order_number'] ?? '',
            'currency'                 => $callbackData['currency'] ?? '',
            'amount'                   => $callbackData['amount'] ?? '',
            'payer_name'               => $callbackData['payer_name'] ?? '',
            'payer_email'              => $callbackData['payer_email'] ?? '',
            'payer_bank_name'          => $callbackData['payer_bank_name'] ?? '',
            'status'                   => $callbackData['status'] ?? '',
            'status_description'       => $callbackData['status_description'] ?? '',
            'datetime'                 => $callbackData['datetime'] ?? '',
        ];

        $expected = $this->generateChecksum($payload);

        return hash_equals($expected, $receivedChecksum);
    }

    public function processCallback($postData)
    {
        if (!$this->verifyCallbackChecksum($postData)) {
            return ['success' => false, 'error' => 'Invalid checksum'];
        }

        return [
            'success'         => true,
            'order_number'    => $postData['order_number'] ?? '',
            'status'          => (int)($postData['status'] ?? 0),
            'is_paid'         => (int)($postData['status'] ?? 0) === self::STATUS_SUCCESSFUL,
            'transaction_id'  => $postData['transaction_id'] ?? '',
            'payment_channel' => $postData['payment_channel'] ?? '',
        ];
    }

    public function getRedirectUrl($paymentIntent)
    {
        return $paymentIntent['url'] ?? null;
    }

    public function isSuccessful($statusCode)
    {
        return (int)$statusCode === self::STATUS_SUCCESSFUL;
    }

    public function isSandbox()
    {
        return $this->sandbox;
    }

    public function getPortalKey()
    {
        return $this->portalKey;
    }

    public static function statusLabel($status)
    {
        $labels = [
            self::STATUS_NEW          => 'New',
            self::STATUS_PENDING      => 'Pending',
            self::STATUS_UNSUCCESSFUL => 'Failed',
            self::STATUS_SUCCESSFUL   => 'Successful',
            self::STATUS_CANCELLED    => 'Cancelled',
        ];
        return $labels[(int)$status] ?? 'Unknown';
    }

    public static function channelLabel($channel)
    {
        $labels = [
            self::CHANNEL_FPX            => 'FPX Online Banking',
            self::CHANNEL_BANK_TRANSFER  => 'Manual Bank Transfer',
            self::CHANNEL_DIRECT_DEBIT   => 'Direct Debit',
            self::CHANNEL_FPX_LOC        => 'FPX Line of Credit',
            self::CHANNEL_DUITNOW_ONLINE => 'DuitNow Online Banking/Wallets',
            self::CHANNEL_DUITNOW_QR     => 'DuitNow QR',
            self::CHANNEL_SPAYLATER      => 'SPayLater',
            self::CHANNEL_BOOST_PAYFLEX  => 'Boost PayFlex',
            self::CHANNEL_QRIS_BANKING   => 'QRIS Indonesia Online Banking',
            self::CHANNEL_QRIS_EWALLET   => 'QRIS Indonesia eWallet',
            self::CHANNEL_NETS_SG        => 'NETS Singapore',
        ];
        return $labels[(int)$channel] ?? 'Unknown';
    }

    // --- Private helpers ---

    private function generateChecksum($data)
    {
        ksort($data);
        $payload = implode('|', $data);
        return hash_hmac('sha256', $payload, $this->secretKey);
    }

    private function apiRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'Accept: application/json',
        ];

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error, 'http_code' => 0];
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            return $decoded;
        }

        return [
            'success'   => false,
            'error'     => $decoded['message'] ?? 'API error',
            'http_code' => $httpCode,
            'response'  => $decoded,
        ];
    }
}
