<?php

namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../email-order.php';
require_once __DIR__ . '/../../model/Bayarcash.php';

require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CheckoutController
{
    public function index()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Checkout";

        if (!isset($_SESSION["test"]) && !empty($_GET["test"])) {
            $_SESSION["test"] = "yes";
        }

        if (isset($_GET["developer"]) && !empty($_GET["developer"])) {
            $_SESSION["developer"] = true;
        }

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $stripe = "SELECT * FROM `stripe_setting` WHERE id='1'";

        $stripes = $conn->query($stripe);

        $stripesRow = $stripes->fetch_array();

        $newArrival = newProduct(8);

        $sessionid = $_SESSION["session_id"];

        $sql = "SELECT * FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $query = $conn->query($sql);

        $myState = stateMalaysia();

        if ($query->num_rows < "1") {
            header("Location: /");
            exit;
        }

        unset($_SESSION["fname"]);
        unset($_SESSION["lname"]);
        unset($_SESSION["add_1"]);
        unset($_SESSION["add_2"]);
        unset($_SESSION["city"]);
        unset($_SESSION["state"]);
        unset($_SESSION["postcode"]);
        unset($_SESSION["ophone"]);
        unset($_SESSION["oemail"]);
        unset($_SESSION["remark"]);

        require_once __DIR__ . '/../../view/ecom/e-checkout-keya88.php';
    }
    
    public function index2()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Checkout";

        if (!isset($_SESSION["test"]) && !empty($_GET["test"])) {
            $_SESSION["test"] = "yes";
        }

        if (isset($_GET["developer"]) && !empty($_GET["developer"])) {
            $_SESSION["developer"] = true;
        }

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $stripe = "SELECT * FROM `stripe_setting` WHERE id='1'";

        $stripes = $conn->query($stripe);

        $stripesRow = $stripes->fetch_array();

        $newArrival = newProduct(8);

        $sessionid = $_SESSION["session_id"];

        $sql = "SELECT * FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $query = $conn->query($sql);

        $myState = stateMalaysia();

        if ($query->num_rows < "1") {
            header("Location: /");
            exit;
        }

        unset($_SESSION["fname"]);
        unset($_SESSION["lname"]);
        unset($_SESSION["add_1"]);
        unset($_SESSION["add_2"]);
        unset($_SESSION["city"]);
        unset($_SESSION["state"]);
        unset($_SESSION["postcode"]);
        unset($_SESSION["ophone"]);
        unset($_SESSION["oemail"]);
        unset($_SESSION["remark"]);

        require_once __DIR__ . '/../../view/ecom/e-checkout2-keya88.php';
    }

    public function nextCalculate()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Checkout";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $stripe = "SELECT * FROM `stripe_setting` WHERE id='1'";

        $stripes = $conn->query($stripe);

        $stripesRow = $stripes->fetch_array();

        $newArrival = newProduct(8);

        $sessionid = $_SESSION["session_id"];

        $sql = "SELECT * FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $query = $conn->query($sql);

        $myState = stateMalaysia();

        if ($query->num_rows < "1") {
            header("Location: /");
            exit;
        }

        $_SESSION["fname"] = $_POST["fname"];
        $_SESSION["lname"] = $_POST["lname"];
        $_SESSION["add_1"] = $_POST["add_1"];
        $_SESSION["add_2"] = $_POST["add_2"];
        $_SESSION["city"] = $_POST["city"];
        $_SESSION["state"] = $_POST["state"];
        $_SESSION["postcode"] = $_POST["postcode"];
        $_SESSION["ophone"] = $_POST["ophone"];
        $_SESSION["oemail"] = $_POST["oemail"];
        $_SESSION["remark"] = $_POST["remark"];

        $states = $_SESSION["state"];

        $sqls = "SELECT * FROM `state` WHERE country_id='$country' AND `name`='$states'";
        $querys = $conn->query($sqls);
        $rowState = $querys->fetch_array();

        $shippingZone = $rowState["shipping_zone"];

        $sqlpc = "SELECT * FROM `postage_cost` WHERE country_id='$country' AND shipping_zone='$shippingZone'";
        $querypc = $conn->query($sqlpc);
        $rowpc = $querypc->fetch_array();

        $sqlw = "SELECT SUM(`quantity` * `weight`) AS tWeight FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $queryw = $conn->query($sqlw);
        $roww = $queryw->fetch_assoc();

        $totalWeightKG = $roww["tWeight"] / 1000;



        $postage = calculatePostage($totalWeightKG, $rowpc["first_kilo"], $rowpc["next_kilo"]);

        require_once __DIR__ . '/../../view/ecom/e-checkout-keya88.php';
    }

    public function checkoutUpdate()
    {
        $conn = getDbConnection();
        $sessionid = $_SESSION["session_id"];
        $dateNow = dateNow();

        if (isset($_POST['cartid']) && is_array($_POST['cartid']) && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            $productid = $_POST['productid'];
            $cartIds = $_POST['cartid'];
            $quantities = $_POST['quantity'];

            $errorUpdate = "";
            $x = 1;
            foreach ($productid as $pid) {

                $dataProduct = GetProductDetails($pid);

                if ($dataProduct["max_purchase"] < $quantities[$x]) {
                    $errorUpdate .= "The maximum purchase quantity for product " . $dataProduct["name"] . " is " . $dataProduct["max_purchase"] . ". You requested " . $quantities[$x] . ".<br>";
                } else if ($quantities[$x] < 1) {
                    $conn->query("UPDATE `cart` SET `deleted_at`= NOW(), `status`='4' WHERE `id`='" . $cartIds[$x] . "'");
                } else {
                    $dataCart = $conn->query("SELECT * FROM `cart` WHERE `id`='" . $cartIds[$x] . "'");
                    $roeCart = $dataCart->fetch_array();
                    $tweight = $quantities[$x] * $dataProduct["weight"];
                    $conn->query("UPDATE `cart` SET `quantity`='" . $quantities[$x] . "', `total_weight`='" . $tweight . "' WHERE `id`='" . $cartIds[$x] . "'");
                }



                $x++;
            }

            $addCartAll = "UPDATE `cart` SET `updated_at`='$dateNow' WHERE `session_id`='$sessionid' AND `deleted_at` IS NULL AND `status` IN(0,1)";
            $conn->query($addCartAll);

            if (!empty($errorUpdate)) {
                $_SESSION["error_update"] = "Error note:<br>" . $errorUpdate;
            }
        }

        // Redirect back to the checkout page
        header("Location: /checkout");
    }

    public function proceedPaymentSenangPay()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $data = dataCountry($country);

        $dateNow = dateNow();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        //echo $_SESSION["session_id"] . "<br><br>";

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

        $getCart = $conn->query("SELECT * FROM `cart` WHERE `session_id`='" . $_SESSION["session_id"] . "' AND `deleted_at` IS NULL AND `status` IN(0,1)");

        if ($getCart->num_rows < 1) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        if ((!isset(($_SESSION["fname"])) || empty($_SESSION["fname"])) && (!isset(($_SESSION["lname"])) || empty($_SESSION["lname"])) && (!isset(($_SESSION["add_1"])) || empty($_SESSION["add_1"])) && (!isset(($_SESSION["postcode"])) || empty($_SESSION["postcode"]))) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }
        //$getCart = $conn->query("SELECT * FROM `cart` WHERE `session_id`='" . $_SESSION["session_id"] . "'");


        $softDeleteLock = $conn->query("UPDATE `cart_lock_senangpay` SET `deleted_at`= '$dateNow' WHERE `session_id`='" . $_SESSION["session_id"] . "'");

        $x = 1;
        $product_var_id = "";
        $qty = 0;
        $tPrice = 0;
        foreach ($getCart as $cartItem) {
            $dataProduct = GetProductDetails($cartItem["p_id"]);
            //echo "Cart Item: " . $dataProduct["name"] . " | Quantity: " . $cartItem["quantity"] . "<br>";
            if ($x == 1) {
                $product_var_id .= "[" . $cartItem["p_id"] . "]";
            } else {
                $product_var_id .= ",[" . $cartItem["p_id"] . "]";
            }
            $qty += $cartItem["quantity"];
            $tPrice += $cartItem["price"] * $cartItem["quantity"];



            $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `cart_id`='" . $cartItem["id"] . "' AND `session_id`='" . $_SESSION["session_id"] . "'");
            if ($getCartLock->num_rows > 0) {
                $dataLock = $getCartLock->fetch_array();
                $conn->query("UPDATE `cart_lock_senangpay` SET `quantity`='" . $cartItem["quantity"] . "', `price`='" . $cartItem["price"] . "', `weight`='" . $cartItem["weight"] . "', `total_weight`='" . $cartItem["total_weight"] . "', `updated_at`='$dateNow', `locked_date`= '$dateNow', `deleted_at`= NULL WHERE `id`='" . $dataLock["id"] . "'");
            } else {
                $conn->query("INSERT INTO `cart_lock_senangpay`(`id`, `cart_id`, `session_id`, `p_id`, `pv_id`, `quantity`, `price`, `weight`, `total_weight`, `currency_sign`, `country_id`, `created_at`, `updated_at`, `locked_date`, `deleted_at`, `status`) VALUES (NULL,'" . $cartItem["id"] . "','" . $_SESSION["session_id"] . "','" . $cartItem["p_id"] . "','" . $cartItem["pv_id"] . "','" . $cartItem["quantity"] . "','" . $cartItem["price"] . "','" . $cartItem["weight"] . "','" . $cartItem["total_weight"] . "','" . $cartItem["currency_sign"] . "','" . $cartItem["country_id"] . "','$dateNow','$dateNow', '$dateNow', NULL, '0')");
            }
            $x++;
        }

        $pendingOrder = $conn->query("INSERT INTO customer_orders (
                `id`, 
                `session_id`, 
                `order_to`, 
                `product_var_id`, 
                `total_qty`, 
                `total_price`, 
                `postage_cost`, 
                `currency_sign`, 
                `country_id`, 
                `country`, 
                `state`, 
                `city`, 
                `postcode`, 
                `address_2`, 
                `address_1`, 
                `customer_name`, 
                `customer_name_last`, 
                `customer_phone`, 
                `customer_email`, 
                `status`, 
                `payment_channel`, 
                `payment_code`, 
                `payment_url`, 
                `ship_channel`, 
                `courier_service`, 
                `awb_number`, 
                `tracking_url`, 
                `created_at`, 
                `updated_at`, 
                `deleted_at`, 
                `remark_comment`, 
                `tracking_milestone`, 
                `to_myr_rate`, 
                `myr_value_include_postage`, 
                `myr_value_without_postage`, 
                `printed_awb`
            ) VALUES(
                NULL, 
                '" . $_SESSION["session_id"] . "', 
                '1', 
                '$product_var_id', 
                '$qty', 
                '$tPrice', 
                '" . $_SESSION["postageCharge"] . "', 
                '" . $data["sign"] . "', 
                '$country', 
                '" . $data["name"] . "', 
                '" . $_SESSION["state"] . "', 
                '" . $_SESSION["city"] . "', 
                '" . $_SESSION["postcode"] . "', 
                '" . $_SESSION["add_2"] . "', 
                '" . $_SESSION["add_1"] . "', 
                '" . $_SESSION["fname"] . "', 
                '" . $_SESSION["lname"] . "', 
                '" . $_SESSION["ophone"] . "', 
                '" . $_SESSION["oemail"] . "', 
                '0', 
                'nill', 
                'nill', 
                'nill', 
                'Doorstep Delivery', 
                '', 
                '', 
                '',
                '$dateNow', 
                '$dateNow', 
                NULL, 
                '" . $_SESSION["remark"] . "', 
                '', 
                '1', 
                '" . $_SESSION["subTotal"] . "', 
                '$tPrice', 
                '0'
            )");

        if ($pendingOrder) {
            $pendingOrderId = $conn->insert_id;

            $order_id = 'ORDERID_' . $pendingOrderId;
            $detail   = 'Payment for ' . $order_id;
            $amount   = number_format($_SESSION["subTotal"], 2, '.', ''); // Example amount
            $name     = $_SESSION["fname"] . ' ' . $_SESSION["lname"];
            $email    = $_SESSION["oemail"];
            $phone    = $_SESSION["ophone"];

            $hash = hash_hmac('sha256', $secret_key . urldecode($detail) . urldecode($amount) . urldecode($order_id), $secret_key);

            $postData = [
                'detail'   => $detail,
                'amount'   => $amount,
                'order_id' => $order_id,
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'hash'     => $hash
            ];

            $payment_url = $urlsubmit . "payment/$merchant_id";
            unset($_SESSION["session_id"]);
            ?>
                <html>
                    <head>
                        <title>senangPay</title>
                    </head>
                    <body onload="document.order.submit()"></body>
                        <form name="order" method="post" action="<?php echo $payment_url; ?>">
                            <input type="hidden" name="detail" value="<?php echo $detail; ?>">
                            <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <input type="hidden" name="name" value="<?php echo $name; ?>">
                            <input type="hidden" name="email" value="<?php echo $email; ?>">
                            <input type="hidden" name="phone" value="<?php echo $phone; ?>">
                            <input type="hidden" name="hash" value="<?php echo $hash; ?>">
                        </form>
                    </body>
                </html>
            <?php

            // //echo "Payment URL: " . $payment_url . "<br><br>";




            // $ch = curl_init();

            // curl_setopt($ch, CURLOPT_URL, $payment_url);
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            // $response = curl_exec($ch);

            // curl_close($ch);

            // unset($_SESSION["session_id"]);

            // echo $response;
        }

        //echo "<br>Product Var ID: " . $product_var_id . " (" . $qty . ")<br><br>";






        // In a real implementation, you would redirect or handle the payment process here
    }

    public function callBackSenangPay()
    {
        $conn = getDbConnection();
        $dateNow = dateNow();
        $domainURL = getMainUrl();

        // Retrieve callback data
        $status_id = $_POST['status_id'];
        $order_id   = $_POST['order_id'];
        $transaction_id   = $_POST['transaction_id'];
        $msg   = $_POST['msg'];
        $hash   = $_POST['hash'];
        $payment_type   = $_POST['payment_type'];
        $channel   = $_POST['channel'];

        $order_ids = str_replace('ORDERID_', '', $order_id);
        $detail   = 'Payment for Order ' . $order_ids;

        $dataOrder = getOrder(1, $order_ids);

        // Fetch SenangPay settings
        $getSenangPay = $conn->query("SELECT * FROM `senangpay_api` ORDER BY id DESC LIMIT 1");
        $dataSenangPay = $getSenangPay->fetch_array();

        if ($dataSenangPay["type"] == 'sandbox') {
            $secret_key  = $dataSenangPay["secret_key"];
        } else {
            $secret_key  = $dataSenangPay["pro_secret_key"];
        }

        // Verify hash
        $calculated_hash = hash_hmac('sha256', $secret_key . $detail . $dataOrder["myr_value_include_postage"] . $status_id, $secret_key);

        //if ($hash === $calculated_hash) {
        // Hash is valid, process the payment status
        if ($status_id == '1') {

            $conn->query("UPDATE `customer_orders` SET `status`='1', `payment_channel`='$payment_type', `payment_code`='$transaction_id', `payment_url`='$transaction_id', `updated_at`='$dateNow' WHERE `id`='" . $order_ids . "'");
            $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `session_id`='" . $dataOrder["session_id"] . "' AND deleted_at IS NULL");
            foreach ($getCartLock as $cartLockItem) {

                $conn->query("UPDATE `cart` SET `updated_at`='$dateNow', `deleted_at`=NULL, `status`='1' WHERE `id`='" . $cartLockItem["cart_id"] . "'");
            }

            $hashOrder = hash("sha256", $order_ids . "_" . $dataOrder['customer_name'] . "_" . $dateNow);
            $conn->query("INSERT INTO order_details(order_id, hash_code, created_at) VALUES ('$order_ids','$hashOrder','$dateNow')");

            $emailData = [
                'CustomerName' => $dataOrder['customer_name'],
                'OrderID'      => $order_ids,
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
                $mail->AltBody = 'Thank you for your order #' . $order_ids . '. View: ' . $domainURL . 'order-details/' . $hashOrder;

                $mail->send();
            } catch (Exception $e) {
                error_log("❌ Mail error: {$mail->ErrorInfo}");
            }
            // Payment successful
            // Update order status in the database
            // Example: Update customer_orders set status to 'paid' where order_id matches
            echo "OK";
        } else {
            $conn->query("UPDATE `customer_orders` SET `status`='10' WHERE `id`='" . $order_ids . "'");

            // Payment failed or pending
            // Handle accordingly
            echo "OK";
        }
        // } else {
        //     // Invalid hash, possible tampering
        //     // Log this incident for further investigation
        //     echo "OK";
        // }
    }

    public function thankYou()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $data = dataCountry($country);

        $dateNow = dateNow();

        

        if (!isset($_GET["order_id"]) || empty($_GET["order_id"])) {
            header("Location: /");
            exit();
        }

        $order_ids = str_replace('ORDERID_', '', $_GET["order_id"]);

        if (isset($_GET["status_id"]) && $_GET["status_id"] == "1") {
            $getOrder = $conn->query("SELECT * FROM `order_details` WHERE `order_id`='" . $order_ids . "'")->fetch_assoc();
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-keya88.php';
            exit();
        } else {
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-failed-keya88.php';
            exit();
        }
    }

    public function proceedPaymentBayarcash()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $conn = getDbConnection();
        $data = dataCountry($country);
        $dateNow = dateNow();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $getCart = $conn->query("SELECT * FROM `cart` WHERE `session_id`='" . $_SESSION["session_id"] . "' AND `deleted_at` IS NULL AND `status` IN(0,1)");

        if ($getCart->num_rows < 1) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        if ((!isset($_SESSION["fname"]) || empty($_SESSION["fname"])) && (!isset($_SESSION["lname"]) || empty($_SESSION["lname"])) && (!isset($_SESSION["add_1"]) || empty($_SESSION["add_1"])) && (!isset($_SESSION["postcode"]) || empty($_SESSION["postcode"]))) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $softDeleteLock = $conn->query("UPDATE `cart_lock_senangpay` SET `deleted_at`= '$dateNow' WHERE `session_id`='" . $_SESSION["session_id"] . "'");

        $x = 1;
        $product_var_id = "";
        $qty = 0;
        $tPrice = 0;
        foreach ($getCart as $cartItem) {
            $dataProduct = GetProductDetails($cartItem["p_id"]);
            if ($x == 1) {
                $product_var_id .= "[" . $cartItem["p_id"] . "]";
            } else {
                $product_var_id .= ",[" . $cartItem["p_id"] . "]";
            }
            $qty += $cartItem["quantity"];
            $tPrice += $cartItem["price"] * $cartItem["quantity"];

            $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `cart_id`='" . $cartItem["id"] . "' AND `session_id`='" . $_SESSION["session_id"] . "'");
            if ($getCartLock->num_rows > 0) {
                $dataLock = $getCartLock->fetch_array();
                $conn->query("UPDATE `cart_lock_senangpay` SET `quantity`='" . $cartItem["quantity"] . "', `price`='" . $cartItem["price"] . "', `weight`='" . $cartItem["weight"] . "', `total_weight`='" . $cartItem["total_weight"] . "', `updated_at`='$dateNow', `locked_date`= '$dateNow', `deleted_at`= NULL WHERE `id`='" . $dataLock["id"] . "'");
            } else {
                $conn->query("INSERT INTO `cart_lock_senangpay`(`id`, `cart_id`, `session_id`, `p_id`, `pv_id`, `quantity`, `price`, `weight`, `total_weight`, `currency_sign`, `country_id`, `created_at`, `updated_at`, `locked_date`, `deleted_at`, `status`) VALUES (NULL,'" . $cartItem["id"] . "','" . $_SESSION["session_id"] . "','" . $cartItem["p_id"] . "','" . $cartItem["pv_id"] . "','" . $cartItem["quantity"] . "','" . $cartItem["price"] . "','" . $cartItem["weight"] . "','" . $cartItem["total_weight"] . "','" . $cartItem["currency_sign"] . "','" . $cartItem["country_id"] . "','$dateNow','$dateNow', '$dateNow', NULL, '0')");
            }
            $x++;
        }

        $pendingOrder = $conn->query("INSERT INTO customer_orders (
                `id`,
                `session_id`,
                `order_to`,
                `product_var_id`,
                `total_qty`,
                `total_price`,
                `postage_cost`,
                `currency_sign`,
                `country_id`,
                `country`,
                `state`,
                `city`,
                `postcode`,
                `address_2`,
                `address_1`,
                `customer_name`,
                `customer_name_last`,
                `customer_phone`,
                `customer_email`,
                `status`,
                `payment_channel`,
                `payment_code`,
                `payment_url`,
                `ship_channel`,
                `courier_service`,
                `awb_number`,
                `tracking_url`,
                `created_at`,
                `updated_at`,
                `deleted_at`,
                `remark_comment`,
                `tracking_milestone`,
                `to_myr_rate`,
                `myr_value_include_postage`,
                `myr_value_without_postage`,
                `printed_awb`
            ) VALUES(
                NULL,
                '" . $_SESSION["session_id"] . "',
                '1',
                '$product_var_id',
                '$qty',
                '$tPrice',
                '" . $_SESSION["postageCharge"] . "',
                '" . $data["sign"] . "',
                '$country',
                '" . $data["name"] . "',
                '" . $_SESSION["state"] . "',
                '" . $_SESSION["city"] . "',
                '" . $_SESSION["postcode"] . "',
                '" . $_SESSION["add_2"] . "',
                '" . $_SESSION["add_1"] . "',
                '" . $_SESSION["fname"] . "',
                '" . $_SESSION["lname"] . "',
                '" . $_SESSION["ophone"] . "',
                '" . $_SESSION["oemail"] . "',
                '0',
                'bayarcash',
                'nill',
                'nill',
                'Doorstep Delivery',
                '',
                '',
                '',
                '$dateNow',
                '$dateNow',
                NULL,
                '" . $_SESSION["remark"] . "',
                '',
                '1',
                '" . $_SESSION["subTotal"] . "',
                '$tPrice',
                '0'
            )");

        if ($pendingOrder) {
            $pendingOrderId = $conn->insert_id;
            $orderNumber = 'ORDERID_' . $pendingOrderId;
            $amount = number_format($_SESSION["subTotal"], 2, '.', '');
            $name = $_SESSION["fname"] . ' ' . $_SESSION["lname"];
            $email = $_SESSION["oemail"];
            $phone = $_SESSION["ophone"];

            $bayarcash = new \Bayarcash();
            $bayarcash->loadConfig();

            $callbackUrl = $domainURL . 'bayarcash-callback';
            $returnUrl = $domainURL . 'bayarcash-thank-you?order_id=' . $orderNumber;

            $intent = $bayarcash->createPaymentIntent($orderNumber, $amount, $name, $email, $phone, $callbackUrl, $returnUrl);

            $paymentIntentId = $intent['id'] ?? null;
            $conn->query("INSERT INTO `bayarcash_transactions` (`order_id`, `order_number`, `payment_intent_id`, `amount`, `status`, `created_at`, `updated_at`) VALUES ('$pendingOrderId', '$orderNumber', '$paymentIntentId', '$amount', '0', '$dateNow', '$dateNow')");

            $redirectUrl = $bayarcash->getRedirectUrl($intent);
            unset($_SESSION["session_id"]);

            if ($redirectUrl) {
                header("Location: " . $redirectUrl);
                exit();
            } else {
                header("Location: " . $domainURL . "checkout");
                exit();
            }
        }
    }

    public function callBackBayarcash()
    {
        $conn = getDbConnection();
        $dateNow = dateNow();
        $domainURL = getMainUrl();

        $bayarcash = new \Bayarcash();
        $bayarcash->loadConfig();

        $result = $bayarcash->processCallback($_POST);

        $orderNumber = $result['order_number'] ?? '';
        $order_ids = str_replace('ORDERID_', '', $orderNumber);
        $callbackPayload = json_encode($_POST);

        if ($result['success'] && $result['is_paid']) {
            $dataOrder = getOrder(1, $order_ids);

            $conn->query("UPDATE `customer_orders` SET `status`='1', `payment_channel`='bayarcash', `payment_code`='" . $result['transaction_id'] . "', `payment_url`='" . $result['transaction_id'] . "', `updated_at`='$dateNow' WHERE `id`='" . $order_ids . "'");

            $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `session_id`='" . $dataOrder["session_id"] . "' AND deleted_at IS NULL");
            foreach ($getCartLock as $cartLockItem) {
                $conn->query("UPDATE `cart` SET `updated_at`='$dateNow', `deleted_at`=NULL, `status`='1' WHERE `id`='" . $cartLockItem["cart_id"] . "'");
            }

            $hashOrder = hash("sha256", $order_ids . "_" . $dataOrder['customer_name'] . "_" . $dateNow);
            $conn->query("INSERT INTO order_details(order_id, hash_code, created_at) VALUES ('$order_ids','$hashOrder','$dateNow')");

            $emailData = [
                'CustomerName' => $dataOrder['customer_name'],
                'OrderID'      => $order_ids,
                'OrderLink'    => $domainURL . "order-details/" . $hashOrder,
            ];

            $emailHTML = getEmailTemplate($emailData);

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
                $mail->AltBody = 'Thank you for your order #' . $order_ids . '. View: ' . $domainURL . 'order-details/' . $hashOrder;

                $mail->send();
            } catch (Exception $e) {
                error_log("Mail error: {$mail->ErrorInfo}");
            }

            $conn->query("UPDATE `bayarcash_transactions` SET `status`='3', `transaction_id`='" . $result['transaction_id'] . "', `payment_channel`='" . $result['payment_channel'] . "', `callback_payload`='" . $conn->real_escape_string($callbackPayload) . "', `updated_at`='$dateNow' WHERE `order_number`='$orderNumber'");

            echo "OK";
        } else {
            $conn->query("UPDATE `customer_orders` SET `status`='10' WHERE `id`='" . $order_ids . "'");
            $conn->query("UPDATE `bayarcash_transactions` SET `status`='2', `callback_payload`='" . $conn->real_escape_string($callbackPayload) . "', `updated_at`='$dateNow' WHERE `order_number`='$orderNumber'");

            echo "OK";
        }
    }

    public function proceedCOD()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $conn = getDbConnection();
        $data = dataCountry($country);
        $dateNow = dateNow();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $getCart = $conn->query("SELECT * FROM `cart` WHERE `session_id`='" . $_SESSION["session_id"] . "' AND `deleted_at` IS NULL AND `status` IN(0,1)");

        if ($getCart->num_rows < 1) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        if ((!isset($_SESSION["fname"]) || empty($_SESSION["fname"])) && (!isset($_SESSION["lname"]) || empty($_SESSION["lname"])) && (!isset($_SESSION["add_1"]) || empty($_SESSION["add_1"])) && (!isset($_SESSION["postcode"]) || empty($_SESSION["postcode"]))) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $softDeleteLock = $conn->query("UPDATE `cart_lock_senangpay` SET `deleted_at`= '$dateNow' WHERE `session_id`='" . $_SESSION["session_id"] . "'");

        $x = 1;
        $product_var_id = "";
        $qty = 0;
        $tPrice = 0;
        foreach ($getCart as $cartItem) {
            $dataProduct = GetProductDetails($cartItem["p_id"]);
            if ($x == 1) {
                $product_var_id .= "[" . $cartItem["p_id"] . "]";
            } else {
                $product_var_id .= ",[" . $cartItem["p_id"] . "]";
            }
            $qty += $cartItem["quantity"];
            $tPrice += $cartItem["price"] * $cartItem["quantity"];

            $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `cart_id`='" . $cartItem["id"] . "' AND `session_id`='" . $_SESSION["session_id"] . "'");
            if ($getCartLock->num_rows > 0) {
                $dataLock = $getCartLock->fetch_array();
                $conn->query("UPDATE `cart_lock_senangpay` SET `quantity`='" . $cartItem["quantity"] . "', `price`='" . $cartItem["price"] . "', `weight`='" . $cartItem["weight"] . "', `total_weight`='" . $cartItem["total_weight"] . "', `updated_at`='$dateNow', `locked_date`= '$dateNow', `deleted_at`= NULL WHERE `id`='" . $dataLock["id"] . "'");
            } else {
                $conn->query("INSERT INTO `cart_lock_senangpay`(`id`, `cart_id`, `session_id`, `p_id`, `pv_id`, `quantity`, `price`, `weight`, `total_weight`, `currency_sign`, `country_id`, `created_at`, `updated_at`, `locked_date`, `deleted_at`, `status`) VALUES (NULL,'" . $cartItem["id"] . "','" . $_SESSION["session_id"] . "','" . $cartItem["p_id"] . "','" . $cartItem["pv_id"] . "','" . $cartItem["quantity"] . "','" . $cartItem["price"] . "','" . $cartItem["weight"] . "','" . $cartItem["total_weight"] . "','" . $cartItem["currency_sign"] . "','" . $cartItem["country_id"] . "','$dateNow','$dateNow', '$dateNow', NULL, '0')");
            }
            $x++;
        }

        $codFee = isset($_SESSION["codFee"]) ? $_SESSION["codFee"] : 0;
        $subTotal = $_SESSION["subTotal"] + $codFee;

        $codOrder = $conn->query("INSERT INTO customer_orders (
                `id`,
                `session_id`,
                `order_to`,
                `product_var_id`,
                `total_qty`,
                `total_price`,
                `postage_cost`,
                `currency_sign`,
                `country_id`,
                `country`,
                `state`,
                `city`,
                `postcode`,
                `address_2`,
                `address_1`,
                `customer_name`,
                `customer_name_last`,
                `customer_phone`,
                `customer_email`,
                `status`,
                `payment_channel`,
                `payment_code`,
                `payment_url`,
                `ship_channel`,
                `courier_service`,
                `awb_number`,
                `tracking_url`,
                `created_at`,
                `updated_at`,
                `deleted_at`,
                `remark_comment`,
                `tracking_milestone`,
                `to_myr_rate`,
                `myr_value_include_postage`,
                `myr_value_without_postage`,
                `printed_awb`
            ) VALUES(
                NULL,
                '" . $_SESSION["session_id"] . "',
                '1',
                '$product_var_id',
                '$qty',
                '$tPrice',
                '" . $_SESSION["postageCharge"] . "',
                '" . $data["sign"] . "',
                '$country',
                '" . $data["name"] . "',
                '" . $_SESSION["state"] . "',
                '" . $_SESSION["city"] . "',
                '" . $_SESSION["postcode"] . "',
                '" . $_SESSION["add_2"] . "',
                '" . $_SESSION["add_1"] . "',
                '" . $_SESSION["fname"] . "',
                '" . $_SESSION["lname"] . "',
                '" . $_SESSION["ophone"] . "',
                '" . $_SESSION["oemail"] . "',
                '1',
                'COD',
                'COD',
                'COD',
                'COD',
                '',
                '',
                '',
                '$dateNow',
                '$dateNow',
                NULL,
                '" . $_SESSION["remark"] . "',
                '',
                '1',
                '$subTotal',
                '$tPrice',
                '0'
            )");

        if ($codOrder) {
            $codOrderId = $conn->insert_id;

            $getCartLock = $conn->query("SELECT * FROM `cart_lock_senangpay` WHERE `session_id`='" . $_SESSION["session_id"] . "' AND deleted_at IS NULL");
            foreach ($getCartLock as $cartLockItem) {
                $conn->query("UPDATE `cart` SET `updated_at`='$dateNow', `deleted_at`=NULL, `status`='1' WHERE `id`='" . $cartLockItem["cart_id"] . "'");
            }

            $hashOrder = hash("sha256", $codOrderId . "_" . $_SESSION["fname"] . "_" . $dateNow);
            $conn->query("INSERT INTO order_details(order_id, hash_code, created_at) VALUES ('$codOrderId','$hashOrder','$dateNow')");

            $emailData = [
                'CustomerName' => $_SESSION["fname"],
                'OrderID'      => $codOrderId,
                'OrderLink'    => $domainURL . "order-details/" . $hashOrder,
            ];

            $emailHTML = getEmailTemplate($emailData);

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
                $mail->addAddress($_SESSION["oemail"], $_SESSION["fname"]);
                $mail->isHTML(true);
                $mail->Subject = 'Your Order Confirmation (COD) - Rozeyana';
                $mail->Body    = $emailHTML;
                $mail->AltBody = 'Thank you for your order #' . $codOrderId . '. View: ' . $domainURL . 'order-details/' . $hashOrder;

                $mail->send();
            } catch (Exception $e) {
                error_log("Mail error: {$mail->ErrorInfo}");
            }

            unset($_SESSION["session_id"]);

            $isCOD = true;
            $getOrder = $conn->query("SELECT * FROM `order_details` WHERE `order_id`='" . $codOrderId . "'")->fetch_assoc();
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-keya88.php';
            exit();
        }
    }

    public function thankYouBayarcash()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $conn = getDbConnection();
        $data = dataCountry($country);

        if (!isset($_GET["order_id"]) || empty($_GET["order_id"])) {
            header("Location: /");
            exit();
        }

        $order_ids = str_replace('ORDERID_', '', $_GET["order_id"]);
        $dataOrder = getOrder(1, $order_ids);

        if ($dataOrder && $dataOrder["status"] == "1") {
            $getOrder = $conn->query("SELECT * FROM `order_details` WHERE `order_id`='" . $order_ids . "'")->fetch_assoc();
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-keya88.php';
            exit();
        } else {
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-failed-keya88.php';
            exit();
        }
    }
}
