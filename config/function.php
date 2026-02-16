<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getMailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = '127.0.0.1';
    $mail->Port = 25;
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;

    $mail->setFrom('noreply@rozeyana.com', 'Rozeyana');

    return $mail;
}

function getMailerBrevo()
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';  // Brevo SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = '889d41001@smtp-brevo.com'; // usually your Brevo SMTP email
    $mail->Password   = 'xsmtpsib-XXXXXXXXXXXXXXXXXXXX';      // API key for SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SMTPS
    $mail->Port       = 587; // TLS port

    $mail->setFrom('noreply@rozeyana.com', 'Rozeyana');

    return $mail;
}

function csrf_token()
{
    if (!isset($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_token'];
}

// SELECT `id`, `user_id`, `created_at`, `updated_at`, `deleted_at`, `description`, `table_name`, `activities` FROM `activities` WHERE 1
function activity($userid, $description, $table, $activity)
{
    $conn = getDbConnection();
    $now = dateNow();

    $addActivity = $conn->query("INSERT INTO `activities`(`id`, `user_id`, `created_at`, `updated_at`, `deleted_at`, `description`, `table_name`, `activities`) VALUES (NULL,'$userid','$now','$now',NULL,'$description','$table','$activity')");
}

function roleVerify($url, $id)
{
    $urlHash = md5($url);
    return cache_remember("role:{$id}:{$urlHash}", 300, function() use ($url, $id) {
        $conn = getDbConnection();
        $query = $conn->query("SELECT * FROM role_access WHERE page_url='$url' AND allowed_user LIKE '%[$id]%'");

        if ($query->num_rows > 0) {
            $allowed = 1;
        } else {
            $allowed = 0;
        }

        return $allowed;
    });
}

function calculatePostage($weightKg, $baseRate, $additionalRate)
{
    // $baseRate = 6.50;
    // $additionalRate = 3.00;

    if ($weightKg <= 1) {
        return $baseRate;
    } else {
        // Subtract the first kg
        $extraWeight = $weightKg - 1;

        // Round up any extra weight to the next whole kg
        $extraKg = ceil($extraWeight);

        return $baseRate + ($extraKg * $additionalRate);
    }
}

function stateMalaysia()
{
    return cache_remember('states:malaysia', 3600, function() {
        $conn = getDbConnection();
        $sql = "SELECT * FROM `state` WHERE country_id='1' AND deleted_at IS NULL";
        $result = $conn->query($sql);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    });
}

function itemSold($id)
{
    $conn = getDbConnection();
    $sql = "SELECT SUM(`quantity`) AS itemSold FROM `cart` WHERE deleted_at IS NULL AND `status`='1'";

    $query = $conn->query($sql);

    $count = $query->fetch_assoc();

    if ($count["itemSold"] >= 1) {
        $sold = $count["itemSold"];
    } else {
        $sold = 0;
    }

    return $sold;
}

function validatePhone($ophone)
{
    // Remove all non-digit characters
    $number = preg_replace('/\D/', '', $ophone);

    // Malaysia: starts with 0 and 10–11 digits
    if (preg_match('/^0\d{8,9}$/', $number)) {
        return '6' . substr($number, 1); // 0123456789 → 60123456789
    }

    // Malaysia: already in 60 format
    if (preg_match('/^60\d{8,9}$/', $number)) {
        return $number;
    }

    // Singapore: starts with 8 or 9 and 8 digits
    if (preg_match('/^[89]\d{7}$/', $number)) {
        return '65' . $number; // 91234567 → 6591234567
    }

    // Singapore: already in 65 format
    if (preg_match('/^65[89]\d{7}$/', $number)) {
        return $number;
    }

    // Brunei: starts with 2, 7, or 8 and 7 digits
    if (preg_match('/^[278]\d{6}$/', $number)) {
        return '673' . $number; // 7123456 → 6737123456
    }

    // Brunei: already in 673 format
    if (preg_match('/^673[278]\d{6}$/', $number)) {
        return $number;
    }

    // Indonesia: starts with 08 and 10–13 digits
    if (preg_match('/^08\d{8,11}$/', $number)) {
        return '62' . substr($number, 1); // 08123456789 → 628123456789
    }

    // Indonesia: already in 62 format
    if (preg_match('/^62\d{8,11}$/', $number)) {
        return $number;
    }

    // Invalid format
    return false;

    //return $number;
}

function validatePhoneNumber($phone)
{
    // Remove spaces, dashes, brackets
    $phone = preg_replace('/[\s\-()]/', '', $phone);

    // If phone starts with 60, 62, 65, 673 but without +, add it
    if (preg_match('/^(60|62|65|673)/', $phone)) {
        $phone = '+' . $phone;
    }

    // International format checks
    if (preg_match('/^\+60(1[0-9]{7,8})$/', $phone, $m)) {
        return [
            'valid' => true,
            'country' => 'Malaysia',
            'local' => '0' . $m[1],
            'international' => $phone
        ];
    } elseif (preg_match('/^\+65([689][0-9]{7})$/', $phone, $m)) {
        return [
            'valid' => true,
            'country' => 'Singapore',
            'local' => $m[1],
            'international' => $phone
        ];
    } elseif (preg_match('/^\+673([2-9][0-9]{6})$/', $phone, $m)) {
        return [
            'valid' => true,
            'country' => 'Brunei',
            'local' => $m[1],
            'international' => $phone
        ];
    } elseif (preg_match('/^\+62([2-9][0-9]{7,11})$/', $phone, $m)) {
        return [
            'valid' => true,
            'country' => 'Indonesia',
            'local' => $m[1],
            'international' => $phone
        ];
    }

    // Local format rules
    $rules = [
        'Malaysia' => [
            'prefixes' => ['01'],
            'lengths'  => [10, 11],
            'code'     => '+60'
        ],
        'Singapore' => [
            'prefixes' => ['6', '8', '9'],
            'lengths'  => [8],
            'code'     => '+65'
        ],
        'Brunei' => [
            'prefixes' => ['2', '3', '7', '8', '9'],
            'lengths'  => [7],
            'code'     => '+673'
        ],
        'Indonesia' => [
            'prefixes' => ['8'],
            'lengths'  => [9, 10, 11, 12],
            'code'     => '+62'
        ]
    ];

    foreach ($rules as $country => $rule) {
        foreach ($rule['prefixes'] as $prefix) {
            if (strpos($phone, $prefix) === 0 && in_array(strlen($phone), $rule['lengths'])) {
                $international = $rule['code'] . ltrim($phone, '0');
                return [
                    'valid' => true,
                    'country' => $country,
                    'local' => $phone,
                    'international' => $international
                ];
            }
        }
    }

    return [
        'valid' => false,
        'message' => 'Invalid or unsupported phone number format.'
    ];
}

function cartCount()
{
    $sessionid = $_SESSION["session_id"] ?? '';
    return cache_remember("cart:qty:{$sessionid}", 300, function() use ($sessionid) {
        $conn = getDbConnection();
        $sql = "SELECT SUM(`quantity`) AS cartQTY FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $query = $conn->query($sql);
        $count = $query->fetch_assoc();

        if ($count["cartQTY"] >= "1") {
            $theCount = $count["cartQTY"];
        } else {
            $theCount = 0;
        }

        $data = array(
            "count" => $theCount
        );
        return ($data);
    });
}

function cartList()
{
    $conn = getDbConnection();
    $sessionid = $_SESSION["session_id"];
    $sql = "SELECT * FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
    $query = $conn->query($sql);


    return $query;
}

//ecom
function getCategoryDetails($id)
{
    return cache_remember("category:{$id}", 600, function() use ($id) {
        $conn = getDbConnection();
        $sql = "SELECT * FROM categories WHERE id='$id'";
        $query = $conn->query($sql);
        $row = $query->fetch_array();
        $data = array(
            "name" => $row["name"],
            "image" => $row["image"],
            "description" => $row["description"]
        );
        return ($data);
    });
}

function getBrandDetails($id)
{
    return cache_remember("brand:{$id}", 600, function() use ($id) {
        $conn = getDbConnection();
        $sql = "SELECT * FROM brands WHERE id='$id'";
        $query = $conn->query($sql);
        $row = $query->fetch_array();
        $data = array(
            "name" => $row["name"],
            "image" => $row["image"],
            "description" => $row["description"]
        );
        return ($data);
    });
}

function stockBalanceIndividual($id)
{
    $conn = getDbConnection();

    $sql = "
    SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.slug,
                p.description,
                p.type,
                p.category_id,
                p.brand_id,
                p.price_capital,

                pv.id AS variant_id,
                pv.sku,
                pv.price_retail,
                pv.price_sale,
                pv.stock AS variant_stock,
                pv.image AS variant_image,
                pv.max_purchase,

                (
                    SELECT pi.image 
                    FROM product_image pi 
                    WHERE pi.product_id = p.id 
                    ORDER BY pi.id ASC 
                    LIMIT 1
                ) AS product_image,

                IFNULL(SUM(sc.stock_in), 0) AS total_stock_in,
                IFNULL(SUM(sc.stock_out), 0) AS total_stock_out,

                IFNULL((
                    SELECT SUM(c.quantity) 
                    FROM cart c 
                    WHERE c.pv_id = pv.id AND c.status IN (0,1)
                ), 0) AS stock_reserved,

                (IFNULL(SUM(sc.stock_in), 0) - IFNULL(SUM(sc.stock_out), 0) - 
                    IFNULL((
                        SELECT SUM(c.quantity) 
                        FROM cart c 
                        WHERE c.pv_id = pv.id AND c.status IN (0,1)
                    ), 0)
                ) AS physical_stock

            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN stock_control sc ON pv.id = sc.pv_id

            WHERE p.id = '$id' AND p.deleted_at IS NULL AND pv.deleted_at IS NULL

            GROUP BY pv.id
            ORDER BY p.id, pv.id
    ";

    $result = $conn->query($sql);

    $row = $result->fetch_assoc();

    $data = array(
        "sku" => $row["sku"],
        "total_stock_in" => $row["total_stock_in"],
        "total_stock_out" => $row["total_stock_out"],
        "physical_stock" => $row["physical_stock"],
        "max_purchase" => $row["max_purchase"]
    );
    return ($data);
}

function getAllProductImage($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM product_image WHERE product_id='$id'";
    $query = $conn->query($sql);

    return $query;
}

function getListCategoryBrand($type)
{
    $conn = getDbConnection();
    if ($type == "1") {
        $sql = "SELECT * FROM brands WHERE deleted_at IS NULL";
    } else if ($type == "2") {
        $sql = "SELECT * FROM categories WHERE deleted_at IS NULL";
    }

    $query = $conn->query($sql);

    return $query;
}
function getListCategoryBrand2($type)
{
    $conn = getDbConnection();
    if ($type == "1") {
        $sql = "SELECT * FROM brands WHERE deleted_at IS NULL";
    } else if ($type == "2") {
        $sql = "SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY RAND() LIMIT 8";
    }

    $query = $conn->query($sql);

    return $query;
}

function getProductImageSingle($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM product_image WHERE product_id='$id' ORDER BY id ASC LIMIT 1";
    $query = $conn->query($sql);
    $row = $query->fetch_array();

    $data = array(
        "image" => $row["image"]
    );
    return ($data);
}

function getPriceOnCountry($cid, $pid)
{
    $conn = getDbConnection();

    $sqlPrice = "SELECT * FROM list_country_product_price WHERE country_id='$cid' AND product_id='$pid'";
    $queryPrice = $conn->query($sqlPrice);
    $rowPrice = $queryPrice->fetch_array();

    $data = array(
        "market" => $rowPrice["market_price"],
        "sale" => $rowPrice["sale_price"]
    );
    return ($data);
}

function countUsedCategory($id)
{
    $conn = getDbConnection();
    $sql = "SELECT * FROM products WHERE category_id='$id' AND deleted_at IS NULL";

    $query = $conn->query($sql);

    if ($query->num_rows >= "1") {
        $used = $query->num_rows;
    } else {
        $used = "0";
    }

    $data = array(
        "used" => $used
    );
    return ($data);
}

function newProduct($limit)
{
    $conn = getDbConnection();

    $sql = "SELECT *  
            FROM `products` 
            WHERE `status` = '1' AND `deleted_at` IS NULL 
            ORDER BY `created_at` DESC 
            LIMIT $limit;";

    $query = $conn->query($sql);

    return $query;
}

//ecom

function dhlDetails()
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM dhl WHERE id='1'";
    $query = $conn->query($sql);
    $row = $query->fetch_array();

    $data = array(
        "production_sandbox" => $row["production_sandbox"],
        "clientid" => $row["clientid"],
        "password" => $row["password"],
        "format" => $row["format"],
        "url" => $row["url"],
        "clientid_test" => $row["clientid_test"],
        "password_test" => $row["password_test"],
        "format_test" => $row["format_test"],
        "url_test" => $row["url_test"]
    );
    return ($data);
}

function tokenDHLOnSaveSetting($cid, $pass, $type, $date)
{
    $conn = getDbConnection();
    $dhl = dhlDetails();
    $curl = curl_init();
    $dateNow = dateNow();

    if ($type == "2") {
        $url = $dhl["url_test"];
    } else if ($type == "1") {
        $url = $dhl["url"];
    }

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $url . 'rest/v1/OAuth/AccessToken?clientId=' . $cid . '&password=' . $pass . '&returnFormat=json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(),
        )
    );

    $response = curl_exec($curl);

    curl_close($curl);

    $result = json_decode($response, TRUE);

    echo "<pre>";
    print_r($result);
    echo "</pre>";
    $token = $result["accessTokenResponse"]["token"];
    $token_type = $result["accessTokenResponse"]["token_type"];
    $expires_in_seconds = $result["accessTokenResponse"]["expires_in_seconds"];

    $timestamp = strtotime($date);
    $timestamps = $timestamp + $expires_in_seconds;

    $daten = new DateTime();
    $daten->setTimestamp($timestamps);
    $daten->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));

    $expiring = $daten->format('Y-m-d H:i:s'); // Output: 2025-07-04 10:30:00

    if ($type == "2") {
        $addToken = "INSERT INTO `dhl_token_test`(`id`, `token`, `token_type`, `expires_in_seconds`, `created_at`, `expired_at`) VALUES (NULL,'$token','$token_type','$expires_in_seconds','$dateNow','$expiring')";
    } else if ($type == "1") {
        $addToken = "INSERT INTO `dhl_token`(`id`, `token`, `token_type`, `expires_in_seconds`, `created_at`, `expired_at`) VALUES (NULL,'$token','$token_type','$expires_in_seconds','$dateNow','$expiring')";
    }

    $query = $conn->query($addToken);
}

