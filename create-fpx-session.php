<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

if (isset($_COOKIE['country'])) {
    $country = $_COOKIE['country'];
}

$data = dataCountry($country);
$cid = $data["id"];
$cname = $data["name"];
$csign = $data["sign"];

$dateNow = dateNow();
$conn = getDbConnection();

$stripe = "SELECT * FROM `stripe_setting` WHERE id='1'";

$stripes = $conn->query($stripe);

$stripesRow = $stripes->fetch_array();


\Stripe\Stripe::setApiKey($stripesRow["secret_key"]); // Replace with your secret key




$sessionid = $_SESSION["session_id"];

$sql = "SELECT SUM(quantity * price) AS amtTotal FROM `cart` WHERE `session_id`='$sessionid' AND `deleted_at` IS NULL AND `status` IN(0,1)";
$query = $conn->query($sql);

$queries = $query->fetch_assoc();

$sqlw = "SELECT SUM(`quantity` * `weight`) AS tWeight FROM `cart` WHERE `session_id`='$sessionid' AND `deleted_at` IS NULL AND `status` IN(0,1)";
$queryw = $conn->query($sqlw);
$roww = $queryw->fetch_assoc();

$totalWeightKG = $roww["tWeight"] / 1000;


header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

$amount = intval($queries['amtTotal'] * 100); // in cents (e.g. 10.00 MYR = 1000)
$fname = isset($input['fname']) ? $conn->real_escape_string($input['fname']) : '';
$lname = isset($input['lname']) ? $conn->real_escape_string($input['lname']) : '';
$add_1 = isset($input['add_1']) ? $conn->real_escape_string($input['add_1']) : '';
$add_2 = isset($input['add_2']) ? $conn->real_escape_string($input['add_2']) : '';
$city = $input['city'] ?? '';
$state = $input['state'] ?? '';
$postcode = $input['postcode'] ?? '';
$ophone = $input['ophone'] ?? '';
$oemail = $input['oemail'] ?? '';
$remark = isset($input['remark']) ? $conn->real_escape_string($input['remark']) : '';
$method = $input['method'] ?? 'card'; // default to card
$amt = $queries['amtTotal'];

$sqls = "SELECT * FROM `state` WHERE country_id='$country' AND `name`='$state'";
$querys = $conn->query($sqls);
$rowState = $querys->fetch_array();

$shippingZone = $rowState["shipping_zone"];

$sqlpc = "SELECT * FROM `postage_cost` WHERE country_id='$country' AND shipping_zone='$shippingZone'";
$querypc = $conn->query($sqlpc);
$rowpc = $querypc->fetch_array();

// if($csign == "MYR"){
//     if($ophone[0] == "0"){
//         $validatePhone = "6".$oemail;
//     }else if($ophone[0] == ""){
//         $validatePhone = "6".$oemail;
//     }
// }

$validatePhone = validatePhoneNumber($ophone);
$valPhone = $validatePhone['international'];

$postage = calculatePostage($totalWeightKG, $rowpc["first_kilo"], $rowpc["next_kilo"]);
$validateTemp = "SELECT * FROM order_temp_data WHERE `session_id`='$sessionid'";

$amountPay = intval(($queries['amtTotal'] + $postage) * 100);

$queryTemp = $conn->query($validateTemp);

if ($queryTemp->num_rows < "1") {

    $updateCartTime = "UPDATE `cart` SET `updated_at`='$dateNow' WHERE `session_id`='$sessionid' AND `deleted_at` IS NULL AND `status`='0'";


    $addData = "INSERT INTO `order_temp_data`(`id`, `session_id`, `first_name`, `last_name`, `add_1`, `add_2`, `city`, `state`, `postcode`, `country_name`, `country_id`, `phone`, `email`, `remark`, `created_at`, `updated_at`, `deleted_at`, `method`, `currency_sign`, `amount`, `shipping_cost`, `status`) VALUES (NULL,'$sessionid','$fname','$lname','$add_1','$add_2','$city ','$state','$postcode','$cname','$cid','$ophone','$oemail','$remark','$dateNow','$dateNow',NULL,'$method','$csign','$amt','$postage','0')";
} else {
    $updateCartTime = "UPDATE `cart` SET `updated_at`='$dateNow' WHERE `session_id`='$sessionid' AND `deleted_at` IS NULL AND `status`='0'";

    $addData = "
        UPDATE `order_temp_data` 
        SET 
            `first_name` = '$fname',
            `last_name` = '$lname',
            `add_1` = '$add_1',
            `add_2` = '$add_2',
            `city` = '$city',
            `state` = '$state',
            `postcode` = '$postcode',
            `country_name` = '$cname',
            `country_id` = '$cid',
            `phone` = '$ophone',
            `email` = '$oemail',
            `remark` = '$remark',
            `method` = '$method',
            `currency_sign` = '$csign',
            `amount` = '$amt',
            `shipping_cost` = '$postage',
            `updated_at` = '$dateNow'
        WHERE `session_id` = '$sessionid'
    ";
}

$addQuery = $conn->query($addData);


if ($addQuery) {
    $deleteLock = $conn->query("DELETE FROM cart_lock WHERE session_id='$sessionid'");
    if ($deleteLock) {

        $sqlLocked = "
    INSERT INTO cart_lock (
        cart_id,
        session_id,
        p_id,
        pv_id,
        quantity,
        price,
        weight,
        total_weight,
        currency_sign,
        country_id,
        created_at,
        locked_date,
        updated_at,
        status
    )
    SELECT
        id,
        session_id,
        p_id,
        pv_id,
        quantity,
        price,
        weight,
        total_weight,
        currency_sign,
        country_id,
        created_at,
        '$dateNow' AS locked_date,
        updated_at,
        status
    FROM cart AS new
    WHERE new.session_id = '$sessionid' AND new.deleted_at IS NULL AND new.status = '0'
    ON DUPLICATE KEY UPDATE
        session_id = new.session_id,
        p_id = new.p_id,
        pv_id = new.pv_id,
        quantity = new.quantity,
        price = new.price,
        weight = new.weight,
        total_weight = new.total_weight,
        currency_sign = new.currency_sign,
        country_id = new.country_id,
        created_at = new.created_at,
        locked_date = '$dateNow',
        updated_at = new.updated_at,
        status = new.status
";

        $conn->query($sqlLocked);
    }
}




try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => [$method], // 'fpx' or 'card'
        'line_items' => [[
            'price_data' => [
                'currency' => strtolower($data["sign"]),
                'unit_amount' => $amountPay,
                'product_data' => [
                    'name' => 'Rozyana Order',
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $domainURL . 'thank-you.php?session_id=' . $_SESSION["session_id"],
        'cancel_url' => $domainURL . 'checkout.php?cancelled=true',
    ]);

    echo json_encode(['id' => $session->id]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
