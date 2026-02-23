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
    public const CHANNEL_DUITNOW_ONLINE = 5;
    public const CHANNEL_DUITNOW_QR = 6;
    public const CHANNEL_SPAYLATER = 7;
    public const CHANNEL_CREDIT_CARD = 12;

    public const STATUS_NEW = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_UNSUCCESSFUL = 2;
    public const STATUS_SUCCESSFUL = 3;
    public const STATUS_CANCELLED = -1;

    private $apiToken;
    private $secretKey;
    private $portalKey;
    private $sandbox;
    private $baseUrl;

    public function __construct($apiToken, $secretKey, $portalKey, $sandbox = false)
    {
        $this->apiToken = $apiToken;
        $this->secretKey = $secretKey;
        $this->portalKey = $portalKey;
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

        $data['checksum'] = $this->generateChecksum($data);

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
        unset($callbackData['checksum']);

        $expected = $this->generateChecksum($callbackData);

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
            self::CHANNEL_DUITNOW_QR     => 'DuitNow QR',
            self::CHANNEL_DUITNOW_ONLINE => 'DuitNow Online Banking',
            self::CHANNEL_CREDIT_CARD    => 'Credit Card',
            self::CHANNEL_SPAYLATER      => 'SPayLater',
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
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
