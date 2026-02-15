<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';
require 'email-order.php'; // Contains getEmailTemplate($data)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// === INIT ===
$conn = getDbConnection();
$dateNow = dateNow();
$domainURL = 'https://rozeyana.com/'; // Change as needed

// === FETCH STRIPE KEYS FROM DB ===
$stripeQuery = "SELECT * FROM `stripe_setting` WHERE id='1'";
$stripeResult = $conn->query($stripeQuery);

if (!$stripeResult || $stripeResult->num_rows === 0) {
    http_response_code(500);
    exit('Stripe configuration not found.');
}

$stripeRow = $stripeResult->fetch_array();
\Stripe\Stripe::setApiKey($stripeRow["secret_key"]);
$endpoint_secret = $stripeRow["webhook_secret"];

// === STRIPE PAYLOAD HANDLING ===
$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

// OPTIONAL DEBUG LOGGING
// file_put_contents("webhook_debug.log", print_r([
//     'timestamp' => date('c'),
//     'payload' => $payload,
//     'signature' => $sig_header
// ], true), FILE_APPEND);
file_put_contents(
    "webhook_debug.log",
    json_encode([
        'timestamp' => date('c'),
        'payload'   => $payload,
        'signature' => $sig_header
    ], JSON_PRETTY_PRINT) . PHP_EOL,
    FILE_APPEND
);

// === VERIFY SIGNATURE ===
try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit('❌ Invalid payload: ' . $e->getMessage());
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit('❌ Invalid signature: ' . $e->getMessage());
}