function dhlToken($type)
{
    $conn = getDbConnection();
    if ($type == "2") {
        $sql = "SELECT * FROM dhl_token_test ORDER BY id DESC LIMIT 1";
    } else if ($type == "1") {
        $sql = "SELECT * FROM dhl_token_test ORDER BY id DESC LIMIT 1";
    }
    $query = $conn->query($sql);

    $row = $query->fetch_array();

    $data = array(
        "token" => $row["token"],
        "token_type" => $row["token_type"],
        "expires_in_seconds" => $row["expires_in_seconds"],
        "created_at" => $row["created_at"],
        "expired_at" => $row["expired_at"]
    );
    return ($data);
}

function dhlCreateShipping($id)
{
    $conn = getDbConnection();
    $dhl = dhlDetails();
    $dateNow = dateNow();
    $dt = new DateTime($dateNow, new DateTimeZone('Asia/Kuala_Lumpur'));

    // Output in ISO 8601 format (same as date("c"))
    $dateMessage = $dt->format('c');

    $dt->modify('+1 day');

    $datePickup = $dt->format('c');

    $order = getOrder(1, $id);

    $formatted = str_pad($id, 8, '0', STR_PAD_LEFT);

    if ($dhl["production_sandbox"] == "2") {
        $url = $dhl["url_test"];
        $tokenDHL = dhlToken(2);
        $prefix = "MYXXX";
        $pickupAccountId = "5999999940";
        $shipmentID = $prefix . $formatted . "-web";
    } else if ($dhl["production_sandbox"] == "1") {
        $url = $dhl["url"];
        $tokenDHL = dhlToken(1);
        $prefix = "MYNVU";
        $pickupAccountId = "9000000416";
        $shipmentID = $prefix . "ROZZ" . $formatted;
    }

    $accessToken = $tokenDHL["token"];

    $value = floatval($order["myr_value_include_postage"]);

    $payload = [
        "manifestRequest" => [
            "hdr" => [
                "messageType" => "SHIPMENT",
                "messageDateTime" => $dateMessage,
                // ISO 8601 format, e.g. date("c")
                "accessToken" => $accessToken,
                "messageVersion" => "1.0",
                "messageLanguage" => "en"
            ],
            "bd" => [
                "pickupAccountId" => $pickupAccountId,
                "soldToAccountId" => $pickupAccountId,
                "pickupDateTime" => $datePickup,
                // ISO 8601, future datetime
                "handoverMethod" => 1,
                "pickupAddress" => [
                    "name" => "ROZZ BEAUTY LEGACY",
                    "address1" => "B-G-48, SAVANNA LIFESTYLE RETAIL",
                    "address2" => "Jalan Southville 2, Southville City",
                    "city" => "Dengkil",
                    "state" => "Selangor",
                    "district" => "Dengkil",
                    "country" => "MY",
                    "postCode" => "43800",
                    "phone" => "60389123807",
                    "email" => "wafazz.tech@gmail.com"
                ],
                "shipperAddress" => [
                    "name" => "ROZZ BEAUTY LEGACY",
                    "address1" => "A-G-30, SAVANNA LIFESTYLE RETAIL",
                    "address2" => "Jalan Southville 2, Southville City",
                    "city" => "Dengkil",
                    "state" => "Selangor",
                    "district" => "Dengkil",
                    "country" => "MY",
                    "postCode" => "43800",
                    "phone" => "60389123807",
                    "email" => "wafazz.tech@gmail.com"
                ],
                "shipmentItems" => [
                    [
                        "consigneeAddress" => [
                            "name" => $order["customer_name"],
                            "address1" => $order["address_1"],
                            "address2" => $order["address_2"],
                            "city" => $order["city"],
                            "state" => $order["state"],
                            "district" => $order["city"],
                            // DHL requires district field
                            "country" => "MY",
                            "postCode" => $order["postcode"],
                            "phone" => $order["customer_phone"],
                            "email" => $order["customer_email"]
                        ],
                        "isRoutingInfoRequired" => "Y",
                        "shipmentID" => $shipmentID,
                        "packageDesc" => "AWB for Order " . $id,
                        "totalWeight" => 10,
                        "totalWeightUOM" => "g",
                        "dimensionUOM" => "CM",
                        "height" => 10.00,
                        "length" => 30.00,
                        "width" => 20.00,
                        "productCode" => "PDO",
                        "codValue" => null,
                        "insuranceValue" => null,
                        "totalValue" => round((float) $order["myr_value_include_postage"], 2),
                        "currency" => $order["currency_sign"],
                        "remarks" => "AWB for Order " . $id,
                        "billingReference1" => "Selangor Bank",
                        "billingReference2" => "Malaysia Bank",
                        "isMult" => "FALSE",
                        "deliveryOption" => "c"
                    ]
                ]
            ]
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url . 'rest/v3/Shipment',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    $responses = json_decode($response, TRUE);

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        echo 'cURL error: ' . curl_error($curl);
    } elseif ($httpCode != 200) {
        echo "HTTP Status Code: $httpCode";
        print_r($responses); // may contain DHL error message
    } else {
        echo "<pre>";
        print_r($responses);
        echo "</pre>";
    }

    $deliveryConfirmationNo = $responses["manifestResponse"]["bd"]["shipmentItems"][0]["deliveryConfirmationNo"];
    $shipmentIDs = $responses["manifestResponse"]["bd"]["shipmentItems"][0]["shipmentID"];
    $deliveryDepotCode = $responses["manifestResponse"]["bd"]["shipmentItems"][0]["deliveryDepotCode"];
    $primarySortCode = $responses["manifestResponse"]["bd"]["shipmentItems"][0]["primarySortCode"];
    $secondarySortCode = $responses["manifestResponse"]["bd"]["shipmentItems"][0]["secondarySortCode"];

    $sql = "INSERT INTO `dhl_ship`(`id`, `order_id`, `deliveryConfirmationNo`, `deliveryDepotCode`, `primarySortCode`, `secondarySortCode`, `shipmentID`) VALUES (NULL,'$id','$deliveryConfirmationNo','$deliveryDepotCode','$primarySortCode','$secondarySortCode','$shipmentIDs')";

    $putQuery = $conn->query($sql);

    // echo number_format($order["myr_value_include_postage"], 2, '.', '');

    // if (curl_errno($curl)) {
    //     echo 'cURL error: ' . curl_error($curl);
    // } else {
    //     echo "<pre>";
    //     print_r($responses);
    //     echo "</pre>";
    // }

    curl_close($curl);

    if (!empty($deliveryConfirmationNo)) {
        $data = array(
            "id" => $id,
            "deliveryConfirmationNo" => $deliveryConfirmationNo,
            "shipmentIDs" => $shipmentIDs,
            "deliveryDepotCode" => $deliveryDepotCode,
            "primarySortCode" => $primarySortCode,
            "secondarySortCode" => $secondarySortCode,
            "message" => ""
        );
    } else {

        $errMess = $responses["manifestResponse"]["bd"]["shipmentItems"][0]["responseStatus"]["message"] . " " . $responses["manifestResponse"]["bd"]["shipmentItems"][0]["responseStatus"]["messageDetails"][0]["messageDetail"];
        $data = array(
            "id" => "",
            "deliveryConfirmationNo" => "",
            "shipmentIDs" => "",
            "deliveryDepotCode" => "",
            "primarySortCode" => "",
            "secondarySortCode" => "",
            "message" => $errMess
        );
    }


    return ($data);
}

function totalSales()
{
    $conn = getDbConnection();

    $sql = "
        SELECT SUM(myr_value_include_postage) AS total_myr
        FROM customer_orders
        WHERE status IN (1, 2, 3, 4)
        AND deleted_at IS NULL
    ";

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $totalMYR = $row['total_myr'];

        if (is_null($row['total_myr'])) {
            echo 0.00;
        } else {
            if ($totalMYR >= 0.01) {
                echo $totalMYR;
            } else {
                echo 0.00;
            }
        }
    } else {
        echo "Error or no matching records.";
    }

    // 5. Close connection
    $conn->close();
}

