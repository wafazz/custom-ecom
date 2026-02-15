<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_XXXXXXXXXXXXXXXXXXXX'); // Replace with your key

$amount = 10000; // Example: 100.00 MYR
$currency = 'myr';

try {
    $intent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => $currency,
        'automatic_payment_methods' => ['enabled' => true],
    ]);

    echo json_encode(['clientSecret' => $intent->client_secret]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
