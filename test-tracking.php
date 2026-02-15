<?php
function e($str) {print '<pre>'; print_r($str); print '</pre>';}

$url = "https://demo-ylshopee.jtexpress.my/jandt_report_web/print/facelistAction!print.action";

//$billcode = "630000926804";
// $billcodes = [
//     "630000926804",
//     "631601126040",
//     "631600002946",
//     "630001175461",
//     "630001217314"
// ];

$billcodes = "630000926804,631601126040,631600002946,630001175461,630001217314";

$data = [
    //'billcode' => $billcode,
    'billcode' => $billcodes,
    'account' => 'TEST',
    'password' => 'TES123',
    'customercode'=> 'ITTEST0001',
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