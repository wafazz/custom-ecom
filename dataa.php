<?php
// SSE headers
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$mysqli = new mysqli("localhost", "root", "224223Fakrul2897!", "2025_rozeyana");
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error);
}

$timezone = "Asia/Kuala_Lumpur";
if (function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
$dates = date("Y-m-d H:i:s");
$today = date("Y-m-d");

// Status labels
$statusLabels = [
    1 => '<span class="btn btn-outline-info">New Order</span>',
    2 => '<span class="btn btn-outline-primary">Processing</span>',
    3 => '<span class="btn btn-outline-secondary">In Delivery</span>',
    4 => '<span class="btn btn-outline-success">Completed</span>',
    5 => '<span class="btn btn-outline-warning">Return</span>',
    6 => '<span class="btn btn-outline-danger">Canceled</span>',
];

while (true) {

    $nows = date("Y-m-d H:i:s");

    $liveVisitorResult = $mysqli->query("
        SELECT * FROM `online_visitor_return` WHERE `session_end_at` >= '$nows'
    ");
    $live = $liveVisitorResult->num_rows ?? 0;

    // Get totals
    $totalOrders = $mysqli->query("SELECT COUNT(*) AS total_orders 
                                   FROM customer_orders 
                                   WHERE status BETWEEN 1 AND 4")->fetch_assoc()['total_orders'];

    $totalOrdersToday = $mysqli->query("
                            SELECT COUNT(*) AS total_orders_today
                            FROM customer_orders
                            WHERE status BETWEEN 1 AND 4
                            AND DATE(created_at) = '$today'
                        ")->fetch_assoc()['total_orders_today'];

    $totalSales = $mysqli->query("SELECT SUM(total_price + postage_cost) AS total_sales 
                                  FROM customer_orders 
                                  WHERE status BETWEEN 1 AND 4")->fetch_assoc()['total_sales'] ?? 0;

    $totalSalesToday = $mysqli->query("
                            SELECT SUM(total_price + postage_cost) AS total_sales_today
                            FROM customer_orders
                            WHERE status BETWEEN 1 AND 4
                            AND DATE(created_at) = '$today'
                        ")->fetch_assoc()['total_sales_today'];

    $totalReturns = $mysqli->query("SELECT COUNT(*) AS total_returns 
                                    FROM customer_orders 
                                    WHERE status = 5")->fetch_assoc()['total_returns'];

    // Get latest 30 orders
    $orders = [];
    $result = $mysqli->query("
        SELECT id, currency_sign, country, customer_name, status, myr_value_include_postage
        FROM customer_orders
        ORDER BY created_at DESC
        LIMIT 30
    ");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = [
                'id' => '#' . str_pad($row['id'], 8, '0', STR_PAD_LEFT),
                'sign' => $row['currency_sign'],
                'country' => $row['country'], // Correct column
                'customer_name' => $row['customer_name'],
                'amount' => number_format($row['myr_value_include_postage'], 2),
                'status_html' => $statusLabels[$row['status']] ?? ''
            ];
        }
    }

    $data = [
        'totals' => [
            'orders' => $totalOrders,
            'orders_today' => $totalOrdersToday,
            'sales' => number_format($totalSales, 2),
            'sales_today' => number_format($totalSalesToday, 2),
            'returns' => $totalReturns,
            'live_visitors' => $live
        ],
        'orders' => $orders
    ];

    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();

    sleep(2);
}