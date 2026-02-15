<!-- order-preview.php -->

<?php include "01-header.php";
include "01-menu.php"; ?>

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <h6>Order Preview</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label>Date From</label>
                    <input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label>Date To</label>
                    <input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Order Status</label>
                    <select name="status" class="form-control">
                        <option value="">ALL</option>
                        <?php
                        $statuses = [
                            1 => "New",
                            2 => "Processing",
                            3 => "In Delivery",
                            4 => "Completed",
                            5 => "Return",
                            6 => "Canceled"
                        ];
                        foreach ($statuses as $k => $v) {
                            $selected = ($_GET['status'] ?? '') == $k ? 'selected' : '';
                            echo "<option value='$k' $selected>$v</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sku">SKU</label>
                    <input type="text" name="sku" id="sku" class="form-control" value="<?= $_GET['sku'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Product Name</label>
                    <input type="text" name="product" class="form-control" value="<?= $_GET['product'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Country</label>
                    <select name="country" class="form-control">
                        <option value="">ALL</option>
                        <?php
                        $countries = $conn->query("SELECT DISTINCT country FROM customer_orders WHERE deleted_at IS NULL ORDER BY country");
                        while ($row = $countries->fetch_assoc()) {
                            $selected = ($_GET['country'] ?? '') === $row['country'] ? 'selected' : '';
                            echo "<option value='{$row['country']}' $selected>{$row['country']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end mt-3">
                    <button class="btn btn-primary me-2" type="submit">Filter</button>
                    <a href="export-order-preview.php?<?= http_build_query($_GET) ?>" class="btn btn-success">Export to Excel</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Country</th>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Total (MYR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include "fetch-order-preview.php"; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" style="text-align: right;">Total</th>
                            <th><?= $totalQty ?></th>
                            <th>—</th>
                            <th>RM <?= number_format($totalPrice, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "01-footer.php"; ?>