<?php

require_once __DIR__ . '/BaseModel.php';

class Bayarcash extends BaseModel
{
    protected $table = 'bayarcash_api';

    public const CHANNEL_FPX = 1;
    public const CHANNEL_DUITNOW_QR = 2;
    public const CHANNEL_DUITNOW_ONLINE = 3;
    public const CHANNEL_CREDIT_CARD = 4;
    public const CHANNEL_SPAYLATER = 5;

    public const STATUS_NEW = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_UNSUCCESSFUL = 2;
    public const STATUS_SUCCESSFUL = 3;
    public const STATUS_CANCELLED = -1;

    private $apiToken = null;
    private $secretKey = null;
    private $portalKey = null;
    private $sandbox = false;
    private $baseUrl = 'https://console.bayar.cash/api';

    public function loadConfig()
    {
        $row = $this->findOne([]);
        if (!$row) return false;

        if ($row['type'] == 'sandbox') {
            $this->apiToken = $row['sandbox_api_token'];
            $this->secretKey = $row['sandbox_secret_key'];
            $this->portalKey = $row['sandbox_portal_key'];
            $this->sandbox = true;
        } else {
            $this->apiToken = $row['api_token'];
            $this->secretKey = $row['secret_key'];
            $this->portalKey = $row['portal_key'];
            $this->sandbox = false;
        }

        return true;
    }

    public function isSandbox()
    {
        return $this->sandbox;
    }

    public function getPortalKey()
    {
        return $this->portalKey;
    }

    public function createPaymentIntent($orderNumber, $amount, $payerName, $payerEmail, $payerPhone, $callbackUrl, $returnUrl, $channel = self::CHANNEL_FPX)
    {
        $data = [
            'portal_key'              => $this->portalKey,
            'order_number'            => $orderNumber,
            'amount'                  => number_format($amount, 2, '.', ''),
            'payer_name'              => $payerName,
            'payer_email'             => $payerEmail,
            'payer_telephone_number'  => $payerPhone,
            'callback_url'            => $callbackUrl,
            'return_url'              => $returnUrl,
            'payment_channel'         => $channel,
        ];

        $data['checksum'] = $this->generateChecksum($data);

        $response = $this->apiRequest('POST', '/v3/payment-intents', $data);
        return $response;
    }

    public function getPaymentIntent($paymentIntentId)
    {
        return $this->apiRequest('GET', '/v3/payment-intents/' . $paymentIntentId);
    }

    public function getTransactionByOrderNumber($orderNumber)
    {
        return $this->apiRequest('GET', '/v3/transactions', ['order_number' => $orderNumber]);
    }

    public function getTransactionByRef($refNumber)
    {
        return $this->apiRequest('GET', '/v3/transactions', ['reference_number' => $refNumber]);
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

    public function isSuccessful($statusCode)
    {
        return (int)$statusCode === self::STATUS_SUCCESSFUL;
    }

    public function processCallback($callbackData)
    {
        if (!$this->verifyCallbackChecksum($callbackData)) {
            return ['success' => false, 'error' => 'Invalid checksum'];
        }

        $orderNumber = $callbackData['order_number'] ?? '';
        $status = (int)($callbackData['status'] ?? 0);
        $transactionId = $callbackData['transaction_id'] ?? '';
        $paymentChannel = $callbackData['payment_channel'] ?? '';

        return [
            'success'         => true,
            'order_number'    => $orderNumber,
            'status'          => $status,
            'is_paid'         => $status === self::STATUS_SUCCESSFUL,
            'transaction_id'  => $transactionId,
            'payment_channel' => $paymentChannel,
        ];
    }

    public function getRedirectUrl($paymentIntent)
    {
        return $paymentIntent['url'] ?? null;
    }

    // --- Private helpers ---

    private function generateChecksum($data)
    {
        ksort($data);
        $payload = '';
        foreach ($data as $val) {
            $payload .= $val;
        }
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

    // --- Status label helpers ---

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
}
