<?php

/**
 * SenangPayGateway — Standalone, framework-agnostic SenangPay payment gateway.
 * No DB, no ORM, no external dependencies. Just require_once + pass credentials.
 *
 * Usage:
 *   require_once 'SenangPayGateway.php';
 *   $gw = new SenangPayGateway($merchantId, $secretKey, true); // sandbox
 *   $data = $gw->buildPaymentData($detail, $amount, $orderId, $name, $email, $phone);
 *   // POST $data to $gw->getPaymentUrl()
 */
class SenangPayGateway
{
    private $merchantId;
    private $secretKey;
    private $sandbox;
    private $baseUrl;

    public function __construct($merchantId, $secretKey, $sandbox = false)
    {
        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->sandbox = $sandbox;
        $this->baseUrl = $sandbox
            ? 'https://sandbox.senangpay.my/'
            : 'https://app.senangpay.my/';
    }

    public function getPaymentUrl()
    {
        return $this->baseUrl . 'payment/' . $this->merchantId;
    }

    public function generatePaymentHash($detail, $amount, $orderId)
    {
        return hash_hmac('sha256', $this->secretKey . urldecode($detail) . urldecode($amount) . urldecode($orderId), $this->secretKey);
    }

    public function buildPaymentData($detail, $amount, $orderId, $name, $email, $phone)
    {
        return [
            'detail'   => $detail,
            'amount'   => $amount,
            'order_id' => $orderId,
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'hash'     => $this->generatePaymentHash($detail, $amount, $orderId),
        ];
    }

    public function verifyCallbackHash($statusId, $orderId, $transactionId, $msg, $receivedHash)
    {
        $computed = hash_hmac('sha256', $this->secretKey . urldecode($statusId) . urldecode($orderId) . urldecode($transactionId) . urldecode($msg), $this->secretKey);
        return hash_equals($computed, urldecode($receivedHash));
    }

    public function verifyFromPost($postData)
    {
        $statusId      = $postData['status_id'] ?? '';
        $orderId       = $postData['order_id'] ?? '';
        $transactionId = $postData['transaction_id'] ?? '';
        $msg           = $postData['msg'] ?? '';
        $hash          = $postData['hash'] ?? '';

        return $this->verifyCallbackHash($statusId, $orderId, $transactionId, $msg, $hash);
    }

    public function verifyFromGet($getData)
    {
        $statusId      = $getData['status_id'] ?? '';
        $orderId       = $getData['order_id'] ?? '';
        $transactionId = $getData['transaction_id'] ?? '';
        $msg           = $getData['msg'] ?? '';
        $hash          = $getData['hash'] ?? '';

        return $this->verifyCallbackHash($statusId, $orderId, $transactionId, $msg, $hash);
    }

    public function isSuccessful($statusId)
    {
        return (string)$statusId === '1';
    }

    public function isPending($statusId)
    {
        return (string)$statusId === '2';
    }

    public function isSandbox()
    {
        return $this->sandbox;
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }
}
