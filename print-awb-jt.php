<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';
$conn = getDbConnection();
function e($str)
{
    print '<pre>';
    print_r($str);
    print '</pre>';
}

$url = "https://ylstandard.jtexpress.my/jandt_report_web/print/facelistAction!print.action";

//$billcode = "630000926804";
// $billcodes = [
//     "630000926804",
//     "631601126040",
//     "631600002946",
//     "630001175461",
//     "630001217314""630000926804,631601126040,631600002946,630001175461,630001217314"
// ];

$ids = $_GET["ids"];

$idArray = array_filter(array_map('intval', explode(',', $ids)));
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

while ($order = $result->fetch_assoc()) {
    if (!empty($order['awb_number'])) {
        $awbList[] = $order['awb_number'];
    }
}



$billcodes = implode(',', $awbList) . ',';;

$data = [
    //'billcode' => $billcode,
    'billcode' => $billcodes,
    'account' => 'ROZEYANA',
    'password' => 'ROZEYANA123',
    'customercode' => 'JTMY023701',
];

$t = [
    'logistics_interface' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    'data_digest' => md5($billcode),
    'msg_type' => '1',
];

$s = curl_init();
curl_setopt($s, CURLOPT_URL, $url);
curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query($t));

header('Content-type: application/pdf');

$response = curl_exec($s);



curl_close($s);

echo $response;

e($response);
