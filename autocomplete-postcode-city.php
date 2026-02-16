<?php
header('Content-Type: application/json');
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';


$conn = getDbConnection();

$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');

$sql = "
    SELECT DISTINCT p.postcode, p.post_office, COALESCE(s.state_name, '') AS state_name
    FROM postcode_my p
    LEFT JOIN state_my s ON s.state_code = p.state_code
    WHERE p.postcode LIKE '$q%'
    ORDER BY p.postcode ASC
    LIMIT 20
";

$result = mysqli_query($conn, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'label' => $row['postcode'] . ' - ' . $row['post_office'],
        'value' => $row['postcode'],
        'postcode' => $row['postcode'],
        'city' => $row['post_office'],
        'state' => $row['state_name']
    ];
}

echo json_encode($data);
