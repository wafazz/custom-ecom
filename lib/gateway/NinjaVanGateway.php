<?php

class NinjaVanGateway
{
    private $clientId;
    private $clientSecret;
    private $sandbox;
    private $baseUrl;
    private $countryCode;

    public function __construct($clientId, $clientSecret, $sandbox = false, $countryCode = 'MY')
    {
        $this->clientId = trim($clientId);
        $this->clientSecret = trim($clientSecret);
        $this->sandbox = $sandbox;
        $this->countryCode = strtoupper(trim($countryCode));

        if ($sandbox) {
            // Sandbox always uses SG endpoint
            $this->baseUrl = 'https://api-sandbox.ninjavan.co/sg';
        } else {
            $this->baseUrl = 'https://api.ninjavan.co/' . $this->countryCode;
        }
    }

    public function authenticate()
    {
        $url = $this->baseUrl . '/2.0/oauth/access_token';

        $data = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        ];

        return $this->apiRequest('POST', $url, $data);
    }

    public function createOrder($accessToken, $orderData)
    {
        $url = $this->baseUrl . '/4.2/orders';
        return $this->apiRequest('POST', $url, $orderData, $accessToken);
    }

    public function getWaybill($accessToken, $trackingNo)
    {
        $url = $this->baseUrl . '/2.0/reports/waybill?tids=' . urlencode($trackingNo);
        return $this->apiRequest('GET', $url, [], $accessToken, true);
    }

    public function cancelOrder($accessToken, $trackingNo)
    {
        $url = $this->baseUrl . '/2.2/orders/' . urlencode($trackingNo);
        return $this->apiRequest('DELETE', $url, [], $accessToken);
    }

    public function verifyWebhookSignature($body, $signature)
    {
        $computed = hash_hmac('sha256', $body, $this->clientSecret);
        return hash_equals($computed, $signature);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    private function apiRequest($method, $url, $data = [], $accessToken = null, $rawResponse = false)
    {
        $ch = curl_init();

        $headers = ['Accept: application/json'];

        if ($accessToken) {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }

        if ($method === 'POST' && !empty($data)) {
            $jsonBody = json_encode($data);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error, 'http_code' => 0];
        }

        if ($rawResponse) {
            return ['success' => ($httpCode >= 200 && $httpCode < 300), 'data' => $response, 'http_code' => $httpCode];
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $decoded;
        }

        return [
            'success'   => false,
            'error'     => $decoded['error']['message'] ?? ($decoded['message'] ?? 'API error'),
            'http_code' => $httpCode,
            'response'  => $decoded,
        ];
    }
}
