<?php

namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../email-order.php';

require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BotSenangpayCotroller
{
    public function handleBot()
    {

        $conn = getDbConnection();
        $dateNow = dateNow();
        $domainURL = getMainUrl();

        $getSenangPay = $conn->query("SELECT * FROM `senangpay_api` ORDER BY id DESC LIMIT 1");
        $dataSenangPay = $getSenangPay->fetch_array();

        if ($dataSenangPay["type"] == 'sandbox') {
            $merchant_id = $dataSenangPay["merchant_id"];
            $secret_key  = $dataSenangPay["secret_key"];
            $urlsubmit = $dataSenangPay["sandbox_url"];
            //echo "sandbox<br><br>";
        } else {
            $merchant_id = $dataSenangPay["pro_merchant_id"];
            $secret_key  = $dataSenangPay["pro_secret_key"];
            $urlsubmit = $dataSenangPay["production_url"];
            //echo "Production<br><br>";
        }

        $pendingPayments = $conn->query("SELECT * FROM customer_orders WHERE status = '10' AND payment_code != 'nill' AND deleted_at IS NULL ORDER BY created_at ASC LIMIT 10");

        $x = 1;
        while ($order = $pendingPayments->fetch_array()) {
            $orderId = $order['id'];
            $orderAmount = $order['myr_value_include_postage'];
            $dateCreated = $order['created_at'];
            $paymentCode = $order['payment_code'];


            $dataOrder = getOrder(1, $orderId);

            $order_id = 'ORDERID_' . $orderId;

            $hash = hash_hmac('sha256', $merchant_id . $secret_key . $order_id, $secret_key);

            $params = [
                "merchant_id" => $merchant_id,
                "order_id"  => $order_id,
                "hash"    => $hash
            ];

            $url = $urlsubmit . "apiv1/query_order_status?" . http_build_query($params);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo curl_error($ch);
                curl_close($ch);
                exit;
            }

            echo "<pre>";
            print_r(json_decode($response, true));
            echo "</pre>";

            curl_close($ch);

            $dataResponse = json_decode($response, true);

            $isPaid = false;



            date_default_timezone_set('Asia/Kuala_Lumpur');

            foreach ($dataResponse['data'] as $row) {

                if ($row['payment_info']['status'] === 'paid') {

                    $isPaid = true;
                    $method = $row['payment_info']['payment_mode'];
                    $transaction_reference = $row['payment_info']['transaction_reference'];
                    $transaction_date = $row['payment_info']['transaction_date'];


                    break;
                }
            }

            echo $isPaid ? "PAID<br>" : "UNPAID ORDER DETECTED!<br>";
            echo "Payment Method: " . ($isPaid ? $method : 'N/A') . "<br>";
            echo "Transaction Reference: " . ($isPaid ? $transaction_reference : 'N/A') . "<br>";
            echo "Transaction Date: " . ($isPaid ? date('Y-m-d H:i:s', $transaction_date) : 'N/A') . "<br>";

            $countPayment = count($dataResponse['data']);
            $countPayments = $countPayment - 1;

            $payment = $dataResponse['data'][$countPayments]['payment_info'];

            $ts = $payment['transaction_date'];

            if ($ts > 9999999999) {
                $ts = $ts / 1000;
            }

            $transactionDate = date('Y-m-d H:i:s', $ts);

            if ($payment['status'] == 'paid') {

                $conn->query("UPDATE `customer_orders` SET `status`='1', `payment_channel`='" . $payment['payment_mode'] . "', `payment_code`='" . $payment['transaction_reference'] . "', `payment_url`='" . $payment['transaction_reference'] . "', `updated_at`='$transactionDate' WHERE `id`='" . $orderId . "'");

                $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `session_id`='" . $dataOrder["session_id"] . "' AND deleted_at IS NULL");
                foreach ($getCartLock as $cartLockItem) {

                    $conn->query("UPDATE `cart` SET `updated_at`='$transactionDate', `deleted_at`=NULL, `status`='1' WHERE `id`='" . $cartLockItem["cart_id"] . "'");
                }

                $hashOrder = hash("sha256", $orderId . "_" . $dataOrder['customer_name'] . "_" . $dateNow);
                $conn->query("INSERT INTO order_details(order_id, hash_code, created_at) VALUES ('$orderId','$hashOrder','$dateNow')");

                $emailData = [
                    'CustomerName' => $dataOrder['customer_name'],
                    'OrderID'      => $orderId,
                    'OrderLink' => $domainURL . "order-details/" . $hashOrder,
                ];

                $emailHTML = getEmailTemplate($emailData);

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
                    $mail->addAddress($dataOrder['customer_email'], $dataOrder['customer_name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Order Confirmation - Rozeyana';
                    $mail->Body    = $emailHTML;
                    $mail->AltBody = 'Thank you for your order #' . $orderId . '. View: ' . $domainURL . 'order-details/' . $hashOrder;

                    $mail->send();
                } catch (Exception $e) {
                    error_log("❌ Mail error: {$mail->ErrorInfo}");
                }
                //update order status to paid
                //$updateOrder = $conn->query("UPDATE `customer_orders` SET `status` = 1, `payment_channel` = 'senangpay', `updated_at` = NOW() WHERE `id` = '$orderId'");
                $btn = "<span style='color:green;font-weight:bold;'>PAID</span>";
            } else {
                //$updateOrder = $conn->query("UPDATE `customer_orders` SET `status` = 10, `updated_at` = NOW() WHERE `id` = '$orderId'");      
                $btn = "<span style='color:red;font-weight:bold;'>UNPAID</span>";
            }



            //echo $countPayment."<br>";


            echo "$x) Order ID = $orderId ($dateCreated), Amount = RM $orderAmount <br>Ref: " . $payment['transaction_reference'] . "<br>Status: " . $btn . " (" . $payment['payment_mode'] . ") on " . $transactionDate . "<br><br>";

            $x++;
        }
    }
}
