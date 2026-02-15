<?php
include "01-header.php";
include "01-menu.php";
?>

<style>
body{font-family:sans-serif;background:#667eea;padding:20px}
.container{max-width:1200px;margin:auto}
h1{color:#000;text-align:center;margin-bottom:30px}
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
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="row">


                <h1>📊 Sales & Revenue Report</h1>

                <?php
                function block($title, $sales, $revenue, $l1, $l2)
                {
                    echo "
<div class='section'>
<h2 class='section-title'>$title</h2>
<div class='grid'>
<div class='card'>
<div class='label'>Sales</div>
<div class='value'>" . money($sales['current']) . "</div>
<div class='compare'><div>$l1<br>" . money($sales['current']) . "</div><div>$l2<br>" . money($sales['previous']) . "</div></div>
<div class='trend {$sales['trend']}'>" . money(abs($sales['difference'])) . " (" . pct($sales['percentage']) . ")</div>
</div>
<div class='card'>
<div class='label'>Revenue</div>
<div class='value'>" . money($revenue['current']) . "</div>
<div class='compare'><div>$l1<br>" . money($revenue['current']) . "</div><div>$l2<br>" . money($revenue['previous']) . "</div></div>
<div class='trend {$revenue['trend']}'>" . money(abs($revenue['difference'])) . " (" . pct($revenue['percentage']) . ")</div>
</div>
</div>
</div>";
                }

                block('📊 Daily Comparison', $daySales, $dayRevenue, 'Today', 'Yesterday');
                block('📆 Weekly Comparison', $weekSales, $weekRevenue, 'This Week', 'Last Week');
                block('📅 Monthly Comparison', $monthSales, $monthRevenue, 'This Month', 'Last Month');
                block('📈 Yearly Comparison', $yearSales, $yearRevenue, 'This Year', 'Last Year');
                ?>

            </div>
        </div>

    </div>



    <?php
    include "01-footer.php";
    ?>