function totalProduct()
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM products WHERE deleted_at IS NULL";

    $result = $conn->query($sql);

    echo $result->num_rows;

    $conn->close();
}

function totalOrder()
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM customer_orders WHERE deleted_at IS NULL";

    $result = $conn->query($sql);

    echo $result->num_rows;

    $conn->close();
}

function totalOrderReturn()
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM customer_orders WHERE `status`='5' AND deleted_at IS NULL";

    $result = $conn->query($sql);

    echo $result->num_rows;

    $conn->close();
}

function dateFromat1($date)
{
    $newdate = new \DateTime($date);

    $formattedDate = $newdate->format('j F, Y h:i A');

    echo $formattedDate;

    // Format to: Day MonthName, Year Hour:Minute AM/PM
}

function verify_csrf($token)
{
    return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
}

function getUserIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'];
}

function getUsedCategory($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM products WHERE category_id = '$id' AND deleted_at IS NULL";

    $query = $conn->query($sql);

    if ($query->num_rows < 1) {
        $count = 0;
    } else {
        $count = $query->num_rows;
    }

    $dataC = array(
        "count" => $count
    );
    return ($dataC);
}

function getUsedBrand($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM products WHERE brand_id = '$id' AND deleted_at IS NULL";

    $query = $conn->query($sql);

    if ($query->num_rows < 1) {
        $count = 0;
    } else {
        $count = $query->num_rows;
    }

    $dataC = array(
        "count" => $count
    );
    return ($dataC);
}

