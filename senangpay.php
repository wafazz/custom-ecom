<?php
$merchant_id = '353176882989426';
$secret_key  = 'SK-sk1Av5A5guGju1G4ASjq';

$order_id = 'ORDER'.rand(1000, 9999);
$detail   = 'Payment for Order #'.$order_id;
$amount   = number_format(rand(10, 200),2, '.', ''); // Example amount
$name     = 'John Doe';
$email    = 'john@email.com';
$phone    = '0123456789';

$hash = hash_hmac('sha256', $secret_key . $detail . $amount . $order_id, $secret_key);

$postData = [
    'detail'   => $detail,
    'amount'   => $amount,
    'order_id' => $order_id,
    'name'     => $name,
    'email'    => $email,
    'phone'    => $phone,
    'hash'     => $hash
];

$payment_url = "https://sandbox.senangpay.my/payment/$merchant_id";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $payment_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);

curl_close($ch);

echo $response;

// echo "<pre>";
// print_r(json_decode($response, true));
// echo "</pre>";