// === PROCESS PAID SESSION ===
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $successUrl = $session->success_url;
    parse_str(parse_url($successUrl, PHP_URL_QUERY), $queryParams);

    $session_id = $queryParams['session_id'] ?? '';
    $payment_status = $session->payment_status;
    $sessionStripeId = $session->id;
    $paymentIntent = $session->payment_intent;
    $amount = $session->amount_total / 100;
    $currency = strtoupper($session->currency);
    $customerName = $conn->real_escape_string($session->customer_details->name ?? '');
    $customerEmail = $conn->real_escape_string($session->customer_details->email ?? '');

    if ($payment_status === 'paid' && !empty($session_id)) {
        $result = $conn->query("SELECT * FROM order_temp_data WHERE session_id='$session_id' AND status='0'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // === ESCAPE ORDER FIELDS ===
            $first_name = $conn->real_escape_string($row['first_name']);
            $last_name = $conn->real_escape_string($row['last_name']);
            $add_1 = $conn->real_escape_string($row['add_1']);
            $add_2 = $conn->real_escape_string($row['add_2']);
            $city = $conn->real_escape_string($row['city']);
            $state = $conn->real_escape_string($row['state']);
            $postcode = $conn->real_escape_string($row['postcode']);
            $country_name = $conn->real_escape_string($row['country_name']);
            $country_id = $conn->real_escape_string($row['country_id']);
            $phone = $conn->real_escape_string($row['phone']);
            $email = $conn->real_escape_string($row['email']);
            $remark = $conn->real_escape_string($row['remark']);
            $amountM = $conn->real_escape_string($row['amount']);
            $postage = $conn->real_escape_string($row['shipping_cost']);
            $method = $row['method'];
            $currency_sign = $row['currency_sign'];
            $rate = $row['rate'] ?? 1;

            $convertMYRWithPostage = ($amountM + $postage) / $rate;
            $convertMYRWithoutPostage = $amountM / $rate;

            $validateLocked = $conn->query("SELECT * FROM cart_lock WHERE session_id='$session_id' AND status='0'");

            if ($validateLocked && $validateLocked->num_rows >= 1) {
                while ($rowLock = $validateLocked->fetch_array()) {
                    $cartId = $conn->real_escape_string($rowLock['cart_id']);
                    $conn->query("
                        UPDATE cart 
                        SET 
                            updated_at = '$dateNow', 
                            deleted_at = NULL, 
                            status = '1' 
                        WHERE 
                            id = '$cartId' 
                            AND session_id = '$session_id'
                    ");
                }
            }

            $qty = 0;
            $var_ids = [];

            $cartSQL = "SELECT * FROM cart WHERE session_id='$session_id' AND deleted_at IS NULL AND status IN (0,1)";
            $cartRes = $conn->query($cartSQL);
            while ($c = $cartRes->fetch_assoc()) {
                $qty += $c['quantity'];
                $var_ids[] = '[' . $c['pv_id'] . ']';
            }

            $var_idss = array_unique($var_ids);

            $product_var_id = implode(',', $var_idss);
            $payment_channel = $method === 'fpx' ? 'Online Banking' : 'Credit/Debit Card';

            // === INSERT ORDER ===
            $exists = $conn->query("SELECT id FROM customer_orders WHERE session_id='$session_id'");

            if ($exists && $exists->num_rows < 1) {
                $insertSQL = "
                    INSERT INTO customer_orders (
                        session_id, order_to, product_var_id, total_qty, total_price, postage_cost, currency_sign,
                        country_id, country, state, city, postcode, address_2, address_1, customer_name, customer_name_last,
                        customer_phone, customer_email, status, payment_channel, payment_code, payment_url, ship_channel,
                        courier_service, awb_number, tracking_url, created_at, updated_at, remark_comment,
                        tracking_milestone, to_myr_rate, myr_value_include_postage, myr_value_without_postage, printed_awb
                    ) VALUES (
                        '$session_id', '1', '$product_var_id', '$qty', '$amountM', '$postage', '$currency_sign',
                        '$country_id', '$country_name', '$state', '$city', '$postcode', '$add_2', '$add_1', '$first_name',
                        '$last_name', '$phone', '$email', '1', '$payment_channel', '$sessionStripeId', '$paymentIntent',
                        'Doorstep Delivery', '', '', '', '$dateNow', '$dateNow', '$remark',
                        '', '$rate', '$convertMYRWithPostage', '$convertMYRWithoutPostage', '0'
                    )
                ";
                $conn->query($insertSQL);
                $lastInsertedId = $conn->insert_id;

                // === ORDER HASH ===
                $hashOrder = hash("sha256", $lastInsertedId . "_" . $first_name . "_" . $dateNow);
                $conn->query("INSERT INTO order_details(order_id, hash_code, created_at) VALUES ('$lastInsertedId','$hashOrder','$dateNow')");

                // === CLEANUP TEMP & CART ===
                $conn->query("UPDATE order_temp_data SET deleted_at='$dateNow', status='1' WHERE session_id='$session_id'");


                //$conn->query("UPDATE cart SET updated_at='$dateNow', status='1' WHERE session_id='$session_id' AND deleted_at IS NULL");

                // === PREPARE EMAIL ===
                $data = [
                    'CustomerName' => $first_name,
                    'OrderID' => $lastInsertedId,
                    'OrderLink' => $domainURL . "order-details/" . $hashOrder,
                ];
                $emailHTML = getEmailTemplate($data);

                // === SEND EMAIL ===
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp-relay.brevo.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = '889d41001@smtp-brevo.com';
                    $mail->Password   = 'xsmtpsib-XXXXXXXXXXXXXXXXXXXX';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('orders-noreply@rozeyana.com', 'Rozeyana.com');
                    $mail->addAddress($email, $first_name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Order Confirmation - Rozeyana';
                    $mail->Body    = $emailHTML;
                    $mail->AltBody = 'Thank you for your order #' . $lastInsertedId . '. View: ' . $domainURL . 'order-details/' . $hashOrder;

                    $mail->send();
                } catch (Exception $e) {
                    error_log("❌ Mail error: {$mail->ErrorInfo}");
                }
            }



            http_response_code(200);
            echo "✅ Order processed.";
        } else {
            http_response_code(404);
            echo "❌ Temp order not found.";
        }
    } else {
        http_response_code(200);
        echo "ℹ️ Payment not completed.";
    }
} else {
    http_response_code(200);
    echo "ℹ️ Event ignored.";
}
