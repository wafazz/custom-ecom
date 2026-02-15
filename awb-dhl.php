<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require __DIR__ . '/vendor/autoload.php'; // Dompdf & Barcode
require_once __DIR__ . '/phpqrcode/qrlib.php'; // QR code

use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

$printID = $_GET["id"];
$conn = getDbConnection();

$sqlPrint = "SELECT * FROM dhl_bulk_print WHERE id='$printID'";
$queryPrint = $conn->query($sqlPrint);
$rowPrint = $queryPrint->fetch_array();
// $order = getOrder(1, $orderID);

// $orerIDS = explode(",", $rowPrint["order_id"]);
$orerIDS = explode(",", $printID);

// 1. Generate QR code as base64 using phpqrcode
function generateQRCodeBase64($data, $filename)
{
    $file = __DIR__ . "/temp/{$filename}.png";
    if (!file_exists(__DIR__ . '/temp')) {
        mkdir(__DIR__ . '/temp', 0755, true);
    }
    QRcode::png($data, $file, QR_ECLEVEL_H, 4);
    return 'data:image/png;base64,' . base64_encode(file_get_contents($file));
}

// 2. Generate barcode as base64 using picqer/php-barcode-generator
function generateBarcodeBase64($code)
{
    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
    return 'data:image/png;base64,' . base64_encode($barcode);
}

// 3. Convert logo to base64
function toBase64($path)
{
    return file_exists($path)
        ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path))
        : '';
}

// 4. Setup Dompdf
$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(__DIR__);
$dompdf = new Dompdf($options);

// 5. Dummy invoice data
$invoices = [
    ['number' => 123, 'total' => 49.30],
    // ['number' => 124, 'total' => 88.00],
    // ['number' => 125, 'total' => 25.50],
];

// 6. Base64 logos
$logo1 = toBase64(__DIR__ . '/assets/images/LOGO-ROZYANA-06.png');
$logo2 = toBase64(__DIR__ . '/assets/images/jt-logo.png');

