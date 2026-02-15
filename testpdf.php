<?php
require __DIR__ . '/vendor/autoload.php'; // Dompdf & Barcode
require_once __DIR__ . '/phpqrcode/qrlib.php'; // QR code

use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

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
$logo2 = toBase64(__DIR__ . '/assets/images/dhl.png');

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
foreach ($invoices as $invoice) {
    $invNo = htmlspecialchars($invoice['number']);
    $total = number_format($invoice['total'], 2);
    $invoiceURL = "7027072148842045";

    $qr = generateQRCodeBase64($invoiceURL, "qr_{$invNo}");
    $barcode = generateBarcodeBase64("INV{$invNo}");

    $html .= <<<HTML
<div class="invoice">
    <table class="header-table">
        <tr>
            <td>
                <img src="{$logo1}" class="logo" style="width:80% !important;" /><br><img src="{$logo2}" class="logo" />
                <small style="display:block;">Ref ID: MYXXX00000001-TEST03</small>
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
                            <td style="padding:5px 8px;height:200px;font-size:12px;text-align:left;vertical-align:top;position:relative;">
                                Ship to:
                                <br>
                                <b>
                                    WAFAZZ TECHNOLOGY
                                </b>
                                <br>
                                42, JALAN ECO GRANDEUR 2/2C, ECO GRANDEUR
                                <br>
                                <br>
                                BANDAR PUNCAK ALAM, SELANGOR
                                <br>
                                <i>Phone: <b>601160641644</b></i>
                                <span class="postcode">42300</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 8px;height:200px;font-size:12px;text-align:left;vertical-align:top;position:relative;">
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
                            <td style="padding:5px 8px;height:108px;font-size:12px;text-align:left;vertical-align:top;color:#000;background:#fff;font-weight:bold;">
                                Ref ID:<br>MYXXX00000001-TEST03
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:50px;font-size:15px;text-align:center;vertical-align:middle;color:#fff;background:#000;font-weight:bold;">
                                MYKLG1
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:50px;font-size:15px;text-align:center;vertical-align:middle;color:#fff;background:#000;font-weight:bold;">
                                CEN1
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;">
                            <td style="padding:5px 8px;height:50px;font-size:15px;text-align:center;vertical-align:middle;color:#fff;background:#000;font-weight:bold;">
                                BA05
                            </td>
                        </tr>
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
            <small>DHL ID: <b>7027072148842045</b></small>
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
$dompdf->stream("rozeyana.com-awb.pdf", ["Attachment" => false]);

// 10. Optional cleanup
// array_map('unlink', glob(__DIR__ . "/temp/*.png"));