function getCategoryBrand($id, $type)
{
    $conn = getDbConnection();
    if ($type == "1") {
        $sql = "SELECT * FROM categories WHERE id='$id'";
    } else if ($type == "2") {
        $sql = "SELECT * FROM brands WHERE id='$id'";
    }

    $query = $conn->query($sql);

    $row = $query->fetch_array();

    $dataCB = array(
        "id" => $row["id"],
        "name" => $row["name"],
        "slug" => $row["slug"],
        "image" => $row["image"],
        "description" => $row["description"],
        "deleted_at" => $row["deleted_at"]
    );
    return ($dataCB);
}

function getFullUrl()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
        || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    $host = $_SERVER['HTTP_HOST']; // e.g. www.example.com
    $requestUri = $_SERVER['REQUEST_URI']; // e.g. /folder/page.php?param=value

    return $protocol . $host . $requestUri;
}

function getCountryByIP($ip)
{
    $url = "https://ipwho.is/{$ip}";
    $response = @file_get_contents($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['country'] ?? null;
        }
    }

    return null;
}

function login($email, $password)
{
    $ip = getUserIP();
    if (!rate_limit("ratelimit:login:{$ip}", 5, 900)) {
        return "Too many login attempts. Please try again in 15 minutes.";
    }

    $conn = getDbConnection();

    $stmt = $conn->prepare("SELECT id, email, password, f_name, l_name, role, status FROM member_hq WHERE email = ? AND deleted_at IS NULL LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($user = $result->fetch_assoc()) {
        // Email found, now verify password
        $hashedPassword = hash('sha256', $password);

        if ($hashedPassword !== $user['password']) {
            return "Invalid email or password.";
        }

        // Password matched, now check status
        switch ((int) $user['status']) {
            case 1:
                $_SESSION['user'] = (object) $user;
                $addActivity = activity($_SESSION['user']->id, "Login to the system.", "-", "login");
                header("Location: dashboard");
                exit;
            case 0:
                return "Inactive account.";
            case 2:
                return "Banned account.";
            default:
                return "Unknown account status.";
        }
    }

    // Email not found
    return "Account does not exist.";
}

function is_login()
{
    return isset($_SESSION['user'])
        && isset($_SESSION['user']->id)
        && $_SESSION['user']->status == 1;
}

function getPriceCountryMP($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM `list_country_product_price` WHERE `product_id`='$id'";

    $query = $conn->query($sql);

    if ($query->num_rows >= "1") {
        echo "<ul>";
        while ($row = $query->fetch_array()) {
            $countryName = getCountryP($row["country_id"]);
?>
            <li>
                <?= $countryName["name"]; ?> - <b>
                    <?= $countryName["sign"]; ?>
                    <?= number_format($row['market_price'], 2) ?>
                </b>
            </li>
        <?php
        }
        echo "</ul>";
    }
}

function getPriceCountrySP($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM `list_country_product_price` WHERE `product_id`='$id'";

    $query = $conn->query($sql);

    if ($query->num_rows >= "1") {
        echo "<ul>";
        while ($row = $query->fetch_array()) {
            $countryName = getCountryP($row["country_id"]);
        ?>
            <li>
                <?= $countryName["name"]; ?> - <b>
                    <?= $countryName["sign"]; ?>
                    <?= number_format($row['sale_price'], 2) ?>
                </b>
            </li>
<?php
        }
        echo "</ul>";
    }
}

function getCountryP($id)
{
    $conn = getDbConnection();
    $sql = "SELECT * FROM `list_country` WHERE `id`='$id'";
    $query = $conn->query($sql);
    $row = $query->fetch_array();
    $dataC = array(
        "id" => $row["id"],
        "name" => $row["name"],
        "sign" => $row["sign"]
    );
    return ($dataC);
}

function getCountryL()
{
    $conn = getDbConnection();
    $sql = "SELECT * FROM `list_country`";
    $query = $conn->query($sql);
    $row = $query->fetch_array();
    $dataC = array(
        "id" => $row["id"],
        "name" => $row["name"],
        "sign" => $row["sign"]
    );
    return ($query);
}

function GetProductDetails($id)
{
    $conn = getDbConnection();
    $sql = "
        SELECT 
            p.id AS product_id,
            p.name,
            p.slug,
            p.description,
            p.type,
            p.category_id,
            p.brand_id,
            p.price_capital,
            p.status AS product_status,
            p.weight,
            p.length,
            p.width,
            p.height,
            p.created_at AS product_created,
            p.updated_at AS product_updated,
            p.deleted_at AS product_deleted,

            pv.id AS variant_id,
            pv.sku,
            pv.price_retail,
            pv.price_sale,
            pv.stock,
            pv.image,
            pv.max_purchase,
            pv.status AS variant_status,
            pv.created_at AS variant_created,
            pv.updated_at AS variant_updated,
            pv.deleted_at AS variant_deleted

        FROM products p
        LEFT JOIN product_variants pv ON p.id = pv.product_id
        WHERE  p.id = '$id' AND p.deleted_at IS NULL
        ";
    $result = $conn->query($sql);
    $row = $result->fetch_array();
    $data = array(
        "name" => $row["name"],
        "slug" => $row["slug"],
        "description" => $row["description"],
        "type" => $row["type"],
        "brand_id" => $row["brand_id"],
        "category_id" => $row["category_id"],
        "max_purchase" => $row["max_purchase"],
        "sku" => $row["sku"],
        "price_capital" => $row["price_capital"],
        "variant_id" => $row["variant_id"],
        "weight" => $row["weight"],
        "length" => $row["length"],
        "width" => $row["width"],
        "height" => $row["height"]
    );
    return ($data);
}
function GetProductVariants($productId)
{
    $conn = getDbConnection();
    $productId = $conn->real_escape_string($productId);
    $sql = "SELECT * FROM `product_variants` WHERE `product_id` = '$productId' AND `deleted_at` IS NULL AND `status` = 1 ORDER BY `id` ASC";
    $result = $conn->query($sql);
    $variants = [];
    while ($row = $result->fetch_assoc()) {
        $variants[] = $row;
    }
    return $variants;
}

function stockBalanceByVariant($variantId)
{
    $conn = getDbConnection();
    $variantId = $conn->real_escape_string($variantId);

    $sql = "
        SELECT
            pv.id AS variant_id,
            pv.variant_name,
            pv.sku,
            pv.max_purchase,

            IFNULL(SUM(sc.stock_in), 0) AS total_stock_in,
            IFNULL(SUM(sc.stock_out), 0) AS total_stock_out,

            IFNULL((
                SELECT SUM(c.quantity)
                FROM cart c
                WHERE c.pv_id = pv.id AND c.status IN (0,1)
            ), 0) AS stock_reserved,

            (IFNULL(SUM(sc.stock_in), 0) - IFNULL(SUM(sc.stock_out), 0) -
                IFNULL((
                    SELECT SUM(c.quantity)
                    FROM cart c
                    WHERE c.pv_id = pv.id AND c.status IN (0,1)
                ), 0)
            ) AS physical_stock

        FROM product_variants pv
        LEFT JOIN stock_control sc ON pv.id = sc.pv_id
        WHERE pv.id = '$variantId' AND pv.deleted_at IS NULL
        GROUP BY pv.id
    ";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return array(
        "variant_id" => $row["variant_id"],
        "variant_name" => $row["variant_name"],
        "sku" => $row["sku"],
        "total_stock_in" => $row["total_stock_in"],
        "total_stock_out" => $row["total_stock_out"],
        "physical_stock" => $row["physical_stock"],
        "max_purchase" => $row["max_purchase"]
    );
}

