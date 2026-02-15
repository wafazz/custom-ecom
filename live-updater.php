<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

// DB connection
$mysqli = new mysqli("localhost", "root", "224223Fakrul2897!", "2025_rozeyana");
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error);
}

$nows = date("Y-m-d H:i:s");

// Count live visitors
$liveVisitorResult = $mysqli->query("
    SELECT COUNT(*) AS live_user
    FROM `online_visitor_return`
    WHERE `session_end_at` >= '$nows'
");

$liveCount = 0;
if ($liveVisitorResult) {
    $row = $liveVisitorResult->fetch_assoc();
    $liveCount = intval($row['live_user']);
}


$allVisitor = 0;
$allVisitorResult = $mysqli->query("
    SELECT COUNT(*) AS all_user
    FROM `online_visitor_return`
");

if ($allVisitorResult) {
    $row = $allVisitorResult->fetch_assoc();
    $allVisitor = intval($row['all_user']);
}

$today = date("Y-m-d");

$allToday = 0;
$allTodayResult = $mysqli->query("
    SELECT COUNT(*) AS all_today
    FROM `online_visitor_return`
    WHERE `created_at` LIKE '%$today%'
");

if ($allTodayResult) {
    $row = $allTodayResult->fetch_assoc();
    $allToday = intval($row['all_today']);
}

// Data to save
$data = [
    'live_user'  => $liveCount,
    'all_user'   => $allVisitor,
    'all_today'  => $allToday,
    'update_at'  => $nows
];

$filePath = __DIR__ . '/live_visitors.json';

// Create file if missing & set permissions
if (!file_exists($filePath)) {
    file_put_contents($filePath, '');
    chmod($filePath, 0666); // read/write for all
}

// Write JSON
file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Live visitors updated: {$liveCount}\n";