<?php
$conn = getDbConnection();
mysqli_set_charset($conn, "utf8mb4");

function fetchSum($conn, $start, $end) {
    $sql = "
        SELECT 
            COALESCE(SUM(myr_value_include_postage),0) as sales,
            COALESCE(SUM(myr_value_without_postage),0) as revenue
        FROM customer_orders
        WHERE created_at >= '$start'
        AND created_at < '$end'
        AND status IN (1,2,3,4)
        AND deleted_at IS NULL
    ";
    $res = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($res) ?: ['sales'=>0,'revenue'=>0];
}

function compare($current, $previous) {
    $diff = $current - $previous;
    $pct = $previous > 0 ? ($diff / $previous) * 100 : 0;
    return [
        'current'=>$current,
        'previous'=>$previous,
        'difference'=>$diff,
        'percentage'=>$pct,
        'trend'=>$diff >= 0 ? 'up' : 'down'
    ];
}

function money($v) {
    return 'RM ' . number_format($v, 2);
}

function pct($v) {
    return ($v >= 0 ? '+' : '') . number_format($v, 2) . '%';
}

function buildDateFilter($type, $input) {
    $where = [];

    if ($type === 'daily') {
        $from = $input['from'] ?? null;
        $to   = $input['to'] ?? null;

        // if (!$from || !$to) {
        //     die('Invalid daily range');
        // }

        $days = (strtotime($to) - strtotime($from)) / 86400;

        // if ($days < 0 || $days > 31) {
        //     die('Daily range max 31 days only');
        // }

        $where[] = "created_at >= '$from'";
        $where[] = "created_at < DATE_ADD('$to', INTERVAL 1 DAY)";
    }

    if ($type === 'weekly') {
        $week = $input['week'] ?? null;

        // if (!$week || !str_contains($week, '-W')) {
        //     die('Invalid week');
        // }

        [$year, $weekNum] = explode('-W', $week);

        $start = date('Y-m-d', strtotime($year . 'W' . $weekNum . '1'));
        $end   = date('Y-m-d', strtotime($start . ' +7 days'));

        $where[] = "created_at >= '$start'";
        $where[] = "created_at < '$end'";
    }

    if ($type === 'monthly') {
        $month = $input['month'] ?? null;

        // if (!$month) {
        //     die('Invalid month');
        // }

        $start = $month . '-01';
        $end   = date('Y-m-d', strtotime("$start +1 month"));

        $where[] = "created_at >= '$start'";
        $where[] = "created_at < '$end'";
    }

    if ($type === 'yearly') {
        $year = (int)($input['year'] ?? 0);

        // if ($year < 2000) {
        //     die('Invalid year');
        // }

        $start = $year . '-01-01';
        $end   = ($year + 1) . '-01-01';

        $where[] = "created_at >= '$start'";
        $where[] = "created_at < '$end'";
    }

    return $where;
}