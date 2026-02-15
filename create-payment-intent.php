<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_XXXXXXXXXXXXXXXXXXXX'); // Replace with your key

// Sanitize values
$amount = 10000; // in sen (e.g. RM100.00)
$currency = 'myr';
$payMethod = $_POST['pay'] ?? 'cc'; // 'cc' or 'fpx'

// Build basic metadata for the order
$metadata = [
    'customer_name' => $_POST['fname'] . ' ' . $_POST['lname'],
    'email' => $_POST['email'],
    // Add more if needed
];

try {
    $params = [
        'amount' => $amount,
        'currency' => $currency,
        'payment_method_types' => [$payMethod == 'fpx' ? 'fpx' : 'card'],
        'metadata' => $metadata,
    ];

    if ($payMethod == 'fpx') {
        $params['return_url'] = 'https://rozyana.com/stripe-success.php';
        $intent = \Stripe\PaymentIntent::create($params);

        echo json_encode([
            'next_action' => $intent->next_action['redirect_to_url']['url'],
        ]);
    } else {
        $intent = \Stripe\PaymentIntent::create($params);
        echo json_encode(['clientSecret' => $intent->client_secret]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
