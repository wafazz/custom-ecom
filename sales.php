<?php
require_once("config/mainConfig.php");

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

$todayStart = date('Y-m-d');
$todayEnd = date('Y-m-d', strtotime('+1 day'));

$yesterdayStart = date('Y-m-d', strtotime('-1 day'));
$yesterdayEnd = $todayStart;

$thisWeekStart = date('Y-m-d', strtotime('monday this week'));
$thisWeekEnd = $todayEnd;

$lastWeekStart = date('Y-m-d', strtotime('monday last week'));
$lastWeekEnd = $thisWeekStart;

$thisMonthStart = date('Y-m-01');
$thisMonthEnd = $todayEnd;

$lastMonthStart = date('Y-m-01', strtotime('-1 month'));
$lastMonthEnd = $thisMonthStart;

$thisYearStart = date('Y-01-01');
$thisYearEnd = $todayEnd;

$lastYearStart = date('Y-01-01', strtotime('-1 year'));
$lastYearEnd = $thisYearStart;

$today = fetchSum($conn,$todayStart,$todayEnd);
$yesterday = fetchSum($conn,$yesterdayStart,$yesterdayEnd);

$thisWeek = fetchSum($conn,$thisWeekStart,$thisWeekEnd);
$lastWeek = fetchSum($conn,$lastWeekStart,$lastWeekEnd);

$thisMonth = fetchSum($conn,$thisMonthStart,$thisMonthEnd);
$lastMonth = fetchSum($conn,$lastMonthStart,$lastMonthEnd);

$thisYear = fetchSum($conn,$thisYearStart,$thisYearEnd);
$lastYear = fetchSum($conn,$lastYearStart,$lastYearEnd);

$daySales = compare($today['sales'],$yesterday['sales']);
$dayRevenue = compare($today['revenue'],$yesterday['revenue']);

$weekSales = compare($thisWeek['sales'],$lastWeek['sales']);
$weekRevenue = compare($thisWeek['revenue'],$lastWeek['revenue']);

$monthSales = compare($thisMonth['sales'],$lastMonth['sales']);
$monthRevenue = compare($thisMonth['revenue'],$lastMonth['revenue']);

$yearSales = compare($thisYear['sales'],$lastYear['sales']);
$yearRevenue = compare($thisYear['revenue'],$lastYear['revenue']);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Sales Report</title>
<style>
body{font-family:sans-serif;background:#667eea;padding:20px}
.container{max-width:1200px;margin:auto}
h1{color:#fff;text-align:center;margin-bottom:30px}
.section{background:#fff;border-radius:15px;padding:25px;margin-bottom:25px}
.section-title{font-size:1.5em;border-bottom:3px solid #667eea;padding-bottom:10px;margin-bottom:20px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px}
.card{background:#f5f7fa;border-radius:10px;padding:20px;position:relative}
.card:before{content:'';position:absolute;left:0;top:0;width:5px;height:100%;background:#667eea}
.label{font-size:.8em;color:#666;text-transform:uppercase}
.value{font-size:2em;font-weight:bold;margin:10px 0}
.compare{display:flex;justify-content:space-between;border-top:1px solid #ddd;padding-top:10px}
.trend{margin-top:10px;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:.9em}
.trend.up{background:#d4edda;color:#155724}
.trend.down{background:#f8d7da;color:#721c24}
</style>
</head>
<body>
<div class="container">
<h1>📊 Sales & Revenue Report</h1>

<?php
function block($title,$sales,$revenue,$l1,$l2){
echo "
<div class='section'>
<h2 class='section-title'>$title</h2>
<div class='grid'>
<div class='card'>
<div class='label'>Sales</div>
<div class='value'>".money($sales['current'])."</div>
<div class='compare'><div>$l1<br>".money($sales['current'])."</div><div>$l2<br>".money($sales['previous'])."</div></div>
<div class='trend {$sales['trend']}'>".money(abs($sales['difference']))." (".pct($sales['percentage']).")</div>
</div>
<div class='card'>
<div class='label'>Revenue</div>
<div class='value'>".money($revenue['current'])."</div>
<div class='compare'><div>$l1<br>".money($revenue['current'])."</div><div>$l2<br>".money($revenue['previous'])."</div></div>
<div class='trend {$revenue['trend']}'>".money(abs($revenue['difference']))." (".pct($revenue['percentage']).")</div>
</div>
</div>
</div>";
}

block('📊 Daily Comparison',$daySales,$dayRevenue,'Today','Yesterday');
block('📆 Weekly Comparison',$weekSales,$weekRevenue,'This Week','Last Week');
block('📅 Monthly Comparison',$monthSales,$monthRevenue,'This Month','Last Month');
block('📈 Yearly Comparison',$yearSales,$yearRevenue,'This Year','Last Year');
?>

</div>
</body>
</html>