function getSelectOptions($selectedBrandId = null, $selectedCategoryId = null)
{
    $rawData = cache_remember('options:brands_cats', 600, function() {
        $conn = getDbConnection();
        $brands = [];
        $categories = [];

        $brandResult = mysqli_query($conn, "SELECT id, name FROM brands ORDER BY name ASC");
        if ($brandResult) {
            while ($row = mysqli_fetch_assoc($brandResult)) {
                $brands[] = $row;
            }
        }

        $categoryResult = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
        if ($categoryResult) {
            while ($row = mysqli_fetch_assoc($categoryResult)) {
                $categories[] = $row;
            }
        }

        return ['brands' => $brands, 'categories' => $categories];
    });

    $output = ['brands' => '', 'categories' => ''];

    if (!empty($rawData['brands'])) {
        foreach ($rawData['brands'] as $row) {
            $selected = ($row['id'] == $selectedBrandId) ? ' selected' : '';
            $output['brands'] .= '<option value="' . htmlspecialchars($row['id']) . '"' . $selected . '>' . htmlspecialchars($row['name']) . '</option>' . PHP_EOL;
        }
    } else {
        $output['brands'] = '<option disabled>No brands found</option>';
    }

    if (!empty($rawData['categories'])) {
        foreach ($rawData['categories'] as $row) {
            $selected = ($row['id'] == $selectedCategoryId) ? ' selected' : '';
            $output['categories'] .= '<option value="' . htmlspecialchars($row['id']) . '"' . $selected . '>' . htmlspecialchars($row['name']) . '</option>' . PHP_EOL;
        }
    } else {
        $output['categories'] = '<option disabled>No categories found</option>';
    }

    return $output;
}

function dataCountry($id)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM list_country WHERE id='$id'";
    $query = $conn->query($sql);
    $row = $query->fetch_array();

    $data = array(
        "id" => $row["id"],
        "name" => $row["name"],
        "sign" => $row["sign"],
        "rate" => $row["rate"]
    );
    return ($data);
}

function allSaleCountry($productId = null)
{
    $cacheKey = $productId !== null ? "countries:product:{$productId}" : "countries:all";
    return cache_remember($cacheKey, 900, function() use ($productId) {
        $conn = getDbConnection();
        if ($productId !== null) {
            $productId = mysqli_real_escape_string($conn, $productId);
            $sql = "SELECT * FROM list_country WHERE product_id = '$productId' ORDER BY id ASC";
        } else {
            $sql = "SELECT * FROM list_country WHERE `status`='1' ORDER BY id ASC";
        }
        $result = $conn->query($sql);
        if (!$result) {
            return [];
        }
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    });
}

function maskEmail($email)
{
    $parts = explode('@', $email);
    $name = $parts[0];
    $domain = $parts[1];

    // Mask all but first and last character of the username
    $nameLen = strlen($name);
    if ($nameLen <= 2) {
        $maskedName = substr($name, 0, 1) . str_repeat('*', $nameLen - 1);
    } else {
        $maskedName = substr($name, 0, 1) . str_repeat('*', $nameLen - 2) . substr($name, -1);
    }

    return $maskedName . '@' . $domain;
}

function allSaleCountryPrice($countryid, $productId)
{
    $conn = getDbConnection();

    $sql = "SELECT * FROM `list_country_product_price` WHERE `country_id`='$countryid' AND `product_id`='$productId'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    $row = $result->fetch_array();

    return $row; // ✅ No row is fetched yet
}

function getProductImages($productId)
{
    $conn = getDbConnection();

    $productId = mysqli_real_escape_string($conn, $productId);

    $sql = "SELECT image FROM product_image WHERE product_id = '$productId' ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row['image']; // assuming this is the file path
    }

    mysqli_close($conn);

    return $images;
}

function getCountry($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM list_country WHERE id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id); // "i" = integer

    $stmt->execute();
    $result = $stmt->get_result();

    return $result; // return the result set
}

function currentYear()
{
    $timezone = new DateTimeZone('Asia/Kuala_Lumpur'); // UTC+8
    $datetime = new DateTime('now', $timezone);
    return $datetime->format('Y');
}

function dateNow()
{
    $timezone = new DateTimeZone('Asia/Kuala_Lumpur'); // UTC+8
    $datetime = new DateTime('now', $timezone);
    return $datetime->format('Y-m-d H:i:s');
}

function userData($userID)
{
    $conn = getDbConnection();
    $userData = $conn->query("SELECT * FROM `member` WHERE `id` = '$userID'");

    $userDatas = $userData->fetch_array();
    $dataUser = array(
        "id" => $userDatas["id"],
        "email" => $userDatas["email"],
        "password" => $userDatas["password"],
        "m_name" => $userDatas["m_name"],
        "m_company" => $userDatas["m_company"],
        "m_phone" => $userDatas["m_phone"],
        "m_mykad" => $userDatas["m_mykad"],
        "m_address_1" => $userDatas["m_address_1"],
        "m_address_2" => $userDatas["m_address_2"],
        "m_city" => $userDatas["m_city"],
        "m_postcode" => $userDatas["m_postcode"],
        "m_state" => $userDatas["m_state"],
        "m_country" => $userDatas["m_country"],
        "email_verify" => $userDatas["email_verify"],
        "status" => $userDatas["status"],
        "role" => $userDatas["role"],
        "date_join" => $userDatas["date_join"],
        "date_active" => $userDatas["date_active"],
        "current_login" => $userDatas["current_login"],
        "last_login" => $userDatas["last_login"],
        "sponsor_by" => $userDatas["sponsor_by"],
        "network_tree" => $userDatas["network_tree"],
        "moq_kpi" => $userDatas["moq_kpi"]
    );
    return ($dataUser);
}

function memberPoint($userID)
{
    $conn = getDbConnection();
    $orders = $conn->query("SELECT * FROM customer_order WHERE `status` IN(2,3,4,5) AND awb='[$userID]'");
    if ($orders->num_rows >= "1") {
        $corder = $orders->num_rows;
    } else {
        $corder = 0;
    }
    $pa = $conn->query("SELECT SUM(purchase_amount) AS pa FROM `membership_point_history` WHERE `membership_id` = '$userID'");
    $userPoint = $conn->query("SELECT SUM(point_amount) AS userPoint FROM `membership_point_history` WHERE `membership_id` = '$userID' AND point_status='1'");
    $userPointEP = $conn->query("SELECT SUM(point_amount) AS userPointEP FROM `membership_point_history` WHERE `membership_id` = '$userID'");

    $userPoints = $userPoint->fetch_assoc();
    $userPointEPs = $userPointEP->fetch_assoc();
    $pas = $pa->fetch_assoc();

    if ($userPoints["userPoint"] >= 1) {
        $pointActive = $userPoints["userPoint"];
    } else {
        $pointActive = 0;
    }

    if ($pas["pa"] >= 1) {
        $memberOrder = $pas["pa"];
    } else {
        $memberOrder = 0.00;
    }


    $pointAll = $userPointEPs["userPointEP"];
    if ($pointAll >= 1) {
        $pointExpired = $pointAll - $pointActive;
    } else {
        $pointExpired = 0;
    }

    $dataPoint = array(
        "point" => $pointActive,
        "pointExp" => $pointExpired,
        "orderAmount" => $memberOrder,
        "countOrder" => $corder
    );
    return ($dataPoint);
}

