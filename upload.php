<?php
require_once("config/mainConfig.php");
require_once("config/function.php");

$currentYear = currentYear();
$dateNow = dateNow();
if (isset($_FILES['image'])) {
    //$targetDir = "uploads/";
    $targetDir = 'assets/images/uploads/' . $currentYear . '/';
    $uploadedFiles = [];
    $errors = [];
    $maxSize = 10 * 1024 * 1024; // 10MB in bytes

    // Create upload directory if it doesn't exist
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            echo "error";
            exit;
        }
    }
    $filename = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        echo $targetFile; // return URL
    } else {
        echo "error";
    }
}
