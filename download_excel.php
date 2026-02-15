<?php
require_once("config/mainConfig.php");

$conn = getDbConnection();
mysqli_set_charset($conn, "utf8mb4");

function buildDateFilter($type, $input) {
    $where = [];

    if ($type === 'daily') {
        $from = $input['from'];
        $to   = $input['to'];
        $days = (strtotime($to) - strtotime($from)) / 86400;
        if ($days < 0 || $days > 31) die('Invalid range');
        $where[] = "created_at >= '$from'";
        $where[] = "created_at < DATE_ADD('$to', INTERVAL 1 DAY)";
    }

    if ($type === 'weekly') {
        [$y,$w] = explode('-W',$input['week']);
        $start = date('Y-m-d', strtotime($y.'W'.$w.'1'));
        $end   = date('Y-m-d', strtotime($start.' +7 days'));
        $where[] = "created_at >= '$start'";
        $where[] = "created_at < '$end'";
    }

    if ($type === 'monthly') {
        $start = $input['month'].'-01';
        $end   = date('Y-m-d', strtotime("$start +1 month"));
        $where[] = "created_at >= '$start'";
        $where[] = "created_at < '$end'";
    }

    if ($type === 'yearly') {
        $start = $input['year'].'-01-01';
        $end   = ($input['year']+1).'-01-01';
        $where[] = "created_at >= '$start'";
        $where[] = "created_at < '$end'";
    }

    return $where;
}

$where = [];
if (!empty($_GET['type'])) {
    $where = buildDateFilter($_GET['type'], $_GET);
}

$where[] = "status IN (1,2,3,4)";
$where[] = "deleted_at IS NULL";

$whereSql = 'WHERE '.implode(' AND ', $where);

$sumSql = "
SELECT 
    COALESCE(SUM(myr_value_include_postage),0) AS total_sales,
    COALESCE(SUM(myr_value_without_postage),0) AS total_revenue
FROM customer_orders
$whereSql
";

$sumRow = mysqli_fetch_assoc(mysqli_query($conn, $sumSql));

$listSql = "
SELECT 
    id,
    customer_name,
    customer_email,
    country,
    myr_value_include_postage,
    myr_value_without_postage,
    created_at
FROM customer_orders
$whereSql
ORDER BY created_at DESC
";

if($_GET['type'] === 'daily') {
    $title = 'Sales Report Daily ('.($_GET['from'] ?? '').' to '.($_GET['to'] ?? '').')';
}else if($_GET['type'] === 'weekly') {
    $title = 'Sales Report Weekly ('.($_GET['week'] ?? '').')';
}else if($_GET['type'] === 'monthly') {
    $title = 'Sales Report Monthly ('.($_GET['month'] ?? '').')';
}else if($_GET['type'] === 'yearly') {
    $title = 'Sales Report Yearly ('.($_GET['year'] ?? '').')';
}else{
    $title = 'Sales Report';
}

$result = mysqli_query($conn, $listSql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=orders_'.($_GET['type'] ?? 'all').'_'. $title.'_'.date('Ymd_His').'.csv');

$output = fopen('php://output', 'w');

/* ===== SUMMARY AT TOP ===== */
fputcsv($output, ['TOTAL SALES (MYR)', number_format($sumRow['total_sales'], 2)]);
fputcsv($output, ['TOTAL REVENUE (MYR)', number_format($sumRow['total_revenue'], 2)]);
fputcsv($output, []);
fputcsv($output, ['TITLE', $title]);
fputcsv($output, []);

/* ===== COLUMN HEADER ===== */
fputcsv($output, [
    'Order ID',
    'Customer Name',
    'Email',
    'Country',
    'Total Sales (MYR)',
    'Revenue (MYR)',
    'Created At'
]);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        mb_strtoupper($row['customer_name'],'UTF-8'),
        $row['customer_email'],
        $row['country'],
        $row['myr_value_include_postage'],
        $row['myr_value_without_postage'],
        $row['created_at']
    ]);
}

fclose($output);
exit;