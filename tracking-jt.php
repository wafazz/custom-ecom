<?php


$awbno = isset($_GET["p"]) ? $_GET["p"] : '';
$comp = isset($_GET["c"]) ? $_GET["c"] : '';
$pass = isset($_GET["pass"]) ? $_GET["pass"] : '';
// $url = 'https://demostandard.jtexpress.my/common/track/trackings'; //new test
$url = 'https://ylstandard.jtexpress.my/common/track/trackings'; // production
$key = $pass; // ask J&T for production key
$eccompanyid = $comp; // from J&T

// Must be valid JSON string
$json_data = json_encode([
    "queryType"  => 1,
    "language"   => 2,
    "queryCodes" => [$awbno]
]);

// data_digest must use raw binary md5
$data_digest = base64_encode(md5($json_data . $key));

$post = [
    'logistics_interface' => $json_data,
    'data_digest'         => $data_digest,
    'msg_type'            => 'TRACK',
    'eccompanyid'         => $eccompanyid
];

$s = curl_init();
curl_setopt($s, CURLOPT_URL, $url);
curl_setopt($s, CURLOPT_POST, 1);
curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($s, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($s);
curl_close($s);

$result = json_decode($r, true);

echo '<pre>';
print_r($result);
echo '</pre>';
//e($r);