function memberData($userID)
{
    $conn = getDbConnection();
    $userData = $conn->query("SELECT * FROM `membership` WHERE `id` = '$userID'");

    $userDatas = $userData->fetch_array();
    $dataPoint = memberPoint($userID);
    if ($dataPoint["point"] > 0) {
        $points = $dataPoint["point"];
    } else {
        $points = 0;
    }
    $dataUser = array(
        "id" => $userDatas["id"],
        "email" => $userDatas["email"],
        "password" => $userDatas["password"],
        "name" => $userDatas["name"],
        "phone" => $userDatas["phone_c_code"] . $userDatas["phone_no"],
        "address_1" => $userDatas["address_1"],
        "address_2" => $userDatas["address_2"],
        "city" => $userDatas["city"],
        "postcode" => $userDatas["postcode"],
        "state" => $userDatas["state"],
        "country" => $userDatas["country"],
        "date_added" => $userDatas["date_added"],
        "date_update" => $userDatas["date_update"],
        "status" => $userDatas["status"],
        "phone_verify" => $userDatas["phone_verify"],
        "email_verify" => $userDatas["email_verify"],
        "membership_stage" => $userDatas["membership_stage"],
        "referral" => $userDatas["referral"],
        "point" => $points
    );
    return ($dataUser);
}

function dataProduct($productID)
{
    $DBcon = getDbConnection();
    $getProduct = $DBcon->query("SELECT * FROM `product` WHERE `id` = '$productID' AND `status` = '1'");
    if ($getProduct->num_rows < "1") {
        $dataProduct = array(
            "type" => "error",
            "reason" => "product inactive or not exist."
        );
        return ($dataProduct);
    } else {
        //`id`, `sku`, `variation_sku`, `product_name`, `product_image`, `weight`, `length`, `width`, `height`, `capital_price`, `selling_price`, `role_4`, `role_5`, `role_6`, `role_7`, `role_8`, `role_9`, `role_10`, `role_11`, `cod_add_on`, `charge_back_if_not_charge_cod`, `category`, `threshold`, `is_variation`, `status`, `date_insert`, `date_update`, `assign_user`

        $getProducts = $getProduct->fetch_array();
        $dataProduct = array(
            "type" => "success",
            "reason" => "product valid.",
            "id" => $getProducts["id"],
            "sku" => $getProducts["sku"],
            "variation_sku" => $getProducts["variation_sku"],
            "product_name" => $getProducts["product_name"],
            "product_image" => $getProducts["product_image"],
            "weight" => $getProducts["weight"],
            "length" => $getProducts["length"],
            "width" => $getProducts["width"],
            "height" => $getProducts["height"],
            "capital_price" => $getProducts["capital_price"],
            "selling_price" => $getProducts["selling_price"],
            "role_4" => $getProducts["role_4"],
            "role_5" => $getProducts["role_5"],
            "role_6" => $getProducts["role_6"],
            "role_7" => $getProducts["role_7"],
            "role_8" => $getProducts["role_8"],
            "role_9" => $getProducts["role_9"],
            "role_10" => $getProducts["role_10"],
            "role_11" => $getProducts["role_11"],
            "kpi_4" => $getProducts["kpi_4"],
            "kpi_5" => $getProducts["kpi_5"],
            "kpi_6" => $getProducts["kpi_6"],
            "kpi_7" => $getProducts["kpi_7"],
            "kpi_8" => $getProducts["kpi_8"],
            "kpi_9" => $getProducts["kpi_9"],
            "kpi_10" => $getProducts["kpi_10"],
            "kpi_11" => $getProducts["kpi_11"],
            "cod_add_on" => $getProducts["cod_add_on"],
            "charge_back_if_not_charge_cod" => $getProducts["charge_back_if_not_charge_cod"],
            "category" => $getProducts["category"],
            "threshold" => $getProducts["threshold"],
            "is_variation" => $getProducts["is_variation"],
            "status" => $getProducts["status"],
            "date_insert" => $getProducts["date_insert"],
            "date_update" => $getProducts["date_update"],
            "enable_moq" => $getProducts["enable_moq"],
            "enable_kpi" => $getProducts["enable_kpi"],
            "assign_user" => $getProducts["assign_user"],
            "assign_admin" => $getProducts["assign_admin"],
            "enable_my_sg" => $getProducts["enable_my_sg"],
            "product_description" => $getProducts["product_description"],
            "member_point" => $getProducts["member_point"]
        );
        return ($dataProduct);
    }
}

function getTAC($userID, $tac)
{
    $conn = getDbConnection();

    $iuserID = (string) $userID; // Ensure it's a string
    $itac = (string) $tac;

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM phone_verify_code WHERE `member` = ? AND `wasap_code` = ?");
    $stmt->bind_param("si", $iuserID, $itac);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tacDetails = $result->fetch_assoc(); // Fetch as an associative array

        $dataTAC = array(
            "userID" => $userID,
            "tac" => $tac,
            "member" => $tacDetails["member"] ?? null,
            // Use null coalescing to prevent undefined index errors
            "phone_no" => $tacDetails["phone_no"] ?? null,
            "wasap_code" => $tacDetails["wasap_code"] ?? null,
            "status" => $tacDetails["status"] ?? null,
            "date_send" => $tacDetails["date_send"] ?? null,
            "date_update" => $tacDetails["date_update"] ?? null,
            "code_expired_on" => $tacDetails["code_expired_on"] ?? null
        );
    } else {
        // If no matching record found, return empty/null values
        $dataTAC = array(
            "userID" => $iuserID,
            "tac" => $itac,
            "member" => null,
            "phone_no" => null,
            "wasap_code" => null,
            "status" => null,
            "date_send" => null,
            "date_update" => null,
            "code_expired_on" => null
        );
    }

    $stmt->close(); // Close the statement
    return $dataTAC;
}

function myTime()
{
    $timezone = "Asia/Kuala_Lumpur";
    if (function_exists('date_default_timezone_set'))
        date_default_timezone_set($timezone);
    $dates = date("Y-m-d H:i:s");
    return $dates;
}

function getOrder($typeID, $theID)
{
    $conn = getDbConnection();
    if ($typeID == "1") {
        $getOrder = $conn->query("SELECT * FROM customer_orders WHERE id='$theID'");
    } else if ($typeID == "2") {
        $getOrder = $conn->query("SELECT * FROM customer_orders WHERE verify_id='$theID'");
    } else if ($typeID == "3") {
        $getOrder = $conn->query("SELECT * FROM customer_orders WHERE payment_code='$theID'");
    }

    $row = $getOrder->fetch_array();

    $data = array(
        "id" => $row["id"],
        "session_id" => $row["session_id"],
        "order_to" => $row["order_to"],
        "product_var_id" => $row["product_var_id"],
        "total_qty" => $row["total_qty"],
        "total_price" => $row["total_price"],
        "postage_cost" => $row["postage_cost"],
        "currency_sign" => $row["currency_sign"],
        "country_id" => $row["country_id"],
        "country" => $row["country"],
        "state" => $row["state"],
        "city" => $row["city"],
        "postcode" => $row["postcode"],
        "address_2" => $row["address_2"],
        "address_1" => $row["address_1"],
        "customer_name" => $row["customer_name"],
        "customer_phone" => $row["customer_phone"],
        "customer_email" => $row["customer_email"],
        "status" => $row["status"],
        "payment_channel" => $row["payment_channel"],
        "payment_code" => $row["payment_code"],
        "payment_url" => $row["payment_url"],
        "ship_channel" => $row["ship_channel"],
        "courier_service" => $row["courier_service"],
        "awb_number" => $row["awb_number"],
        "tracking_url" => $row["tracking_url"],
        "created_at" => $row["created_at"],
        "updated_at" => $row["updated_at"],
        "deleted_at" => $row["deleted_at"],
        "remark_comment" => $row["remark_comment"],
        "tracking_milestone" => $row["tracking_milestone"],
        "to_myr_rate" => $row["to_myr_rate"],
        "myr_value_include_postage" => $row["myr_value_include_postage"],
        "myr_value_without_postage" => $row["myr_value_without_postage"]
    );

    return ($data);
}

