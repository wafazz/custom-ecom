<?php
include "01-header.php";
include "01-menu.php";
?>


<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12 col-12">

            <div class="row">

                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-lg-6 col-7">
                                <h6>Sales Report</h6>
                            </div>

                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">


                        <form method="get" id="filterForm">
                            <!-- Report Type Selection -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-calendar-range me-1"></i>Report Type
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-bar-chart-fill"></i>
                                    </span>
                                    <select class="form-select" name="type" id="reportType" onchange="changeReportType(this)">
                                        <option value="">-- Select Report Type --</option>
                                        <option value="daily" <?= ($_GET['type'] ?? '') == 'daily' ? 'selected' : '' ?>>
                                            📅 Daily Report
                                        </option>
                                        <option value="weekly" <?= ($_GET['type'] ?? '') == 'weekly' ? 'selected' : '' ?>>
                                            📊 Weekly Report
                                        </option>
                                        <option value="monthly" <?= ($_GET['type'] ?? '') == 'monthly' ? 'selected' : '' ?>>
                                            📈 Monthly Report
                                        </option>
                                        <option value="yearly" <?= ($_GET['type'] ?? '') == 'yearly' ? 'selected' : '' ?>>
                                            📆 Yearly Report
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Daily Date Range -->
                            <?php if (($_GET['type'] ?? '') == 'daily') {
                                $range = $_GET['from'] . ' to ' . $_GET['to'];
                                $params = "&from=" . $_GET['from'] . "&to=" . $_GET['to'];
                            ?>
                                <div class="fade-in">
                                    <label class="form-label">
                                        <i class="bi bi-calendar-check me-1"></i>Date Range
                                    </label>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group">
                                                <label class="form-label" style="display: block;width: 100% !important;">
                                                    <i class="bi bi-calendar-check me-1"></i>From</label>

                                                <span class="input-group-text">
                                                    <i class="bi bi-calendar-check"></i>
                                                </span>
                                                <input type="date"
                                                    class="form-control"
                                                    name="from"
                                                    placeholder="From Date"
                                                    value="<?= $_GET['from'] ?? '' ?>"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group">
                                                <label class="form-label" style="display: block;width: 100% !important;">
                                                    <i class="bi bi-calendar-check me-1"></i>To</label>
                                                <span class="input-group-text">
                                                    <i class="bi bi-calendar-check"></i>
                                                </span>
                                                <input type="date"
                                                    class="form-control"
                                                    name="to"
                                                    placeholder="To Date"
                                                    value="<?= $_GET['to'] ?? '' ?>"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Weekly Selection -->
                            <?php if (($_GET['type'] ?? '') == 'weekly') {
                                $range = $_GET['week'];
                                $params = "&week=" . $_GET['week'];
                            ?>
                                <div class="fade-in">
                                    <label class="form-label">
                                        <i class="bi bi-calendar-week me-1"></i>Select Week
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar3"></i>
                                        </span>
                                        <input type="week"
                                            class="form-control"
                                            name="week"
                                            value="<?= $_GET['week'] ?? '' ?>"
                                            required>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>Select a week to view the report
                                    </small>
                                </div>
                            <?php } ?>

                            <!-- Monthly Selection -->
                            <?php if (($_GET['type'] ?? '') == 'monthly') {

                                $range = $_GET['month'];
                                $params = "&month=" . $_GET['month'];
                            ?>
                                <div class="fade-in">
                                    <label class="form-label">
                                        <i class="bi bi-calendar-month me-1"></i>Select Month
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar3"></i>
                                        </span>
                                        <input type="month"
                                            class="form-control"
                                            name="month"
                                            value="<?= $_GET['month'] ?? '' ?>"
                                            required>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>Select a month to view the report
                                    </small>
                                </div>
                            <?php } ?>

                            <!-- Yearly Selection -->
                            <?php if (($_GET['type'] ?? '') == 'yearly') {
                                $range = $_GET['year'];

                                $params = "&year=" . $_GET['year'];
                            ?>
                                <div class="fade-in">
                                    <label class="form-label">
                                        <i class="bi bi-calendar4 me-1"></i>Select Year
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar3"></i>
                                        </span>
                                        <select class="form-select" name="year" required>
                                            <option value="">-- Select Year --</option>
                                            <?php for ($y = date('Y'); $y >= 2020; $y--) { ?>
                                                <option value="<?= $y ?>" <?= ($_GET['year'] ?? '') == $y ? 'selected' : '' ?>>
                                                    <?= $y ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Action Buttons -->
                            <?php if (!empty($_GET['type'])) { ?>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="?" class="btn btn-reset">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                    </a>
                                    <button type="submit" class="btn btn-filter">
                                        <i class="bi bi-search me-2"></i>Generate Report
                                    </button>
                                </div>
                            <?php } ?>
                        </form>

                        <script>
                            function changeReportType(select) {
                                const type = select.value;
                                if (type) {
                                    // Redirect with only the type parameter
                                    window.location.href = '?type=' + type;
                                } else {
                                    // If empty, redirect to base URL
                                    window.location.href = window.location.pathname;
                                }
                            }
                        </script>

                        <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;overflow-x: auto;">
                            <style>
                                thead th {
                                    text-align: center !important;
                                }

                                #reportTable td {
                                    text-align: center;
                                    vertical-align: top;
                                }

                                /* EXCEPTION: left-align the "name" column (2nd column) */
                                #reportTable td:nth-child(2) {
                                    text-align: left;
                                    vertical-align: top;
                                }
                            </style>

                            <?php

                            if (!empty($_GET['type'] and (!empty($_GET['from']) or !empty($_GET['to']) or !empty($_GET['week'])) or !empty($_GET['month']) or !empty($_GET['year']))) {
                            ?>
                                <style>
                                    .table-container {
                                        background: white;
                                        border-radius: 10px;
                                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                                        overflow: hidden;
                                    }

                                    .table-header {
                                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        color: white;
                                        padding: 20px;
                                    }

                                    .table-header h5 {
                                        margin: 0;
                                        font-weight: 600;
                                    }

                                    .custom-table {
                                        margin: 0;
                                    }

                                    .custom-table thead th {
                                        background: #f8f9fa;
                                        color: #495057;
                                        font-weight: 600;
                                        text-transform: uppercase;
                                        font-size: 0.85rem;
                                        letter-spacing: 0.5px;
                                        border-bottom: 2px solid #dee2e6;
                                        padding: 15px;
                                        vertical-align: middle;
                                    }

                                    .custom-table tbody tr {
                                        transition: all 0.2s ease;
                                    }

                                    .custom-table tbody tr:hover {
                                        background: #f8f9fa;
                                        transform: scale(1.01);
                                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                                    }

                                    .custom-table tbody td {
                                        padding: 15px;
                                        vertical-align: middle;
                                        color: #495057;
                                    }

                                    .order-id {
                                        font-weight: 600;
                                        color: #667eea;
                                        font-family: monospace;
                                    }

                                    .customer-name {
                                        font-weight: 500;
                                        color: #333;
                                    }

                                    .country-badge {
                                        display: inline-block;
                                        padding: 4px 12px;
                                        background: #e9ecef;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: 500;
                                    }

                                    .amount {
                                        font-weight: 600;
                                        color: #28a745;
                                        font-size: 1.05rem;
                                    }

                                    .status-badge {
                                        padding: 6px 14px;
                                        border-radius: 20px;
                                        font-size: 0.8rem;
                                        font-weight: 600;
                                        text-transform: capitalize;
                                        display: inline-block;
                                    }

                                    .status-pending {
                                        background: #fff3cd;
                                        color: #856404;
                                    }

                                    .status-processing {
                                        background: #cfe2ff;
                                        color: #084298;
                                    }

                                    .status-completed {
                                        background: #d1e7dd;
                                        color: #0f5132;
                                    }

                                    .status-shipped {
                                        background: #d1ecf1;
                                        color: #0c5460;
                                    }

                                    .status-cancelled {
                                        background: #f8d7da;
                                        color: #842029;
                                    }

                                    .date-text {
                                        color: #6c757d;
                                        font-size: 0.9rem;
                                    }

                                    .no-data {
                                        text-align: center;
                                        padding: 40px;
                                        color: #6c757d;
                                    }

                                    .no-data i {
                                        font-size: 3rem;
                                        opacity: 0.3;
                                        display: block;
                                        margin-bottom: 15px;
                                    }

                                    @media (max-width: 768px) {
                                        .table-responsive {
                                            border-radius: 0;
                                        }

                                        .custom-table {
                                            font-size: 0.85rem;
                                        }

                                        .custom-table thead th,
                                        .custom-table tbody td {
                                            padding: 10px;
                                        }
                                    }
                                </style>
                                <div class="table-responsive">
                                    <h4>Sales (<?php echo strtoupper(htmlspecialchars($_GET['type'] ?? 'All')); ?> - <?php echo htmlspecialchars($range ?? 'All'); ?>): RM <?= number_format($sumResult['total_sales'], 2) ?></h4>
                                    <a class="btn btn-success" href="download_excel.php?type=<?php echo htmlspecialchars($_GET['type'] ?? ''); ?><?php echo $params ?? ''; ?>" target="_blank">
                                        <i class="bi bi-download"></i> Download Excel
                                    </a>
                                    <table class="table custom-table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th><i class="bi bi-hash me-1"></i>Order ID</th>
                                                <th><i class="bi bi-person me-1"></i>Customer</th>
                                                <th><i class="bi bi-geo-alt me-1"></i>Country</th>
                                                <th><i class="bi bi-currency-exchange me-1"></i>Total (MYR)</th>
                                                <th><i class="bi bi-info-circle me-1"></i>Status</th>
                                                <th><i class="bi bi-calendar me-1"></i>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $hasData = false;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $hasData = true;
                                                // Determine status class
                                                $statusClass = 'status-pending';
                                                $statusLower = strtolower($row['status']);

                                                if (strpos($statusLower, 'completed') !== false || strpos($statusLower, 'delivered') !== false) {
                                                    $statusClass = 'status-completed';
                                                } elseif (strpos($statusLower, 'shipped') !== false || strpos($statusLower, 'shipping') !== false) {
                                                    $statusClass = 'status-shipped';
                                                } elseif (strpos($statusLower, 'processing') !== false || strpos($statusLower, 'confirmed') !== false) {
                                                    $statusClass = 'status-processing';
                                                } elseif (strpos($statusLower, 'cancelled') !== false || strpos($statusLower, 'canceled') !== false) {
                                                    $statusClass = 'status-cancelled';
                                                }

                                                // Format date
                                                $date = date('d M Y, h:i A', strtotime($row['created_at']));
                                            ?>
                                                <tr>
                                                    <td>
                                                        <span class="order-id">#<?= $row['id'] ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="customer-name"><?= htmlspecialchars($row['customer_name']) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="country-badge">
                                                            <i class="bi bi-flag me-1"></i><?= htmlspecialchars($row['country']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="amount">RM <?= number_format($row['myr_value_include_postage'], 2) ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($row['status'] == '1') {
                                                            $statusText = "New Order";
                                                            $classText = "btn btn-primary";
                                                        } else if ($row['status'] == '2') {
                                                            $statusText = "Processing";
                                                            $classText = "btn btn-secondary";
                                                        } else if ($row['status'] == '3') {
                                                            $statusText = "In Delivery";
                                                            $classText = "btn btn-info";
                                                        } else if ($row['status'] == '4') {
                                                            $statusText = "Completed";
                                                            $classText = "btn btn-success";
                                                        }
                                                        ?>
                                                        <span class="<?= htmlspecialchars($classText) ?>"><?= htmlspecialchars($statusText) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="date-text">
                                                            <i class="bi bi-clock me-1"></i><?= $date ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <?php if (!$hasData) { ?>
                                                <tr>
                                                    <td colspan="6" class="no-data">
                                                        <i class="bi bi-inbox"></i>
                                                        <p class="mb-0">No orders found</p>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                            <?php
                            }

                            ?>



                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <?php
    include "01-footer.php";
    ?>