<?php
header('Content-Type: application/json');
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';


$conn = getDbConnection();

$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');

$sql = "
    SELECT DISTINCT postcode, post_office 
    FROM postcode_my 
    WHERE postcode LIKE '$q%' 
    ORDER BY postcode ASC 
    LIMIT 20
";

$result = mysqli_query($conn, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'label' => $row['postcode'] . ' - ' . $row['post_office'],
        'value' => $row['postcode'],
        'postcode' => $row['postcode'],
        'city' => $row['post_office']
    ];
}

echo json_encode($data);
