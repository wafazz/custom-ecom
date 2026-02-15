<?php
require_once("config/mainConfig.php");
require_once("config/function.php");

//header("Content-Type:application/x-www-form-urlencoded");

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array

$col_id = $_REQUEST["collection_id"];
$pay_id = $_REQUEST["id"];
$paid = $_REQUEST["paid"];
$state = $_REQUEST["state"];
$amount = $_REQUEST["amount"];
$paid_amount = $_REQUEST["paid_amount"];
$due_at = $_REQUEST["due_at"];
$email = $_REQUEST["email"];
$mobile = $_REQUEST["mobile"];
$name = $_REQUEST["name"];
$url = $_REQUEST["url"];
$paid_at = $_REQUEST["paid_at"];

if($paid == "true" AND $state = "paid"){
    $new_date = date('Y-m-d H:i:s', strtotime($dates . ' +90 days'));
    $order = getOrder (3, $pay_id);
    $orderId = $order["id"];
    $conn = getDbConnection();
    $updateMP = $conn->query("UPDATE membership_point_history SET date_expired_point='$new_date', point_status='1' WHERE order_id='$orderId'");
    $settlement = confirmBillPlz ($pay_id, "1", $dates);
}

// $jsonFile = $col_id."_".time()."_".$pay_id.".json";

// //file_put_contents($jsonFile, $inputJSONs);
// file_put_contents("json/".$jsonFile, $inputJSONs);