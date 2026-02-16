<?php
include "01-header.php";
include "01-menu.php";
?>



<!-- End Navbar -->
<div class="container-fluid py-4">
  <div class="row">
    <h6 class="mb-0">Welcome back user
      <?= $_SESSION['user']->f_name . " " . $_SESSION['user']->l_name; ?>
    </h6>
  </div>
</div>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-12 col-12">
      <div class="row">


        <div class="col-lg-3 col-md-6 col-12 mt-4 mt-md-0">
          <div class="card">
            <span class="mask bg-info opacity-10 border-radius-lg"></span>
            <div class="card-body p-3 position-relative">
              <div class="row">
                <div class="col-8 text-start">
                  <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                    <i class="fa-solid fa-database text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                  <h5 class="text-white font-weight-bolder mb-0 mt-3">
                    <?= totalProduct(); ?>
                  </h5>
                  <span class="text-white text-sm">Total Product</span>
                </div>
                <div class="col-4">
                  <div class="dropstart text-end mb-6">
                    <a href="javascript:;" class="cursor-pointer" id="dropdownUsers2" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="fa fa-ellipsis-h text-white"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers2">
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                  <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">+124%</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mt-4 mt-md-0">
          <div class="card">
            <span class="mask bg-info opacity-10 border-radius-lg"></span>
            <div class="card-body p-3 position-relative">
              <div class="row">
                <div class="col-8 text-start">
                  <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                    <i class="fa-solid fa-users text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                  <h5 class="text-white font-weight-bolder mb-0 mt-3">
                    <!-- <?= totalOrder() ?> -->
                    <span id="totalOrders">0</span>
                  </h5>
                  <span class="text-white text-sm">Total Order</span>
                </div>
                <div class="col-4">
                  <div class="dropdown text-end mb-6">
                    <a href="javascript:;" class="cursor-pointer" id="dropdownUsers1" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="fa fa-ellipsis-h text-white"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers1">
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                  <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">+55%</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mt-4 mt-md-0">
          <div class="card">
            <span class="mask bg-info opacity-10 border-radius-lg"></span>
            <div class="card-body p-3 position-relative">
              <div class="row">
                <div class="col-8 text-start">
                  <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                    <i class="fa-solid fa-right-left text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                  <h5 class="text-white font-weight-bolder mb-0 mt-3">
                    <!-- <?= totalOrderReturn() ?> -->
                    <span id="returnsCount">0</span>
                  </h5>
                  <span class="text-white text-sm">Return Order</span>
                </div>
                <div class="col-4">
                  <div class="dropstart text-end mb-6">
                    <a href="javascript:;" class="cursor-pointer" id="dropdownUsers4" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="fa fa-ellipsis-h text-white"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers4">
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                  <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">+90%</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top: 20px !important;">
          <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
            <div class="card">
              <span class="mask bg-info opacity-10 border-radius-lg"></span>
              <div class="card-body p-3 position-relative">
                <div class="row">
                  <div class="col-8 text-start">
                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                      <i class="fa-solid fa-circle-dollar-to-slot text-dark text-gradient text-lg opacity-10"
                        aria-hidden="true"></i>
                    </div>
                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                      RM
                      <!-- <?= totalSales() ?> -->
                      <span id="totalSales">0.00</span>
                    </h5>
                    <span class="text-white text-sm">Total Sales</span>
                  </div>
                  <div class="col-4">
                    <div class="dropdown text-end mb-6">
                      <a href="javascript:;" class="cursor-pointer" id="dropdownUsers3" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-ellipsis-h text-white"></i>
                      </a>
                      <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers3">
                        <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                        <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                        <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                      </ul>
                    </div>
                    <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">+15%</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
            <div class="card">
              <span class="mask bg-info opacity-10 border-radius-lg"></span>
              <div class="card-body p-3 position-relative">
                <div class="row">
                  <div class="col-8 text-start">
                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                      <i class="fa-solid fa-circle-dollar-to-slot text-dark text-gradient text-lg opacity-10"
                        aria-hidden="true"></i>
                    </div>
                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                      RM
                      <!-- <?= totalSales() ?> -->
                      <span id="totalSalesThisMonth">0.00</span>
                    </h5>
                    <span class="text-white text-sm">This Month Sales</span>
                  </div>
                  <div class="col-4">
                    <div class="dropdown text-end mb-6">
                      <a href="javascript:;" class="cursor-pointer" id="dropdownUsers3" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-ellipsis-h text-white"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
            <div class="card">
              <span class="mask bg-info opacity-10 border-radius-lg"></span>
              <div class="card-body p-3 position-relative">
                <div class="row">
                  <div class="col-8 text-start">
                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                      <i class="fa-solid fa-circle-dollar-to-slot text-dark text-gradient text-lg opacity-10"
                        aria-hidden="true"></i>
                    </div>
                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                      RM
                      <!-- <?= totalSales() ?> -->
                      <span id="totalSalesLastMonth">0.00</span>
                    </h5>
                    <span class="text-white text-sm">Last Month Sales</span>
                  </div>
                  <div class="col-4">
                    <div class="dropdown text-end mb-6">
                      <a href="javascript:;" class="cursor-pointer" id="dropdownUsers3" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-ellipsis-h text-white"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12 col-12" style="margin-top: 20px !important;">
      <style>
        .blink {
          animation: blink 1s steps(2, start) infinite;
        }

        @keyframes blink {
          to {
            visibility: hidden;
          }
        }
      </style>
      <h3>Today Sales (<?= date("Y-m-d") ?>)<br><span id="liveView" style="font-size: 15px;
    background: chartreuse;
    padding: 5px 10px;
    border-radius: 10px;
    display: block;
    width: fit-content;
    color: red;"><i class="fa-solid fa-eye"></i> 0 <span class="blink">LIVE</span></span></h3>
      <div class="row">



        <div class="col-lg-6 col-md-6 col-12 mt-4 mt-md-0">
          <div class="card">
            <span class="mask bg-info opacity-10 border-radius-lg"></span>
            <div class="card-body p-3 position-relative">
              <div class="row">
                <div class="col-8 text-start">
                  <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                    <i class="fa-solid fa-users text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                  <h5 class="text-white font-weight-bolder mb-0 mt-3">
                    <!-- <?= totalOrder() ?> -->
                    <span id="totalOrdersToday">0</span>
                  </h5>
                  <span class="text-white text-sm">Total Order</span>
                </div>
                <div class="col-4">
                  <div class="dropdown text-end mb-6">
                    <a href="javascript:;" class="cursor-pointer" id="dropdownUsers1" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="fa fa-ellipsis-h text-white"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers1">
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                  <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">+55%</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12 mt-4 mt-md-0">
          <div class="card">
            <span class="mask bg-info opacity-10 border-radius-lg"></span>
            <div class="card-body p-3 position-relative">
              <div class="row">
                <div class="col-8 text-start">
                  <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                    <i class="fa-solid fa-circle-dollar-to-slot text-dark text-gradient text-lg opacity-10"
                      aria-hidden="true"></i>
                  </div>
                  <h5 class="text-white font-weight-bolder mb-0 mt-3">
                    RM
                    <!-- <?= totalSales() ?> -->
                    <span id="totalSalesToday">0.00</span>
                  </h5>
                  <span class="text-white text-sm">Total Sales</span>
                </div>
                <div class="col-4">
                  <div class="dropdown text-end mb-6">
                    <a href="javascript:;" class="cursor-pointer" id="dropdownUsers3" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="fa fa-ellipsis-h text-white"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers3">
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                  <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">+15%</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- <div class="col-lg-6 col-12 mt-4 mt-lg-0">
          <div class="card shadow h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Reviews</h6>
            </div>
            <div class="card-body pb-0 p-3">
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                  <div class="w-100">
                    <div class="d-flex mb-2">
                      <span class="me-2 text-sm font-weight-bold text-dark"><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i></span>
                      <span class="ms-auto text-sm font-weight-bold">80%</span>
                    </div>
                    <div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-primary w-80" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                  <div class="w-100">
                    <div class="d-flex mb-2">
                      <span class="me-2 text-sm font-weight-bold text-dark"><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i></span>
                      <span class="ms-auto text-sm font-weight-bold">80%</span>
                    </div>
                    <div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-primary w-80" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                  <div class="w-100">
                    <div class="d-flex mb-2">
                      <span class="me-2 text-sm font-weight-bold text-dark"><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i></span>
                      <span class="ms-auto text-sm font-weight-bold">80%</span>
                    </div>
                    <div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-primary w-80" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                  <div class="w-100">
                    <div class="d-flex mb-2">
                      <span class="me-2 text-sm font-weight-bold text-dark"><i class="fa-solid fa-star star-yellow"></i><i class="fa-solid fa-star star-yellow"></i></span>
                      <span class="ms-auto text-sm font-weight-bold">80%</span>
                    </div>
                    <div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-primary w-80" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                  <div class="w-100">
                    <div class="d-flex mb-2">
                      <span class="me-2 text-sm font-weight-bold text-dark"><i class="fa-solid fa-star star-yellow"></i></span>
                      <span class="ms-auto text-sm font-weight-bold">80%</span>
                    </div>
                    <div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-primary w-80" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </li>
                
              </ul>
            </div>
            <div class="card-footer pt-0 p-3 d-flex align-items-center">
              <div class="w-60">
                <p class="text-sm">
                  More than <b>1,500,000</b> developers used Creative Tim's products and over <b>700,000</b> projects were created.
                </p>
              </div>
              <div class="w-40 text-end">
                <a class="btn btn-dark mb-0 text-end" href="javascript:;">View all reviews</a>
              </div>
            </div>
          </div>
        </div> -->
  </div>
  <div class="row my-4">
    <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
      <div class="card">
        <div class="card-header pb-0">
          <div class="row">
            <div class="col-lg-6 col-7">
              <h6>Latest Order/s</h6>
              <p class="text-sm mb-0">
                <i class="fa fa-check text-info" aria-hidden="true"></i>
                <span class="font-weight-bold ms-1">30</span> (latest)
              </p>
            </div>
            <div class="col-lg-6 col-5 my-auto text-end">
              <div class="dropdown float-lg-end pe-4">
                <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-ellipsis-v text-secondary"></i>
                </a>
                <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                  <li><a class="dropdown-item border-radius-md" href="<?= $domainURL; ?>new-order">View All</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order ID</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer's Name
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Country
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action
                  </th>
                </tr>
              </thead>
              <tbody id="orderList"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


    <div class="col-lg-4 col-md-6">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h6>Activity overview</h6>
        </div>
        <div class="card-body p-3" style="max-height: 800px;
    overflow-y: auto;
    margin-bottom: 20px;">
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
              echo "No records found.";
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