function menuOrderCount()
{
    $conn = getDbConnection();
    $newOrder = $conn->query("SELECT * FROM customer_orders WHERE `status`='1'");
    if ($newOrder->num_rows >= "1") {
        $orderNew = $newOrder->num_rows;
    } else {
        $orderNew = 0;
    }

    $processOrder = $conn->query("SELECT * FROM customer_orders WHERE `status`='2'");
    if ($processOrder->num_rows >= "1") {
        $orderProcess = $processOrder->num_rows;
    } else {
        $orderProcess = 0;
    }

    $deliveryOrder = $conn->query("SELECT * FROM customer_orders WHERE `status`='3'");
    if ($deliveryOrder->num_rows >= "1") {
        $orderDelivery = $deliveryOrder->num_rows;
    } else {
        $orderDelivery = 0;
    }

    $completeOrder = $conn->query("SELECT * FROM customer_orders WHERE `status`='4'");
    if ($completeOrder->num_rows >= "1") {
        $orderComplete = $completeOrder->num_rows;
    } else {
        $orderComplete = 0;
    }

    $returnOrder = $conn->query("SELECT * FROM customer_orders WHERE `status`='5'");
    if ($returnOrder->num_rows >= "1") {
        $orderReturn = $returnOrder->num_rows;
    } else {
        $orderReturn = 0;
    }

    $cancelOrder = $conn->query("SELECT * FROM customer_orders WHERE `status`='6'");
    if ($cancelOrder->num_rows >= "1") {
        $orderCancel = $cancelOrder->num_rows;
    } else {
        $orderCancel = 0;
    }

    $data = array(
        "0" => $orderNew,
        "1" => $orderProcess,
        "2" => $orderDelivery,
        "3" => $orderComplete,
        "4" => $orderReturn,
        "5" => $orderCancel
    );

    return ($data);
}

function getBillPlzz()
{
    $conn = getDbConnection();
    $billPlz = $conn->query("SELECT * FROM `billplz` ORDER BY id DESC LIMIT 1");
    $billPlzs = $billPlz->fetch_array();

    $data = array(
        "sandbox_production" => $billPlzs["sandbox_production"],
        "sanbox" => $billPlzs["sand_box_url"],
        "production" => $billPlzs["production_url"],
        "api_key" => $billPlzs["api_key"],
        "x_signature" => $billPlzs["x_signature"],
        "bill_collection_id" => $billPlzs["bill_collection_id"],
        "payment_collection_slug" => $billPlzs["payment_collection_slug"],
        "bill_charge" => $billPlzs["bill_charge"],
        "payment_charge" => $billPlzs["payment_charge"]
    );
    return ($data);
}

function billPlzzOrder($orderID, $name, $email, $phone, $domainURL)
{
    $dataBP = getBillPlzz();

    $orderDetails = getOrder(1, $orderID);

    if ($dataBP["sandbox_production"] == "0") {
        $bpURL = $dataBP["sanbox"];
    } else {
        $bpURL = $dataBP["production"];
    }

    $epURL = $bpURL;
    $billplzAPI_key = $dataBP["api_key"];
    $epCID = $dataBP["bill_collection_id"];
    $uEmail = $email;
    $uPhone = $phone;
    $uName = $name;
    $uAmount = ($orderDetails["order_amount"] + $orderDetails["postage_cost"] + $dataBP["bill_charge"]) * 100;
    $uDescription = "Payment Order " . $orderID . " (RM1.00 will charge for the FPX fee)";
    $reference_1_label = "Payment Order " . $orderID;
    $reference_1 = "" . $orderID;

    $testCallBack = "https://webhook.site/0ce7b58c-31d9-46bc-9f41-ccfc18651041";
    $trueCallBack = $domainURL . "bp-callback.php";

    $url = $epURL . 'api/v3/bills';
    $api_key = $billplzAPI_key;
    $fields = array(
        'collection_id' => $epCID,
        'email' => $uEmail,
        'mobile' => urlencode($uPhone),
        'name' => $uName,
        'amount' => $uAmount,
        'callback_url' => $trueCallBack,
        'description' => $uDescription,
        'redirect_url' => $domainURL . "thank-you.php",
        'reference_1_label' => $reference_1_label,
        'reference_1' => $reference_1
    );
    $fields_string = http_build_query($fields);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
    // Set custom headers for RapidAPI Auth and Content-Type header
    curl_setopt($curl, CURLOPT_USERPWD, $api_key . ":");
    // Execute cURL request with all previous settings
    $response = curl_exec($curl);
    // Close cURL session
    curl_close($curl);
    $result = json_decode($response, TRUE);

    // echo "<pre>";
    // print_r($result);
    // echo "</pre>";

    $bpIDss = $result["id"];
    $bpURL = $result["url"];

    $data = array(
        "pay_code" => $bpIDss,
        "pay_url" => $bpURL
    );
    return ($data);
}

function confirmBillPlz($pay_id, $orderStatus, $dates)
{
    $conn = getDbConnection();
    $updateOrder = $conn->query("UPDATE customer_order SET date_update='$dates', `status`='$orderStatus' WHERE payment_code='$pay_id'");

    if ($updateOrder) {
        $getOrder = getOrder("3", $pay_id);

        $cartId = $getOrder["cart_id"];

        $updateCart = $conn->query("UPDATE cart SET date_update='$dates', `status`='1' WHERE cart_id='$cartId'");
    }
}

function normalizeNumber($number)
{
    return preg_replace('/^0+/', '0', $number);
}

function createJTShippings($id)
{
    $conn = getDbConnection();

    $url = "https://demostandard.jtexpress.my/blibli/order/createOrder";
    $key = "AKe62df84bJ3d8e4b1hea2R45j11klsb";

    $jsonData = '{
        "detail":[
            {
                "username":"TEST",
                "api_key":"",
                "cuscode":"",
                "password:"",
                "orderid:"",
                "shipper_name:"",
                "shipper_contact:"",
                "shipper_phone:"",
                "shipper_addr:"",
                "sender_zip:"",
                "receiver_name:"",
                "receiver_addr:"",
                "receiver_phone:"",
                "receiver_zip:"",
                "qty:"",
                "weight:"",
                "Item_name:"",
                "goodsdesc:"",
                "goodsvalue:"",
                "payType:"",
                "expressType:"",
                "goodsType:"",
                "servicetype:"",
                "sendstarttime:"",
                "sendendtime:""
            }
        ]
    }';

    $singnature = base64_encode(md5($jsonData . $key));

    $post = array(
        'data_param' => $jsonData,
        'data_sign' => $singnature,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
}

