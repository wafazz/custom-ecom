<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

if (!is_login()) {
    header("Location: login");
    exit;
}

$conn = getDbConnection();


if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // PDO or mysqli connection here
    $stmt = $conn->query("SELECT * FROM member_hq WHERE email = '$email'");
    //$stmt->execute([$email]);
    
    if ($stmt->num_rows >= 1) {
        echo "exists";
    } else {
        echo "ok";
    }
}