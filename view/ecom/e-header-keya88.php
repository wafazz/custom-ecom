<?php
if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
    if (isset($_COOKIE['country'])) {
        $country = $_COOKIE['country'];
    }

    $getCountry = dataCountry($country);;
    $_SESSION["session_id"] = $currentYear . "_" . $getCountry["name"] . "_" . $getCountry["sign"] . "_" . uniqid('cart_', true);
}

$dateNow = date('Y-m-d H:i:s');
$dateOnly = date('Y-m-d');
$date5Minutes = date('Y-m-d H:i:s', strtotime($dateNow . ' + 5 minutes'));

function getUserLiveIP()
{
    $ipKeys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'];
}

$sessionIdReturn = session_id();
$ipAddressLive = $conn->real_escape_string(getUserLiveIP());

// online_visitor_return (unique IP)
$getVar1 = $conn->query("
    SELECT * FROM online_visitor_return 
        WHERE ip_address = '$ipAddressLive' AND created_at LIKE '%$dateOnly%'
");

if ($getVar1->num_rows < 1) {
    // User has visited today
    $conn->query("INSERT INTO `online_visitor_return`(`id`, `ip_address`, `created_at`, `updated_at`, `session_end_at`) VALUES (NULL,'$ipAddressLive','$dateNow','$dateNow','$date5Minutes')");
} else {
    $row1 = $getVar1->fetch_assoc();
    $conn->query("UPDATE `online_visitor_return` SET `updated_at` = '$dateNow', `session_end_at` = '$date5Minutes' WHERE `id` = '" . $row1['id'] . "'");
}

// $conn->query("
//     INSERT INTO online_visitor_return (ip_address, created_at, updated_at, session_end_at)
//     VALUES ('$ipAddressLive', '$dateNow', '$dateNow', '$date5Minutes')
//     ON DUPLICATE KEY UPDATE 
//         updated_at = '$dateNow',
//         session_end_at = '$date5Minutes'
// ");

// // online_visitor_unique (unique session)
// $conn->query("
//     INSERT INTO online_visitor_unique (session_id, ip_address, created_at, updated_at, session_end_at)
//     VALUES ('$sessionIdReturn', '$ipAddressLive', '$dateNow', '$dateNow', '$date5Minutes')
//     ON DUPLICATE KEY UPDATE 
//         updated_at = '$dateNow',
//         session_end_at = '$date5Minutes'
// ");


$countCart = cartCount();
$listCart = cartList();
$listCart2 = cartList();


?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="<?= $ipAddressLive ?>">
    <meta name="keywords" content="Ashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $domainURL; ?>assets/images/r-web-logo.png">
    <link rel="icon" type="image/png" href="<?= $domainURL; ?>assets/images/r-web-logo.png">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ECOM@Rozeyana.com</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/bootstrap.min.css" type="text/css">
    <!-- <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/font-awesome.min.css" type="text/css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="<?= $domainURL ?>assets/ecom/css/style.css" type="text/css">


    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" />

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/assets/owl.theme.default.min.css">
    <script src="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/c274b4e380.js" crossorigin="anonymous"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        .cart-item {
            position: absolute;
            background: #fff;
            width: 200px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 999;
            padding: 10px;
            list-style: none;
        }

        .cart-dropdown {
            position: relative;
        }

        .cart-item li {
            width: 100%;
            text-align: left;
            font-size: 12px;
        }

        #map {
            height: 450px;
            width: 100%;
        }

        .conts ul li {
            margin-left: 25px !important;
        }

        @media (max-width: 768px) {
            .cart-item {
                width: 250px !important;
                right: 5%;
                z-index: 9999;
                left: -167px !important;
            }
        }
    </style>
</head>