function createJTShipping($id)
{
    $jt = dataSettingJNT();
    $conn = getDbConnection(); // Make sure this returns a working mysqli connection
    $dateNow = dateNow();

    $todayDate = date('Y-m-d');

    if ($jt["status"] == "0") {
        $url = $jt["url_sandbox"];
        $key = $jt["key_sandbox"];
        $username = $jt["username_sanbox"];
        $api_key = "TES123";
        $cuscode = $jt["cuscode_sandbox"];
        $password = $jt["password_sandbox"];
        $sistemOrderID = "-webtest3";
    } else if ($jt["status"] == "1") {
        $url = $jt["url_production"];
        $key = $jt["key_production"];
        $username = $jt["username_production"];
        $api_key = "ROZEYANA123";
        $cuscode = $jt["cuscode_production"];
        $password = $jt["password_production"];
        $sistemOrderID = "";
    }


    $idArray = array_filter(array_map('intval', explode(',', $id)));
    if (empty($idArray)) {
        echo "No valid order IDs provided.\n";
        return;
    }

    $idList = implode(',', $idArray);
    $sql = "SELECT * FROM customer_orders WHERE id IN ($idList)";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows == 0) {
        echo "No matching orders found.\n";
        return;
    }

    $hqname = "ROZZ BEAUTY LEGACY";
    $address1 = "B-G-48, SAVANNA LIFESTYLE RETAIL";
    $address2 = "Jalan Southville 2, Southville City";
    $city = "Dengkil";
    $state = "Selangor";
    $district = "Dengkil";
    $country = "MY";
    $postCode = "43800";
    $phone = "60389123807";
    $email = "wafazz.tech@gmail.com";

    $now = $todayDate . " 10:00:00";
    $end = $todayDate . " 17:00:00";



    $detail = [];
    while ($order = $result->fetch_assoc()) {
        $receiverName = trim($order['customer_name'] . ' ' . $order['customer_name_last']);
        $receiverAddr = $order['address_1'] . ', ' . $order['address_2'] . ', ' . $order['city'] . ', ' . $order['state'];
        $receiverPhone = $order['customer_phone'];
        $receiverZip = $order['postcode'];

        $detail[] = [
            "username" => $username,
            "api_key" => $api_key,
            "cuscode" => $cuscode,
            "password" => $password,
            "orderid" => "ROZEYANA-" . str_pad($order['id'], 8, '0', STR_PAD_LEFT) . $sistemOrderID,
            "shipper_name" => $hqname,
            "shipper_contact" => "Admin",
            "shipper_phone" => $phone,
            "shipper_addr" => $address1 . ", " . $address2 . ", " . $city . ", " . $state,
            "sender_zip" => $postCode,

            "receiver_name" => $receiverName,
            "receiver_addr" => $receiverAddr,
            "receiver_phone" => $receiverPhone,
            "receiver_zip" => preg_replace('/\s+/', '', $receiverZip),

            "qty" => (int)$order['total_qty'],
            "weight" => 0.5,
            "Item_name" => "PARCEL FOR ORDER " . str_pad($order['id'], 8, '0', STR_PAD_LEFT),
            "goodsdesc" => "Order #" . str_pad($order['id'], 8, '0', STR_PAD_LEFT),
            "goodsvalue" => number_format($order['myr_value_without_postage'], 2),

            "payType" => "1",
            "expressType" => "EZ",
            "goodsType" => "PARCEL",
            "servicetype" => "PICKUP",

            "sendstarttime" => $now,
            "sendendtime" => $end
        ];
    }

    //$conn->close();

    if (empty($detail)) {
        echo "No valid order details found.\n";
        return;
    }

    $jsonData = json_encode(['detail' => $detail], JSON_UNESCAPED_UNICODE);
    $signature = base64_encode(md5($jsonData . $key));

    $post = [
        'data_param' => $jsonData,
        'data_sign' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $responses = json_decode($response, true);

    $ids = explode(',', $id);

    $n = 0;
    $u = 0;
    $f = 0;
    $trackingTrue = "";
    $trackingFalse = "";
    $idss = array_reverse($ids);
    //$idArray = explode(',', $ids);

    // Step 2: Convert strings to integers (optional but recommended)
    $idArrays = array_map('intval', $ids);

    // Step 3: Sort in ascending order
    sort($idArrays);
    foreach ($idArrays as $k) {

        $awb = $responses["details"][$n]["awb_no"];
        $code = $responses["details"][$n]["data"]["code"];
        $thestatus = $responses["details"][$n]["status"];
        if ($thestatus === 'success') {
            $trackingURL = "https://jtexpress.my/tracking/" . $awb;

            $updateOrder = $conn->query("UPDATE customer_orders SET `status`='2', `ship_channel`='Doorstep Delivery', `courier_service`='J&T EXPRESS', `awb_number`='$awb', `tracking_url`='$trackingURL', `updated_at`='$dateNow' WHERE id='$k'");

            $addCode = $conn->query("INSERT INTO `jt_code`(`id`, `order_id`, `awb`, `jt_code`) VALUES (NULL,'$k','$awb','$code')");

            $trackingTrue .= "<span style='color:green;'>** Successful assign tracking to order #" . $k . ". The order now transfered to Processing Order.</span><br>";
            $trackingFalse .= "";
            $u++;
        } else {
            //$updateOrder = $conn->query("UPDATE customer_orders SET `status`='1', `ship_channel`='Doorstep Delivery', `courier_service`='J&T EXPRESS', `awb_number`='', `tracking_url`='', `updated_at`='$dateNow' WHERE id='$k'");
            $trackingTrue .= "";
            $trackingFalse .= "<span style='color:red;'>** Failed to assign tracking to order #" . $k . ". " . $responses["details"][$n]["status"] . " " . $responses["details"][$n]["reason"] . " - " . $responses["details"][$n]["msg"] . ". Order #" . $k . " remain in New Order stage.</span><br>";
            $f++;
        }
        $n++;
    }

    $thesuccess = $n - $u;

    echo $trackingTrue;
    echo $trackingFalse;

    echo "<pre>";
    print_r($responses);
    echo "</pre>";

    $data = array(
        "all" => $n,
        "success" => $u,
        "failed" => $f,
        "true" => $trackingTrue,
        "false" => $trackingFalse
    );
    return ($data);

    // if(isset($trackingTrue) || !empty($trackingTrue)){
    //     $_SESSION["trackingTrue"] = $trackingTrue;
    // }

    // if(isset($trackingFalse) || !empty($trackingFalse)){
    //     $_SESSION["trackingTrue"] = $trackingFalse;
    // }
}

function dataSettingJNT()
{
    $conn = getDbConnection();

    $sql = "SELECT `production_sandbox`, `url_sandbox`, `username_sanbox`, `password_sandbox`, `cuscode_sandbox`, `key_sandbox`, `url_production`, `username_production`, `password_production`, `cuscode_production`, `key_production` FROM `jt_setting` WHERE id='1'";

    $query = $conn->query($sql);
    $row = $query->fetch_array();

    $data = [
        "status" => $row["production_sandbox"],
        "url_sandbox" => $row["url_sandbox"],
        "username_sanbox" => $row["username_sanbox"],
        "password_sandbox" => $row["password_sandbox"],
        "cuscode_sandbox" => $row["cuscode_sandbox"],
        "key_sandbox" => $row["key_sandbox"],
        "url_production" => $row["url_production"],
        "username_production" => $row["username_production"],
        "password_production" => $row["password_production"],
        "cuscode_production" => $row["cuscode_production"],
        "key_production" => $row["key_production"]
    ];

    return ($data);
}

function sendSecurityCode(string $toEmail, int $code): bool
{
    $templatePath = __DIR__ . '/../EmailTemplate/securityCodeTemp.php';

    if (!file_exists($templatePath)) {
        return false;
    }

    $html = file_get_contents($templatePath);

    $html = str_replace(
        ['{{CODE}}', '{{APP_NAME}}', '{{EXPIRY}}'],
        [$code, 'Rozeyana', '5 minutes'],
        $html
    );

    //$mail = new PHPMailer(true);
    //$mail = getMailer();
    $mail = getMailerBrevo();

    try {
        // $mail->isSMTP();
        // $mail->Host = '127.0.0.1';
        // $mail->Port = 25;
        // $mail->SMTPAuth = false;
        // $mail->SMTPAutoTLS = false;

        // $mail->setFrom('noreply@rozeyana.com', 'Rozeyana');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your Security Code';
        $mail->Body = $html;
        $mail->AltBody = "Your security code is: $code";

        return $mail->send();
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

function invalidateCache_category($id = null)
{
    if ($id) {
        cache_delete("category:{$id}");
    }
    cache_delete('options:brands_cats');
}

function invalidateCache_brand($id = null)
{
    if ($id) {
        cache_delete("brand:{$id}");
    }
    cache_delete('options:brands_cats');
}

function invalidateCache_country()
{
    cache_flush('countries:');
}

function invalidateCache_role($userId = null)
{
    if ($userId) {
        cache_flush("role:{$userId}:");
    } else {
        cache_flush('role:');
    }
}

function invalidateCache_cart($sessionId = null)
{
    if ($sessionId) {
        cache_delete("cart:qty:{$sessionId}");
    } else {
        cache_flush('cart:qty:');
    }
}
