<?php
include "01-header.php";
include "01-menu.php";
?>

<style>
  .dash-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .dash-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.15);
  }
  .dash-card .card-body {
    padding: 1.5rem;
    position: relative;
    z-index: 1;
  }
  .dash-card .dash-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 1rem;
  }
  .dash-card .dash-value {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
  }
  .dash-card .dash-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.85;
  }
  .dash-card .dash-link {
    position: absolute;
    top: 1rem;
    right: 1rem;
    opacity: 0.7;
    font-size: 0.85rem;
    z-index: 2;
    text-decoration: none;
  }
  .dash-card .dash-link:hover { opacity: 1; }

  .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
  .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
  .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
  .bg-gradient-dark { background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); }
  .bg-gradient-ocean { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); }
  .bg-gradient-sunset { background: linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%); }

  .blink {
    animation: blink 1s steps(2, start) infinite;
  }
  @keyframes blink {
    to { visibility: hidden; }
  }
  .live-badge {
    font-size: 13px;
    background: #e8f5e9;
    color: #2e7d32;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
  }
  .live-badge .blink { color: #d32f2f; }
  .section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #344767;
    margin-bottom: 0.25rem;
  }
  .section-sub {
    font-size: 0.8rem;
    color: #8392ab;
  }
</style>

<!-- End Navbar -->
<div class="container-fluid py-4">
  <div class="row mb-2">
    <div class="col-12 d-flex align-items-center justify-content-between">
      <div>
        <h5 class="mb-1" style="color:#344767;">Welcome back, <?= $_SESSION['user']->f_name . " " . $_SESSION['user']->l_name; ?></h5>
        <p class="text-sm text-secondary mb-0">Here's what's happening with your store.</p>
      </div>
      <span id="liveView" class="live-badge"><i class="fa-solid fa-eye"></i> 0 <span class="blink">LIVE</span></span>
    </div>
  </div>

  <!-- Overview Cards -->
  <div class="row mb-2">
    <div class="col-12">
      <p class="section-title">Overview</p>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card dash-card bg-gradient-primary text-white h-100">
        <a href="<?= $domainURL; ?>product" class="dash-link text-white"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <div class="card-body">
          <div class="dash-icon" style="background:rgba(255,255,255,0.2);">
            <i class="fa-solid fa-box-open text-white"></i>
          </div>
          <div class="dash-value"><?= totalProduct(); ?></div>
          <div class="dash-label text-white">Total Products</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card dash-card bg-gradient-success text-white h-100">
        <a href="<?= $domainURL; ?>new-order" class="dash-link text-white"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <div class="card-body">
          <div class="dash-icon" style="background:rgba(255,255,255,0.2);">
            <i class="fa-solid fa-cart-shopping text-white"></i>
          </div>
          <div class="dash-value"><span id="totalOrders">0</span></div>
          <div class="dash-label text-white">Total Orders</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card dash-card bg-gradient-warning text-white h-100">
        <a href="<?= $domainURL; ?>returned-order" class="dash-link text-white"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <div class="card-body">
          <div class="dash-icon" style="background:rgba(255,255,255,0.2);">
            <i class="fa-solid fa-rotate-left text-white"></i>
          </div>
          <div class="dash-value"><span id="totalReturns">0</span></div>
          <div class="dash-label text-white">Returned Orders</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card dash-card bg-gradient-dark text-white h-100">
        <div class="card-body">
          <div class="dash-icon" style="background:rgba(255,255,255,0.15);">
            <i class="fa-solid fa-sack-dollar text-white"></i>
          </div>
          <div class="dash-value">RM <span id="totalSales">0.00</span></div>
          <div class="dash-label text-white">Total Sales (All Time)</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sales Breakdown -->
  <div class="row mb-2 mt-2">
    <div class="col-12">
      <p class="section-title">Sales Breakdown</p>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card dash-card h-100" style="background:#fff;border:1px solid #e9ecef;">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="dash-icon mb-0" style="background:#e3f2fd;color:#1565c0;">
              <i class="fa-solid fa-calendar-day"></i>
            </div>
            <span class="badge" style="background:#e3f2fd;color:#1565c0;">This Month</span>
          </div>
          <div class="dash-value" style="color:#344767;">RM <span id="totalSalesThisMonth">0.00</span></div>
          <div class="dash-label" style="color:#8392ab;">Sales This Month</div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card dash-card h-100" style="background:#fff;border:1px solid #e9ecef;">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="dash-icon mb-0" style="background:#fce4ec;color:#c62828;">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <span class="badge" style="background:#fce4ec;color:#c62828;">Last Month</span>
          </div>
          <div class="dash-value" style="color:#344767;">RM <span id="totalSalesLastMonth">0.00</span></div>
          <div class="dash-label" style="color:#8392ab;">Sales Last Month</div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-12 mb-4">
      <div class="card dash-card bg-gradient-ocean text-white h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="dash-icon mb-0" style="background:rgba(255,255,255,0.2);">
              <i class="fa-solid fa-bolt text-white"></i>
            </div>
            <span class="badge" style="background:rgba(255,255,255,0.25);">Today</span>
          </div>
          <div class="dash-value">RM <span id="totalSalesToday">0.00</span></div>
          <div class="dash-label text-white">Today's Sales</div>
          <hr style="border-color:rgba(255,255,255,0.3);margin:0.75rem 0;">
          <div class="d-flex align-items-center justify-content-between">
            <span class="text-white" style="font-size:0.8rem;">Orders Today</span>
            <span class="text-white" style="font-size:1.1rem;font-weight:700;"><span id="totalOrdersToday">0</span></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Latest Orders & Activity -->
  <div class="row">
    <div class="col-lg-8 col-md-6 mb-4">
      <div class="card dash-card h-100" style="border:1px solid #e9ecef;">
        <div class="card-header pb-0" style="border-bottom:1px solid #f0f0f0;">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h6 class="mb-0" style="color:#344767;">Latest Orders</h6>
              <p class="text-sm text-secondary mb-0">Last 30 orders</p>
            </div>
            <a href="<?= $domainURL; ?>new-order" class="btn btn-sm mb-0" style="background:#667eea;color:#fff;border-radius:8px;">View All</a>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order ID</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer's Name</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Country</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                </tr>
              </thead>
              <tbody id="orderList"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
      <div class="card dash-card h-100" style="border:1px solid #e9ecef;">
        <div class="card-header pb-0" style="border-bottom:1px solid #f0f0f0;">
          <h6 class="mb-0" style="color:#344767;">Activity Overview</h6>
        </div>
        <div class="card-body p-3" style="max-height:600px;overflow-y:auto;">
          <div class="timeline timeline-one-side">
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
            ?>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-bell-55 text-success text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0"><?= "(Staff ID: #" . str_pad($row["member_id"], 6, "0", STR_PAD_LEFT) . ") - " . $row["f_name"] . " " . $row["l_name"] ?></h6>
                    <h6 class="text-warning text-sm font-weight-bold mb-0"><?= $row["description"]  ?></h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0"><?= date("dS M Y, h:iA", strtotime($row["activity_created"]));  ?></p>
                  </div>
                </div>
            <?php
              }
            } else {
              echo "<p class='text-secondary text-sm'>No activity records found.</p>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>


  <?php
  include "01-footer.php";
  ?>