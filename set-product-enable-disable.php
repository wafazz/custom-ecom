<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

$dateNow = dateNow();
$conn = getDbConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stat = isset($_GET['stat']) ? intval($_GET['stat']) : 0;

if ($id > 0) {
    $stmt = $conn->query("UPDATE products SET `status` = '$stat', updated_at = '$dateNow' WHERE id = $id");
}