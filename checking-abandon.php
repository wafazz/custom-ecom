<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

$conn = getDbConnection();

// Set MySQL timezone to UTC+8
//$conn->query("SET time_zone = '+08:00'");

// PHP timezone
//date_default_timezone_set('Asia/Kuala_Lumpur');

$dateNow = dateNow();

$sql = "SELECT * FROM cart WHERE deleted_at IS NULL AND `status`='0'";
$result = $conn->query($sql);

$x=1;
while ($row = $result->fetch_assoc()) {


    $idCart = $row["id"];

    $newTime = date('Y-m-d H:i:s', strtotime($row["updated_at"] . ' +30 minutes'));

    if($dateNow > $newTime){
        $ss = "<span style='color:red;'>Expired</span>";
        $updateCart = $conn->query("UPDATE cart SET updated_at='$dateNow', deleted_at='$dateNow', `status`='4' WHERE id='$idCart'");
    }else{

        $ss = "<span style='color:green;'>Active</span>";
    }
    echo $x.") ".$row["session_id"]. " - " .$row["updated_at"]." (expired on ".$newTime." - ".$ss.")<br>";
    $x++;
}
