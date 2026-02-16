<?php
include "01-header.php";
include "01-menu.php";
?>

<style>
  .stats-header {
    color: #344767;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  .stats-sub {
    color: #8392ab;
    font-size: 0.85rem;
  }
  .stats-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #344767;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
  }
  .stat-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
  }
  .stat-card .card-body { padding: 1.5rem; }
  .stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
  }
  .stat-card .stat-type {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.4rem;
  }
  .stat-card .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    line-height: 1.2;
  }
  .stat-compare {
    display: flex;
    gap: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(0,0,0,0.08);
    margin-bottom: 0.75rem;
  }
  .stat-compare-item {
    flex: 1;
  }
  .stat-compare-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    opacity: 0.7;
    margin-bottom: 0.15rem;
  }
  .stat-compare-value {
    font-size: 0.95rem;
    font-weight: 600;
  }
  .stat-trend {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  .stat-trend.up {
    background: rgba(17,153,142,0.12);
    color: #11998e;
  }
  .stat-trend.down {
    background: rgba(245,87,108,0.12);
    color: #f5576c;
  }
  .stat-trend .trend-icon { font-size: 0.7rem; }

  .bg-grad-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
  .bg-grad-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
  .bg-grad-blue { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); }
  .bg-grad-pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
  .bg-grad-orange { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
  .bg-grad-dark { background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); }
  .bg-grad-teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
  .bg-grad-red { background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%); }

  .white-card {
    background: #fff;
    border: 1px solid #e9ecef;
  }
  .white-card .stat-type { color: #8392ab; }
  .white-card .stat-value { color: #344767; }
  .white-card .stat-compare-label { color: #8392ab; }
  .white-card .stat-compare-value { color: #344767; }

  .gradient-card { color: #fff; }
  .gradient-card .stat-type { color: rgba(255,255,255,0.8); }
  .gradient-card .stat-compare { border-top-color: rgba(255,255,255,0.2); }
  .gradient-card .stat-compare-label { color: rgba(255,255,255,0.7); }
  .gradient-card .stat-trend.up { background: rgba(255,255,255,0.2); color: #fff; }
  .gradient-card .stat-trend.down { background: rgba(255,255,255,0.2); color: #fff; }
</style>

<div class="container-fluid py-4">
  <div class="row mb-3">
    <div class="col-12">
      <h5 class="stats-header">Sales & Revenue Report</h5>
      <p class="stats-sub">Compare your sales and revenue across different time periods.</p>
    </div>
  </div>

  <?php
  function statCard($type, $data, $currentLabel, $previousLabel, $iconClass, $gradientClass, $iconBg)
  {
      $trendIcon = $data['trend'] === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
      $trendClass = $data['trend'];
      $isGradient = $gradientClass !== '' ? 'gradient-card' : 'white-card';
      $cardBg = $gradientClass !== '' ? $gradientClass : '';
      $iconStyle = $gradientClass !== ''
          ? 'background:rgba(255,255,255,0.2);color:#fff;'
          : "background:$iconBg;";
      ?>
      <div class="card stat-card <?= $isGradient ?> <?= $cardBg ?> h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="stat-icon" style="<?= $iconStyle ?>">
              <i class="fa-solid <?= $iconClass ?>"></i>
            </div>
            <span class="stat-type"><?= $type ?></span>
          </div>
          <div class="stat-value"><?= money($data['current']) ?></div>
          <div class="stat-compare">
            <div class="stat-compare-item">
              <div class="stat-compare-label"><?= $currentLabel ?></div>
              <div class="stat-compare-value"><?= money($data['current']) ?></div>
            </div>
            <div class="stat-compare-item text-end">
              <div class="stat-compare-label"><?= $previousLabel ?></div>
              <div class="stat-compare-value"><?= money($data['previous']) ?></div>
            </div>
          </div>
          <div class="stat-trend <?= $trendClass ?>">
            <i class="fa-solid <?= $trendIcon ?> trend-icon"></i>
            <?= money(abs($data['difference'])) ?> (<?= pct($data['percentage']) ?>)
          </div>
        </div>
      </div>
      <?php
  }
  ?>

  <!-- Daily -->
  <div class="row mb-2">
    <div class="col-12">
      <p class="stats-section-title"><i class="fa-solid fa-sun me-2"></i>Daily Comparison</p>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-6 mb-4">
      <?php statCard('Sales', $daySales, 'Today', 'Yesterday', 'fa-receipt', 'bg-grad-purple', ''); ?>
    </div>
    <div class="col-md-6 mb-4">
      <?php statCard('Revenue', $dayRevenue, 'Today', 'Yesterday', 'fa-coins', 'bg-grad-green', ''); ?>
    </div>
  </div>

  <!-- Weekly -->
  <div class="row mb-2">
    <div class="col-12">
      <p class="stats-section-title"><i class="fa-solid fa-calendar-week me-2"></i>Weekly Comparison</p>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-6 mb-4">
      <?php statCard('Sales', $weekSales, 'This Week', 'Last Week', 'fa-receipt', '', '#e3f2fd;color:#1565c0'); ?>
    </div>
    <div class="col-md-6 mb-4">
      <?php statCard('Revenue', $weekRevenue, 'This Week', 'Last Week', 'fa-coins', '', '#e8f5e9;color:#2e7d32'); ?>
    </div>
  </div>

  <!-- Monthly -->
  <div class="row mb-2">
    <div class="col-12">
      <p class="stats-section-title"><i class="fa-solid fa-calendar-days me-2"></i>Monthly Comparison</p>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-6 mb-4">
      <?php statCard('Sales', $monthSales, 'This Month', 'Last Month', 'fa-receipt', 'bg-grad-blue', ''); ?>
    </div>
    <div class="col-md-6 mb-4">
      <?php statCard('Revenue', $monthRevenue, 'This Month', 'Last Month', 'fa-coins', 'bg-grad-pink', ''); ?>
    </div>
  </div>

  <!-- Yearly -->
  <div class="row mb-2">
    <div class="col-12">
      <p class="stats-section-title"><i class="fa-solid fa-chart-line me-2"></i>Yearly Comparison</p>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-6 mb-4">
      <?php statCard('Sales', $yearSales, 'This Year', 'Last Year', 'fa-receipt', '', '#fce4ec;color:#c62828'); ?>
    </div>
    <div class="col-md-6 mb-4">
      <?php statCard('Revenue', $yearRevenue, 'This Year', 'Last Year', 'fa-coins', '', '#fff3e0;color:#e65100'); ?>
    </div>
  </div>

</div>

<?php
include "01-footer.php";
?>
