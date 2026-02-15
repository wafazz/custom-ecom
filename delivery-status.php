<?php
ini_set("max_execution_time", 0);
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';



$conn = getDbConnection();
$dateNow = dateNow();

$batchSize = 100;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
// $next = isset($_GET['next']) ? (int)$_GET['next'] : 0;

$lastFile = "last_processed.json";

if (file_exists($lastFile)) {
    $jsonData = json_decode(file_get_contents($lastFile), true);
    $next = isset($jsonData['last_id']) ? (int)$jsonData['last_id'] : 0;
} else {
    $next = 0;
}

function jtTracking($awbno)
{
    $url = 'https://ylstandard.jtexpress.my/common/track/trackings'; // production
    $key = "151f4f49cf6da6c1bdf067f1c047ce2d"; // ask J&T for production key
    $eccompanyid = "ROZEYANA"; // from J&T

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

    $status = $result['responseitems']['data'][0]['details'][0]['scanstatus'];


    return $status;
}

if ($next >= 1) {
    $sql = "SELECT id, awb_number FROM customer_orders 
        WHERE id > $next AND status=3 AND awb_number IS NOT NULL ORDER BY id ASC LIMIT $batchSize";
}else{
    $sql = "SELECT id, awb_number FROM customer_orders 
        WHERE status=3 AND awb_number IS NOT NULL ORDER BY id ASC LIMIT $batchSize";
}


$result = $conn->query($sql);

if ($result->num_rows >= 1) {
    $count = $result->num_rows;
    $x = 1;
    $p = "";
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $awbno = $row['awb_number'];

        $noo = $x + $offset;

        $status = jtTracking($awbno);

        if ($status == "Delivered") {
            $delivered = "<span style='color: green;font-weight:bold;'>Delivered NEW</span>";
            $updateOrder = $conn->query("UPDATE customer_orders SET `status`='4', updated_at='$dateNow' WHERE `id`='$id'");
        } else {
            $delivered = "<span style='color: red;font-weight:bold;'>" . $status . "</span>";
        }

        if($x == $count){
            $p .= $id;
        }

        echo $noo . ") Order $id | $awbno - status: " . $delivered . "<br>";
        flush();

        $x++;
    }

    $nextOffset = $offset + $batchSize;

    file_put_contents("last_processed.json", json_encode(["last_id" => $p]));

    // echo "<script>
    //     setTimeout(function() {
    //         window.location.href='delivery-status.php?next=$p';
    //     }, 3000);
    // </script>";
    //window.location.href='delivery-status.php?offset=$nextOffset';
} else {
    $p = "0";
    file_put_contents("last_processed.json", json_encode(["last_id" => $p]));
    echo "No more records found.";
}
