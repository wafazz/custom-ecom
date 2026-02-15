<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

// === INIT ===
$conn = getDbConnection();
$dateNow = dateNow();
$domainURL = 'https://rozeyana.com/'; // Change as needed

// === FETCH STRIPE KEYS FROM DB ===
$stripeQuery = "SELECT * FROM `stripe_setting` WHERE id='1'";
$stripeResult = $conn->query($stripeQuery);
$stripeRow = $stripeResult->fetch_array();

\Stripe\Stripe::setApiKey($stripeRow["secret_key"]); // Your Stripe Secret Key

// Example: PaymentIntent ID from your database or webhook payload
$paymentIntentId = $_GET['payment_intent'] ?? null;

if (!$paymentIntentId) {
    die(json_encode(['error' => 'Missing payment_intent ID']));
}

try {
    // Retrieve the PaymentIntent from Stripe
    $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

    // Check payment status
    if ($paymentIntent->status === 'succeeded') {
        echo "<pre>";

        $details = [
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount_received / 100,
            'currency' => strtoupper($paymentIntent->currency),
            'payment_method' => $paymentIntent->payment_method_types,
            'customer_email' => $paymentIntent->receipt_email,
            'proof' => $paymentIntent
        ];
        echo "Payment Status: " . $details['status'] . "\n";
        echo "Payment Amount: " . $details['currency'] . $details['amount'] . "\n";
        echo "Payment Method: " . $details['payment_method'][0] . "\n";
        echo "Email: " . $details['customer_email'] . "\n";
        //echo "Details: " . $details['proof'] . "\n";
        // echo json_encode([
        //     'status' => 'success',
        //     'amount' => $paymentIntent->amount_received / 100,
        //     'currency' => strtoupper($paymentIntent->currency),
        //     'payment_method' => $paymentIntent->payment_method,
        //     'customer_email' => $paymentIntent->charges->data[0]->billing_details->email ?? null,
        //     'proof' => $paymentIntent
        // ]);
        echo "</pre>";
    } else {
        echo json_encode([
            'status' => 'pending',
            'message' => 'Payment not completed yet',
            'stripe_status' => $paymentIntent->status
        ]);
    }

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}