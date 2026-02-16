<?php

class LiveOrdersJob
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function handle()
    {
        $nows = date("Y-m-d H:i:s");
        $today = date("Y-m-d");
        $thisMonthStart = date("Y-m-01");
        $lastMonthStart = date("Y-m-01", strtotime("first day of last month"));
        $lastMonthEnd = date("Y-m-t", strtotime("last month"));

        $statusLabels = [
            0 => '<span class="btn btn-outline-warning">Pending Payment</span>',
            1 => '<span class="btn btn-outline-info">New Order</span>',
            2 => '<span class="btn btn-outline-primary">Processing</span>',
            3 => '<span class="btn btn-outline-secondary">In Delivery</span>',
            4 => '<span class="btn btn-outline-success">Completed</span>',
            5 => '<span class="btn btn-outline-warning">Return</span>',
            6 => '<span class="btn btn-outline-danger">Canceled</span>',
            10 => '<span class="btn btn-outline-danger">Failed Payment</span>',
        ];

        $liveVisitorResult = $this->mysqli->query("
            SELECT * FROM `online_visitor_return` WHERE `session_end_at` >= '$nows'
        ");
        $live = $liveVisitorResult->num_rows ?? 0;

        $totalOrders = $this->mysqli->query("SELECT COUNT(*) AS total_orders
                                   FROM customer_orders
                                   WHERE status BETWEEN 1 AND 4")->fetch_assoc()['total_orders'];

        $totalOrdersToday = $this->mysqli->query("
                            SELECT COUNT(*) AS total_orders_today
                            FROM customer_orders
                            WHERE status BETWEEN 1 AND 4
                            AND DATE(created_at) = '$today'
                        ")->fetch_assoc()['total_orders_today'];

        $totalSales = $this->mysqli->query("SELECT SUM(total_price + postage_cost) AS total_sales
                                  FROM customer_orders
                                  WHERE status BETWEEN 1 AND 4")->fetch_assoc()['total_sales'] ?? 0;

        $totalSalesToday = $this->mysqli->query("
                            SELECT SUM(total_price + postage_cost) AS total_sales_today
                            FROM customer_orders
                            WHERE status BETWEEN 1 AND 4
                            AND DATE(created_at) = '$today'
                        ")->fetch_assoc()['total_sales_today'];

        $totalReturns = $this->mysqli->query("SELECT COUNT(*) AS total_returns
                                    FROM customer_orders
                                    WHERE status = 5")->fetch_assoc()['total_returns'];

        $totalSalesThisMonth = $this->mysqli->query("
            SELECT SUM(total_price + postage_cost) AS total_sales_this_month
            FROM customer_orders
            WHERE status BETWEEN 1 AND 4
            AND DATE(created_at) >= '$thisMonthStart'
            ")->fetch_assoc()['total_sales_this_month'] ?? 0;

        $totalSalesLastMonth = $this->mysqli->query("
            SELECT SUM(total_price + postage_cost) AS total_sales_last_month
            FROM customer_orders
            WHERE status BETWEEN 1 AND 4
            AND DATE(created_at) BETWEEN '$lastMonthStart' AND '$lastMonthEnd'
            ")->fetch_assoc()['total_sales_last_month'] ?? 0;

        $orders = [];
        $result = $this->mysqli->query("
            SELECT id, total_qty, currency_sign, country, customer_name, status, myr_value_include_postage
            FROM customer_orders
            WHERE status IN(1,2,3,4,5,6) AND deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT 30
        ");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = [
                    'id' => '#' . str_pad($row['id'], 8, '0', STR_PAD_LEFT),
                    'total_qty' => $row['total_qty'],
                    'sign' => $row['currency_sign'],
                    'country' => $row['country'],
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
                'sales_this_month' => number_format($totalSalesThisMonth, 2),
                'sales_last_month' => number_format($totalSalesLastMonth, 2),
                'returns' => $totalReturns,
                'live_visitors' => $live
            ],
            'orders' => $orders
        ];

        $filePath = __DIR__ . '/../live_orders.json';
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return "Live orders updated";
    }
}
