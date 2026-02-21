<?php

namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../email-order.php';
require_once __DIR__ . '/../../model/Bayarcash.php';
require_once __DIR__ . '/../../model/Cart.php';
require_once __DIR__ . '/../../model/Order.php';
require_once __DIR__ . '/../../model/OrderDetail.php';
require_once __DIR__ . '/../../model/CartLock.php';
require_once __DIR__ . '/../../model/SenangPaySetting.php';
require_once __DIR__ . '/../../model/StripeSetting.php';
require_once __DIR__ . '/../../model/StateSetting.php';
require_once __DIR__ . '/../../model/PostageCost.php';
require_once __DIR__ . '/../../model/BayarcashTransaction.php';

require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CheckoutController
{
    private $conn;
    private $cartModel;
    private $orderModel;
    private $orderDetailModel;
    private $cartLockModel;
    private $senangPayModel;
    private $stripeModel;
    private $stateModel;
    private $postageCostModel;
    private $bayarcashTxModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->cartModel = new \Cart($this->conn);
        $this->orderModel = new \Order($this->conn);
        $this->orderDetailModel = new \OrderDetail($this->conn);
        $this->cartLockModel = new \CartLock($this->conn);
        $this->senangPayModel = new \SenangPaySetting($this->conn);
        $this->stripeModel = new \StripeSetting($this->conn);
        $this->stateModel = new \StateSetting($this->conn);
        $this->postageCostModel = new \PostageCost($this->conn);
        $this->bayarcashTxModel = new \BayarcashTransaction($this->conn);
    }

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
        $conn = $this->conn;
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

        $stripesRow = $this->stripeModel->getSettings();

        $newArrival = newProduct(8);

        $sessionid = $_SESSION["session_id"];

        $cartItems = $this->cartModel->getActiveBySession($sessionid);
        $query = $cartItems;

        $myState = stateMalaysia();

        if (empty($cartItems)) {
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
        $conn = $this->conn;
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

        $stripesRow = $this->stripeModel->getSettings();

        $newArrival = newProduct(8);

        $sessionid = $_SESSION["session_id"];

        $cartItems = $this->cartModel->getActiveBySession($sessionid);
        $query = $cartItems;

        $myState = stateMalaysia();

        if (empty($cartItems)) {
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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Checkout";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $stripesRow = $this->stripeModel->getSettings();

        $newArrival = newProduct(8);

        $sessionid = $_SESSION["session_id"];

        $cartItems = $this->cartModel->getActiveBySession($sessionid);
        $query = $cartItems;

        $myState = stateMalaysia();

        if (empty($cartItems)) {
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

        $rowState = $this->stateModel->findByCountryAndName($country, $states);

        $shippingZone = $rowState["shipping_zone"];

        $rowpc = $this->postageCostModel->findByCountryZone($country, $shippingZone);

        $totalWeightGram = $this->cartModel->getTotalWeight($sessionid);
        $totalWeightKG = $totalWeightGram / 1000;

        $postage = calculatePostage($totalWeightKG, $rowpc["first_kilo"], $rowpc["next_kilo"]);

        require_once __DIR__ . '/../../view/ecom/e-checkout-keya88.php';
    }

    public function checkoutUpdate()
    {
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
                    $this->cartModel->softDeleteById($cartIds[$x], $dateNow);
                } else {
                    $roeCart = $this->cartModel->getById($cartIds[$x]);
                    $tweight = $quantities[$x] * $dataProduct["weight"];
                    $this->cartModel->updateById($cartIds[$x], [
                        'quantity' => $quantities[$x],
                        'total_weight' => $tweight
                    ]);
                }

                $x++;
            }

            $this->cartModel->touchSession($sessionid, $dateNow);

            if (!empty($errorUpdate)) {
                $_SESSION["error_update"] = "Error note:<br>" . $errorUpdate;
            }
        }

        header("Location: /checkout");
    }

    private function buildOrderData($sessionId, $data, $country, $productVarId, $qty, $tPrice, $paymentChannel, $status, $subTotal)
    {
        $dateNow = dateNow();
        return [
            'session_id'             => $sessionId,
            'order_to'               => '1',
            'product_var_id'         => $productVarId,
            'total_qty'              => $qty,
            'total_price'            => $tPrice,
            'postage_cost'           => $_SESSION["postageCharge"],
            'currency_sign'          => $data["sign"],
            'country_id'             => $country,
            'country'                => $data["name"],
            'state'                  => $_SESSION["state"],
            'city'                   => $_SESSION["city"],
            'postcode'               => $_SESSION["postcode"],
            'address_2'              => $_SESSION["add_2"],
            'address_1'              => $_SESSION["add_1"],
            'customer_name'          => $_SESSION["fname"],
            'customer_name_last'     => $_SESSION["lname"],
            'customer_phone'         => $_SESSION["ophone"],
            'customer_email'         => $_SESSION["oemail"],
            'status'                 => $status,
            'payment_channel'        => $paymentChannel,
            'payment_code'           => $paymentChannel === 'COD' ? 'COD' : 'nill',
            'payment_url'            => $paymentChannel === 'COD' ? 'COD' : 'nill',
            'ship_channel'           => $paymentChannel === 'COD' ? 'COD' : 'Doorstep Delivery',
            'courier_service'        => '',
            'awb_number'             => '',
            'tracking_url'           => '',
            'created_at'             => $dateNow,
            'updated_at'             => $dateNow,
            'remark_comment'         => $_SESSION["remark"],
            'tracking_milestone'     => '',
            'to_myr_rate'            => '1',
            'myr_value_include_postage' => $subTotal,
            'myr_value_without_postage' => $tPrice,
            'printed_awb'            => '0',
        ];
    }

    private function lockCartItems($sessionId, $cartItems, $dateNow)
    {
        $this->cartLockModel->softDeleteBySession($sessionId, $dateNow);

        $x = 1;
        $product_var_id = "";
        $qty = 0;
        $tPrice = 0;
        foreach ($cartItems as $cartItem) {
            $dataProduct = GetProductDetails($cartItem["p_id"]);
            if ($x == 1) {
                $product_var_id .= "[" . $cartItem["pv_id"] . "]";
            } else {
                $product_var_id .= ",[" . $cartItem["pv_id"] . "]";
            }
            $qty += $cartItem["quantity"];
            $tPrice += $cartItem["price"] * $cartItem["quantity"];

            $existingLock = $this->cartLockModel->findByCartAndSession($cartItem["id"], $sessionId);
            if ($existingLock) {
                $this->cartLockModel->updateLock($existingLock["id"], [
                    'quantity'     => $cartItem["quantity"],
                    'price'        => $cartItem["price"],
                    'weight'       => $cartItem["weight"],
                    'total_weight' => $cartItem["total_weight"],
                    'updated_at'   => $dateNow,
                    'locked_date'  => $dateNow,
                ]);
            } else {
                $this->cartLockModel->createLock([
                    'cart_id'       => $cartItem["id"],
                    'session_id'    => $sessionId,
                    'p_id'          => $cartItem["p_id"],
                    'pv_id'         => $cartItem["pv_id"],
                    'quantity'      => $cartItem["quantity"],
                    'price'         => $cartItem["price"],
                    'weight'        => $cartItem["weight"],
                    'total_weight'  => $cartItem["total_weight"],
                    'currency_sign' => $cartItem["currency_sign"],
                    'country_id'    => $cartItem["country_id"],
                    'created_at'    => $dateNow,
                    'updated_at'    => $dateNow,
                    'locked_date'   => $dateNow,
                    'status'        => '0',
                ]);
            }
            $x++;
        }

        return ['product_var_id' => $product_var_id, 'qty' => $qty, 'tPrice' => $tPrice];
    }

    private function completeOrderAfterPayment($orderId, $customerName, $customerEmail, $dateNow, $domainURL)
    {
        $lockedItems = $this->cartLockModel->getActiveBySession(getOrder(1, $orderId)["session_id"]);
        foreach ($lockedItems as $cartLockItem) {
            $this->cartModel->restoreAndMarkPaid($cartLockItem["cart_id"], $dateNow);
        }

        $hashOrder = hash("sha256", $orderId . "_" . $customerName . "_" . $dateNow);
        $this->orderDetailModel->createDetail($orderId, $hashOrder, $dateNow);

        $emailData = [
            'CustomerName' => $customerName,
            'OrderID'      => $orderId,
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
            $mail->addAddress($customerEmail, $customerName);
            $mail->isHTML(true);
            $mail->Subject = 'Your Order Confirmation - Rozeyana';
            $mail->Body    = $emailHTML;
            $mail->AltBody = 'Thank you for your order #' . $orderId . '. View: ' . $domainURL . 'order-details/' . $hashOrder;

            $mail->send();
        } catch (Exception $e) {
            error_log("Mail error: {$mail->ErrorInfo}");
        }

        return $hashOrder;
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
        $data = dataCountry($country);
        $dateNow = dateNow();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $credentials = $this->senangPayModel->getCredentials();
        $merchant_id = $credentials['merchant_id'];
        $secret_key  = $credentials['secret_key'];
        $urlsubmit   = $credentials['url'];

        $cartItems = $this->cartModel->getActiveBySession($_SESSION["session_id"]);

        if (empty($cartItems)) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        if ((!isset(($_SESSION["fname"])) || empty($_SESSION["fname"])) && (!isset(($_SESSION["lname"])) || empty($_SESSION["lname"])) && (!isset(($_SESSION["add_1"])) || empty($_SESSION["add_1"])) && (!isset(($_SESSION["postcode"])) || empty($_SESSION["postcode"]))) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $lockResult = $this->lockCartItems($_SESSION["session_id"], $cartItems, $dateNow);
        $product_var_id = $lockResult['product_var_id'];
        $qty = $lockResult['qty'];
        $tPrice = $lockResult['tPrice'];

        $orderData = $this->buildOrderData($_SESSION["session_id"], $data, $country, $product_var_id, $qty, $tPrice, 'nill', '0', $_SESSION["subTotal"]);
        $pendingOrderId = $this->orderModel->createOrder($orderData);

        if ($pendingOrderId) {
            $order_id = 'ORDERID_' . $pendingOrderId;
            $detail   = 'Payment for ' . $order_id;
            $amount   = number_format($_SESSION["subTotal"], 2, '.', '');
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
        }
    }

    public function callBackSenangPay()
    {
        $dateNow = dateNow();
        $domainURL = getMainUrl();

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

        $settings = $this->senangPayModel->getSettings();
        if ($settings["type"] == 'sandbox') {
            $secret_key = $settings["secret_key"];
        } else {
            $secret_key = $settings["pro_secret_key"];
        }

        if ($status_id == '1') {
            $this->orderModel->update((int)$order_ids, [
                'status'          => '1',
                'payment_channel' => $payment_type,
                'payment_code'    => $transaction_id,
                'payment_url'     => $transaction_id,
                'updated_at'      => $dateNow,
            ]);

            $this->completeOrderAfterPayment($order_ids, $dataOrder['customer_name'], $dataOrder['customer_email'], $dateNow, $domainURL);

            echo "OK";
        } else {
            $this->orderModel->updateStatus((int)$order_ids, 10, $dateNow);
            echo "OK";
        }
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
        $conn = $this->conn;

        $data = dataCountry($country);

        $dateNow = dateNow();

        if (!isset($_GET["order_id"]) || empty($_GET["order_id"])) {
            header("Location: /");
            exit();
        }

        $order_ids = str_replace('ORDERID_', '', $_GET["order_id"]);

        if (isset($_GET["status_id"]) && $_GET["status_id"] == "1") {
            $getOrder = $this->orderDetailModel->findByOrderId($order_ids);
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
        $data = dataCountry($country);
        $dateNow = dateNow();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $cartItems = $this->cartModel->getActiveBySession($_SESSION["session_id"]);

        if (empty($cartItems)) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        if ((!isset($_SESSION["fname"]) || empty($_SESSION["fname"])) && (!isset($_SESSION["lname"]) || empty($_SESSION["lname"])) && (!isset($_SESSION["add_1"]) || empty($_SESSION["add_1"])) && (!isset($_SESSION["postcode"]) || empty($_SESSION["postcode"]))) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $lockResult = $this->lockCartItems($_SESSION["session_id"], $cartItems, $dateNow);
        $product_var_id = $lockResult['product_var_id'];
        $qty = $lockResult['qty'];
        $tPrice = $lockResult['tPrice'];

        $orderData = $this->buildOrderData($_SESSION["session_id"], $data, $country, $product_var_id, $qty, $tPrice, 'bayarcash', '0', $_SESSION["subTotal"]);
        $pendingOrderId = $this->orderModel->createOrder($orderData);

        if ($pendingOrderId) {
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
            $this->bayarcashTxModel->createTransaction([
                'order_id'           => $pendingOrderId,
                'order_number'       => $orderNumber,
                'payment_intent_id'  => $paymentIntentId,
                'amount'             => $amount,
                'status'             => '0',
                'created_at'         => $dateNow,
                'updated_at'         => $dateNow,
            ]);

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

            $this->orderModel->update((int)$order_ids, [
                'status'          => '1',
                'payment_channel' => 'bayarcash',
                'payment_code'    => $result['transaction_id'],
                'payment_url'     => $result['transaction_id'],
                'updated_at'      => $dateNow,
            ]);

            $this->completeOrderAfterPayment($order_ids, $dataOrder['customer_name'], $dataOrder['customer_email'], $dateNow, $domainURL);

            $this->bayarcashTxModel->updateByOrderNumber($orderNumber, [
                'status'           => '3',
                'transaction_id'   => $result['transaction_id'],
                'payment_channel'  => $result['payment_channel'],
                'callback_payload' => $callbackPayload,
                'updated_at'       => $dateNow,
            ]);

            echo "OK";
        } else {
            $this->orderModel->updateStatus((int)$order_ids, 10, $dateNow);
            $this->bayarcashTxModel->updateByOrderNumber($orderNumber, [
                'status'           => '2',
                'callback_payload' => $callbackPayload,
                'updated_at'       => $dateNow,
            ]);

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
        $data = dataCountry($country);
        $dateNow = dateNow();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $cartItems = $this->cartModel->getActiveBySession($_SESSION["session_id"]);

        if (empty($cartItems)) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        if ((!isset($_SESSION["fname"]) || empty($_SESSION["fname"])) && (!isset($_SESSION["lname"]) || empty($_SESSION["lname"])) && (!isset($_SESSION["add_1"]) || empty($_SESSION["add_1"])) && (!isset($_SESSION["postcode"]) || empty($_SESSION["postcode"]))) {
            header("Location: " . $domainURL . "checkout");
            exit();
        }

        $lockResult = $this->lockCartItems($_SESSION["session_id"], $cartItems, $dateNow);
        $product_var_id = $lockResult['product_var_id'];
        $qty = $lockResult['qty'];
        $tPrice = $lockResult['tPrice'];

        $codFee = isset($_SESSION["codFee"]) ? $_SESSION["codFee"] : 0;
        $subTotal = $_SESSION["subTotal"] + $codFee;

        $orderData = $this->buildOrderData($_SESSION["session_id"], $data, $country, $product_var_id, $qty, $tPrice, 'COD', '1', $subTotal);
        $codOrderId = $this->orderModel->createOrder($orderData);

        if ($codOrderId) {
            $lockedItems = $this->cartLockModel->getActiveBySession($_SESSION["session_id"]);
            foreach ($lockedItems as $cartLockItem) {
                $this->cartModel->restoreAndMarkPaid($cartLockItem["cart_id"], $dateNow);
            }

            $hashOrder = hash("sha256", $codOrderId . "_" . $_SESSION["fname"] . "_" . $dateNow);
            $this->orderDetailModel->createDetail($codOrderId, $hashOrder, $dateNow);

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
            $getOrder = $this->orderDetailModel->findByOrderId($codOrderId);
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
        $conn = $this->conn;
        $data = dataCountry($country);

        if (!isset($_GET["order_id"]) || empty($_GET["order_id"])) {
            header("Location: /");
            exit();
        }

        $order_ids = str_replace('ORDERID_', '', $_GET["order_id"]);
        $dataOrder = getOrder(1, $order_ids);

        if ($dataOrder && $dataOrder["status"] == "1") {
            $getOrder = $this->orderDetailModel->findByOrderId($order_ids);
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-keya88.php';
            exit();
        } else {
            require_once __DIR__ . '/../../view/ecom/e-senangpay-thank-you-failed-keya88.php';
            exit();
        }
    }
}
