<?php

require __DIR__ . '/vendor/autoload.php';

require_once("config/mainConfig.php");
require_once("config/function.php");


$comp = "ROZEYANA";
$pass = "151f4f49cf6da6c1bdf067f1c047ce2d";

$conn = getDbConnection();

$orders = $conn->query("SELECT * FROM customer_orders WHERE `status`=1 AND awb_number != ''");
?>
<ol>

    <?php
    while ($row = $orders->fetch_assoc()) {
        $order_id = $row["id"];
        $awb_number = $row["awb_number"];
        $awbno = $awb_number;



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

        $success = $result['responseitems']['success'];
        $scanstatus = $result['responseitems']['data'][0]['details'][0]['scanstatus'];

        if($scanstatus == 'Delivered') {
            $updateOrder = $conn->query("UPDATE customer_orders SET `status`='4' WHERE id='$order_id '");
        }else if($scanstatus == 'On Delivery') {
            $updateOrder = $conn->query("UPDATE customer_orders SET `status`='3' WHERE id='$order_id '");
        }

    ?>
        <li>
            Order: #<?= $order_id ?>
            <br>
            AWB: <?= $awb_number ?>
            <br>
            Status: <?= $success ?>
            <br>
            Scanstatus: <?= $scanstatus ?>
        </li>
    <?php

        // echo '<pre>';
        // print_r($result);
        // echo '</pre>';
    }
    ?>

</ol>