// 7. Start HTML
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoices</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .invoice {
            page-break-after: always;
            padding: 10px;
        }
        .invoice:last-child {
            page-break-after: auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 0px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .header-table td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        img.logo {
            width: 60%;
        }
        img.qr {
            width: 60%;
            margin-bottom: 5px;
        }
        img.barcode {
            width: 100%;
            height: 40px;
        }
        .postcode{
            display:block;
            text-align:center;
            font-size:18px;
            color:#000;
            background:#fff;
            border:1px solid #000;
            padding:10px 15px;
            font-weight:bold;
            width:80px;
            margin-left:auto;
            margin-right:auto;
            position:absolute;
            bottom:15px;
            right: 15px;
        }
    </style>
</head>
<body>
HTML;

// 8. Loop through each invoice

foreach ($orerIDS as $orerID) {


    $getOrderss = "
    SELECT 
        customer_orders.id AS order_id,
        customer_orders.session_id,
        customer_orders.customer_name,
        customer_orders.created_at AS order_date,
        customer_orders.awb_number AS awb_number,

        cart.pv_id,
        cart.quantity,
        
        products.name AS product_name

    FROM customer_orders

    JOIN cart ON customer_orders.session_id = cart.session_id
        AND cart.deleted_at IS NULL

    JOIN products ON cart.p_id = products.id
        AND products.deleted_at IS NULL

    WHERE customer_orders.id = $orerID
        AND customer_orders.deleted_at IS NULL
";

    $gr = $conn->query($getOrderss);

    $order = getOrder(1, $orerID);
    $xx = 1;
    $itemsddd = "";
    while ($orders = $gr->fetch_array()) {

        $itemsddd .= '<tr style="border-bottom: 1px solid #ccc;">
            <td style="width:70%; text-align:left;padding: 5px 8px;">'.$orders["product_name"].'</td>
            <td style="width:30%; text-align:center;padding: 5px 8px;">x'.$orders["quantity"].'</td>
        </tr>';
        
        $xx++;
    }
    $invNo = htmlspecialchars($order["awb_number"]);
    $total = number_format(200, 2);
    $invoiceURL = $order["awb_number"];

    $padded = str_pad($orerID, 8, '0', STR_PAD_LEFT);
    $tracking = $order["awb_number"];

    $tsql = "SELECT * FROM jt_code WHERE order_id='$orerID'";
    $tquery = $conn->query($tsql);
    $row = $tquery->fetch_array();

    $qr = generateQRCodeBase64($invoiceURL, $order["awb_number"]);
    $barcode = generateBarcodeBase64($order["awb_number"]);

    $html .= <<<HTML
<div class="invoice">
    <table class="header-table">
        <tr>
            <td>
                <img src="{$logo1}" class="logo" style="width:80% !important;" /><br><img src="{$logo2}" class="logo" />
                <br>
                <small style="display:block;">Ref CODE: {$row["jt_code"]}</small>
            </td>
            <td>
                <img src="{$qr}" class="qr" />
            </td>
        </tr>
    </table>


    <table>
        <tbody>
            <tr>
                <td style="text-align:left;width:75%;">
                    <table style="width:100%;border:0px solid #fff;">
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:150px;font-size:12px;text-align:left;vertical-align:top;position:relative;">
                                Ship to:
                                <br>
                                <b>
                                    {$order["customer_name"]}
                                </b>
                                <br>
                                {$order["address_1"]}
                                <br>
                                {$order["address_2"]}
                                <br>
                               
                                {$order["city"]}, {$order["state"]}
                                <br>
                                <i>Phone: <b>{$order["customer_phone"]}</b></i>
                                <span class="postcode">{$order["postcode"]}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 8px;height:150px;font-size:12px;text-align:left;vertical-align:top;position:relative;">
                                Sender:
                                <br>
                                <b>
                                    ROZZ BEAUTY LEGACY
                                </b>
                                <br>
                                B-G-48, SAVANA LIFESTYLE RETAIL,
                                <br>
                                <br>
                                DENGKIL, SELANGOR
                                <span class="postcode">43800</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="text-align:left;vertical-align:top;">
                    <table style="width:100%;border:0px solid #fff;">
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:68px;font-size:12px;text-align:center;vertical-align:top;color:#000;background:#fff;font-weight:100;">
                                Ref CODE:<br><b>{$row["jt_code"]}</b>
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:50px;font-size:15px;text-align:center;vertical-align:middle;color:#fff;background:#000;font-weight:bold;">
                            "PICKUP"
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;font-size:12px;text-align:left;vertical-align:middle;color:#000;background:#fff;font-weight:100;">
                                <br>
                            Order No:
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:50px;font-size:15px;text-align:center;vertical-align:middle;color:#fff;background:#000;font-weight:bold;">
                                #{$padded}
                            
                            </td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    Order Date:<br>
                    {$order["created_at"]}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left !important;font-size:12px !important;">
<br>
                    &nbsp;&nbsp;Order Item:
                    <table style="width:100%;border:0px solid #fff;">
                        {$itemsddd}
                    </table>
                    
                </td>
            </tr>
        </tbody>
    </table>



    <table class="header-table" style="margin-top:30px;">
        <tr>
            <td style="width:120px;"></td>
            <td>
                
            <img src="{$barcode}" class="barcode" />
            <br>
            <small><b>{$order["awb_number"]}</b></small>
            </td>
            <td style="width:120px;"></td>
        </tr>
    </table>
</div>
HTML;
}

$html .= "</body></html>";

// 9. Render PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A5', 'portrait');
$dompdf->render();
$dompdf->stream("rozeyana.com-awb.pdf", ["Attachment" => true]);

// 10. Optional cleanup
// array_map('unlink', glob(__DIR__ . "/temp/*.png"));
