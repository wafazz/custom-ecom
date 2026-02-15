<?php
include "e-header-keya88.php";
include "e-menu-keya88.php";
?>
<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?= $domainURL ?>main"><i class="fa fa-home"></i> Home</a>
                    <span>Order -
                        #
                        <?= str_pad($orderID, 8, "0", STR_PAD_LEFT); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->
<!-- Shop Cart Section Begin -->
<section class="shop-cart spad" style="padding-bottom: 0px !important;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">


                <div class="row property__gallery" style="padding: 5px !important;">


                    <h4 class="mb-3">Order Details</h4>

                    <!-- Buyer Details -->
                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">🧑 Buyer Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width:200px;max-width:200px;">Name</th>
                                <td>
                                    <?= htmlspecialchars($rows['customer_name']) ?>
                                </td>
                            </tr>
                            <tr>

                                <th style="width:200px;max-width:200px;">Email</th>
                                <td>
                                    <?php
                                    $maskedEmail = maskEmail($rows['customer_email']);
                                    ?>
                                    <?= htmlspecialchars($maskedEmail) ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">Phone</th>
                                <td>
                                    <?php
                                    $maskedPhone = str_repeat('*', strlen($rows['customer_phone']) - 4) . substr($rows['customer_phone'], -4);
                                    ?>
                                    <?= htmlspecialchars($maskedPhone) ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">Address</th>
                                <td>
                                    <?= htmlspecialchars($rows['address_1']) ?><br>
                                    <?= htmlspecialchars($rows['address_2']) ?><br>
                                    <?= htmlspecialchars($rows['postcode']) ?>
                                    <?= htmlspecialchars($rows['city']) ?><br>
                                    <?= htmlspecialchars($rows['state']) ?>,
                                    <?= htmlspecialchars($rows['country']) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Order Details -->
                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">📦 Order Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width:200px;max-width:200px;">Order ID</th>
                                <td>#
                                    <?= str_pad($rows['id'], 8, "0", STR_PAD_LEFT) ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">Product</th>
                                <td>
                                    <?php
                                    $sessionID = $rows["session_id"];
                                    $getCartProduct = $conn->query("SELECT * FROM cart WHERE `session_id`='$sessionID' AND deleted_at IS NULL");

                                    ?>
                                    <ul>
                                        <?php
                                        $x = 1;
                                        while ($rowP = $getCartProduct->fetch_array()) {
                                            $productID = $rowP["p_id"];
                                            $getProduct = $conn->query("SELECT * FROM products WHERE id='$productID'");
                                            $rowPP = $getProduct->fetch_array();

                                            echo "<li style=\"margin-left:15px;\">{$rowPP["name"]} (<b>x{$rowP["quantity"]}</b>)</li>";


                                            $x++;
                                        }
                                        ?>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">Total Price</th>
                                <td>
                                    <?= $rows['currency_sign'] . number_format($rows['total_price'], 2) ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">Status</th>
                                <td>
                                    <?php
                                    if ($rows['status'] == 1) {
                                        ?>
                                        <span class="badge bg-outline-primary">New Order</span>
                                        <?php
                                    } else if ($rows['status'] == 2) {
                                        ?>
                                            <span class="badge bg-info">Processed</span>
                                        <?php
                                    } else if ($rows['status'] == 3) {
                                        ?>
                                                <span class="badge bg-primary">On Delivery</span>
                                        <?php
                                    } else if ($rows['status'] == 4) {
                                        ?>
                                                    <span class="badge bg-success">Completed</span>
                                        <?php
                                    } else if ($rows['status'] == 5) {
                                        ?>
                                                        <span class="badge bg-warning">Returned</span>
                                        <?php
                                    } else if ($rows['status'] == 6) {
                                        ?>
                                                            <span class="badge bg-warning">Cancelled</span>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Shipment Details -->
                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">🚚 Shipment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width:200px;max-width:200px;">Courier</th>
                                <td>
                                    <?= htmlspecialchars($rows['courier_service']) ?: '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">AWB Number</th>
                                <td>
                                    <?= htmlspecialchars($rows['awb_number']) ?: '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:200px;max-width:200px;">Tracking</th>
                                <td>
                                    <?php if (!empty($rows['tracking_url'])): ?>
                                        <a href="<?= htmlspecialchars($rows['tracking_url']) ?>" target="_blank">Track
                                            Package</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div>
            </div>
        </div>

    </div>
</section>

<!-- Shop Cart Section End -->

<?php
include "e-footer-keya88.php";
?>