<?php
include "01-header.php";
include "01-menu.php";
?>



<!-- End Navbar -->
<div class="container-fluid py-4">
    <div class="row">

    </div>
    <div class="row my-4">
        <div class="col-lg-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6><?= $pageName ?></h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="position:relative;display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">

                        <style>
                            /* Default: make table scrollable on small screens */
                            .table-responsive {
                                overflow-x: auto;
                            }

                            #stockTable {
                                min-width: 700px;
                                /* Minimum width before horizontal scroll appears */
                                width: 100%;
                                /* Always take full width */
                                border-collapse: collapse;
                            }

                            #stockTable thead tr {
                                background-color: #f6f6f6;
                            }

                            #stockTable th {
                                font-weight: 100;
                                font-size: 14px;
                                ;
                                --font: "Roboto", "Droid Sans", Arial, sans-serif;
                            }

                            #stockTable tbody tr.header-list {
                                background-color: #f6f6f6;
                            }

                            #stockTable tbody tr.header-list td,
                            #stockTable tbody tr.details td {
                                padding: 10px 15px;
                                font-weight: 100;
                                font-size: 14px;
                                ;
                                --font: "Roboto", "Droid Sans", Arial, sans-serif;
                                vertical-align: top;
                            }

                            #stockTable tbody tr.details {
                                border: 1px solid #f6f6f6;
                            }

                            .wrap-img {
                                float: left;
                                margin-right: 15px;
                                width: 150px;
                                height: auto;
                            }

                            .text-wrap-image p {
                                font-weight: 100;
                                font-size: 14px;
                                ;
                                --font: "Roboto", "Droid Sans", Arial, sans-serif;
                                color: #000000;

                            }

                            @media (min-width: 768px) {
                                .table-responsive {
                                    overflow-x: visible;
                                }

                                #stockTable {
                                    width: 100%;
                                    /* Take full width on desktop */
                                }
                            }
                        </style>
                        <div class="table-responsive">

                            <div style="display: block;
    margin-bottom: 20px;">
                                Filter by:
                                <br>
                                <input type="text" id="filter-product" placeholder="Product Name"> <input type="number" id="qty-product" placeholder="Quantity"> <select name="sort" id="sort-product" style="height: 32px;
    line-height: 32px;">
                                    <option value="">Sort By</option>
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                                <i class="fa-solid fa-filter btn btn-info" id="filter" style="margin-left: -10px;
    margin-top: 14px;
    height: 32px;
    border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;
    line-height: 10px;
    padding: 12px;"></i>
                                <script>
                                    // Restore input value on page load if URL contains ?filter=
                                    window.addEventListener("DOMContentLoaded", function() {
                                        const urlParams = new URLSearchParams(window.location.search);
                                        const existingSearch = urlParams.get("filter");
                                        const existingQty = urlParams.get("qty");
                                        const existingSort = urlParams.get("sort");
                                        if (existingSearch) {
                                            document.getElementById("filter-product").value = existingSearch;
                                        }
                                        if (existingQty) {
                                            document.getElementById("qty-product").value = existingQty;
                                        }
                                        if (existingSort) {
                                            document.getElementById("sort-product").value = existingSort;
                                        }

                                        // Auto-focus and keep cursor at the end
                                        const input = document.getElementById("filter-product");
                                        input.focus();
                                        const length = input.value.length;
                                        input.setSelectionRange(length, length);
                                    });

                                    // On filter icon click
                                    document.getElementById("filter").addEventListener("click", function() {
                                        const input = document.getElementById("filter-product");
                                        const getqty = document.getElementById("qty-product");
                                        const value = input.value.trim();
                                        const qty = getqty.value.trim();
                                        const sort = document.getElementById("sort-product").value.trim();


                                        //if (value.length >= 4) {
                                        const url = new URL(window.location.href);
                                        url.searchParams.set("filter", value);
                                        url.searchParams.set("qty", qty);
                                        url.searchParams.set("sort", sort);
                                        window.location.href = url.toString();
                                        // } else {
                                        //     alert("Enter at least 4 characters to filter.");
                                        // }
                                    });
                                </script>
                            </div>
                            <form action="" method="post">
                                <input type="hidden" id="allIDS" name="orderID" value="">

                                <?php
                                if ($pageName == "Order - New") {
                                ?>
                                    <button class="btn btn-info" id="bulkJNT" type="submit" name="jnt" style="display:block;margin-bottom:10px;width: 190px;" disabled><i class="fa-solid fa-truck-fast"></i> Bulk Send to J&T</button>
                                <?php
                                } else if ($pageName == "Order - Process") {
                                ?>
                                    <button class="btn btn-primary" id="bulkJNT" type="submit" name="printAWB" style="display: inline-block;
    margin-bottom: 10px;
    width: 155px;" disabled><i class="fa-solid fa-print"></i> Bulk Print AWB</button> <button class="btn btn-primary" id="bulkJNTS" type="submit" name="move-indelivery" style="display: inline-block;
    margin-bottom: 10px;
    width: 193px;" disabled><i class="fa-solid fa-truck-fast"></i> Bulk Move In Delivery</button>
                                <?php
                                }
                                ?>

                            </form>
                            <style>
                                .single-line {
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    display: inline-block;
                                    /* or block */
                                    max-width: 120px;
                                    /* set width so ellipsis works */
                                }
                            </style>
                            <table id='stockTable' cellpadding='8' cellspacing='0'>
                                <thead>
                                    <tr>
                                        <th>Select All<br><input type="checkbox" id="checkAll"><br>
                                            <div style="display: block;
    width: fit-content;
    text-align: center;
    background: black;
    color: #fff;
    padding: 5px 10px;
    border-radius: 10px;
    font-weight: bold;" id="selectedCount">0 selected</div>
                                        </th>
                                        <th>Product(s)</th>
                                        <th>Order Total</th>
                                        <th>Status</th>
                                        <th>Shipping Channel</th>
                                        <th style="min-width: 190px !important;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows < 1) {
                                    ?>
                                        <tr class="seperator">
                                            <td colspan="6"></td>
                                        </tr>
                                        <tr class="header-list">
                                            <td colspan="6" style="text-align:center;">
                                                <img src="<?= $domainURL ?>assets/images/no-data.png" style="display: block;
                                                    width: 60px;
                                                    margin-left: auto;
                                                    margin-right: auto;">
                                                no data
                                            </td>
                                        </tr>
                                        <?php
                                    } else {
                                        while ($row = $result->fetch_array()) {
                                            $session_id = $row["session_id"];
                                        ?>
                                            <tr class="seperator">
                                                <td colspan="6"></td>
                                            </tr>
                                            <tr class="header-list">
                                                <td colspan="4"><i class="fa-solid fa-user"></i>
                                                    <?= $row["customer_name"] ?> (
                                                    <?= $row["country"] ?>)
                                                </td>
                                                <td colspan="2" style="text-align:right;">Order Id: <b>#
                                                        <?= str_pad($row["order_id"], 8, "0", STR_PAD_LEFT); ?>
                                                        <br>
                                                        <?php
                                                        if ($pageName == "Order - Process") {
                                                            echo $row['printed_status'];
                                                        }
                                                        ?>
                                                    </b></td>
                                            </tr>
                                            <tr class="details">
                                                <td>
                                                    <?php
                                                    if ($row['status'] == "1" AND !empty($row['awb_number'])) {

                                                    ?>
                                                        <input type="checkbox" disabled>
                                                    <?php
                                                    }else{

                                                    ?>
                                                        <input type="checkbox" name="order_ids[]" value="<?= $row['order_id'] ?>">
                                                    <?php

                                                    }
                                                    ?>
                                                    
                                                </td>
                                                <td>
                                                    <table>
                                                        <?php
                                                        $string = $row["product_var_id"];

                                                        // Step 1: explode by comma
                                                        $parts = explode(",", $string);

                                                        // Step 2: loop and clean brackets
                                                        foreach ($parts as $varID) {
                                                            // Remove [ and ] using str_replace
                                                            $id = str_replace(['[', ']'], '', $varID);
                                                            //echo $id . "<br>";

                                                            $sql1 = "SELECT * FROM product_variants WHERE id='$id'";
                                                            $result1 = $conn->query($sql1);

                                                            while ($row1 = $result1->fetch_assoc()) {

                                                                $product_id = $row1["product_id"];
                                                                $pv_id = $row1["id"];

                                                                $sql11 = "SELECT * FROM `products` WHERE `id`='$product_id'";
                                                                $result11 = $conn->query($sql11);
                                                                $row11 = $result11->fetch_assoc();

                                                                $sql111 = "SELECT * FROM `product_image` WHERE `product_id`='$product_id' ORDER BY id ASC LIMIT 1";
                                                                $result111 = $conn->query($sql111);
                                                                $row111 = $result111->fetch_assoc();

                                                                //cart
                                                                $sql1111 = "SELECT * FROM cart WHERE `session_id`='$session_id' AND p_id='$product_id' AND pv_id='$id' AND deleted_at IS NULL";
                                                                $result1111 = $conn->query($sql1111);
                                                                $row1111 = $result1111->fetch_assoc();

                                                        ?>
                                                                <tr>
                                                                    <td style="min-width: 250px !important;">
                                                                        <div class="text-wrap-image">
                                                                            <img src="<?= $domainURL ?>assets/images/products/<?= $row111['image'] ?>" style="width:60px;" alt="Example" class="wrap-img">
                                                                            <p>
                                                                                (<?= $row1["sku"] ?>)
                                                                                <?= $row11["name"] ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td style="min-width: 70px !important;
    text-align: center;">
                                                                        x
                                                                        <?= $row1111['quantity'] ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        $tprices = $row1111['quantity'] * $row1111['price'];
                                                                        echo "<span class=\"single-line\">(" . $row1111['quantity'] . " x RM" . number_format($row1111['price'], 2) . ")</span><br><b>RM" . number_format($tprices, 2) . "</b>";
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            }

                                                            ?>

                                                        <?php
                                                        }
                                                        ?>

                                                    </table>


                                                </td>

                                                <td>
                                                    RM
                                                    <?= number_format($row["myr_value_include_postage"], 2) ?>
                                                    <br>
                                                    <small>
                                                        <?= $row['payment_channel'] ?>
                                                    </small>
                                                    <br>
                                                    [<a href="<?= $domainURL ?>check-stripe-status.php?payment_intent=<?= $row['payment_url'] ?>" target="_blank">Payment Proof</a>]
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($row['status'] == "0") {
                                                        echo "To Pay";
                                                    } else if ($row['status'] == "1") {
                                                        echo "New Order";
                                                    } else if ($row['status'] == "2") {
                                                        echo "Processing Order";
                                                    } else if ($row['status'] == "3") {
                                                        echo "In Delivery Order";
                                                    } else if ($row['status'] == "4") {
                                                        echo "Completed Order";
                                                    } else if ($row['status'] == "5") {
                                                        echo "Return Order";
                                                    } else if ($row['status'] == "6") {
                                                        echo "Cancel Order";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= $row['ship_channel'] ?>
                                                    <br>
                                                    <small>Courier: <b>
                                                            <?php
                                                            if (empty($row['courier_service'])) {
                                                                echo "~not assign yet~";
                                                            } else {
                                                                echo $row['courier_service'];
                                                            }
                                                            ?>
                                                        </b></small>
                                                </td>
                                                <td>

                                                    <a class="btn btn-outline-primary open-details" data-id="<?= $row['order_id']; ?>" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-magnifying-glass"></i> check details</a>




                                                    <?php

                                                    if (!empty($row['awb_number'])) {
                                                    ?>
                                                        <span class="btn btn-info" onClick="window.open('<?= $row["tracking_url"] ?>', '_blank')" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-truck-fast"></i>
                                                            <?= $row['awb_number'] ?>
                                                        </span>
                                                    <?php
                                                    }

                                                    if ($row['status'] == "1") {
                                                    ?>
                                                        <form action="" method="post">
                                                            <input type="hidden" name="orderID" value="<?= $row["order_id"] ?>">
                                                            <!-- <button class="btn btn-info" type="submit" name="dhl" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-truck-fast"></i> Send to DHL</button> -->

                                                            <?php
                                                            if(!empty($row['awb_number'])){
                                                            ?>
                                                                <a class="btn btn-info" onClick="window.location.href = '<?= $domainURL ?>set-order-to-processing/<?= $row["order_id"] ?>'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-right-long"></i> Move to Processing</a>
                                                                
                                                            <?php
                                                            }else{
                                                            ?>
                                                            <button class="btn btn-info" type="submit" name="jnt" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-truck-fast"></i> Send to J&T</button>
                                                            <?php
                                                            }
                                                            ?>
                                                            

                                                            <!-- <button class="btn btn-primary" type="submit" name="jnt"
                                                                style="display:block;margin-bottom:10px;width: 190px;"><i
                                                                    class="fa-solid fa-truck-fast"></i> Send to J&T Express</button>
                                                            <button class="btn btn-secondary" type="submit" name="ninja"
                                                                style="display:block;margin-bottom:10px;width: 190px;"><i
                                                                    class="fa-solid fa-truck-fast"></i> Send to NinjaVan</button> -->
                                                            <a class="btn btn-danger" onClick="window.location.href = '<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/1/6'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-ban"></i> Mark as Cancel</a>
                                                        </form>
                                                    <?php
                                                    } else if ($row['status'] == "2") {
                                                    ?>
                                                        <form action="" method="post">
                                                            <input type="hidden" name="orderID" value="<?= $row["order_id"] ?>">


                                                            <button class="btn btn-secondary" type="submit" name="print-awb" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-print"></i> Print AWB</button>
                                                            <a class="btn btn-dark" onClick="window.location.href = '<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/2/3'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-truck-ramp-box"></i> Mark as In Delivery</a>
                                                            <a class="btn btn-danger" onClick="window.location.href = '<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/2/6'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-ban"></i> Mark as Cancel</a>
                                                        </form>
                                                    <?php
                                                    } else if ($row['status'] == "3") {
                                                    ?>
                                                        <form id="printForm" action="" method="post" target="hiddenIframe">
                                                            <input type="hidden" name="orderID" value="<?= $row["order_id"] ?>">
                                                            <a class="btn btn-secondary" style="display:block;margin-bottom:10px;width: 190px;" onClick="window.open('<?= $domainURL ?>awb-jt.php?id=<?= $row["order_id"] ?>', '_blank')"><i class="fa-solid fa-print"></i> Re-Print AWB</a>

                                                            <a class="btn btn-success" onClick="window.location.href = '<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/3/4'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-check-double"></i> Mark as Completed</a>
                                                            <a class="btn btn-warning" onClick="window.location.href = '<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/3/5'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-right-left"></i> Mark as RTS</a>
                                                            <a class="btn btn-danger" onClick="window.location.href = '<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/3/6'" style="display:block;margin-bottom:10px;width: 190px;"><i class="fa-solid fa-ban"></i> Mark as Cancel</a>
                                                        </form>
                                                    <?php
                                                    }
                                                    ?>

                                                    </form>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>


                                </tbody>
                            </table>
                            <?php if ($totalPages >= 1): ?>
                                <div style="margin-top: 20px; text-align: center;">
                                    <?php
                                    $baseURL = $domainURL . $firstSegments;
                                    $queryParams = [];
                                    if (!empty($search)) {
                                        $queryParams['filter'] = urlencode($search);
                                    }
                                    if (!empty($qty)) {
                                        $queryParams['qty'] = urlencode($qty);
                                    }
                                    if (!empty($printed)) {
                                        $queryParams['printed'] = urlencode($printed);
                                    }
                                    if (!empty($sort)) {
                                        $queryParams['sort'] = urlencode($sort);
                                    }

                                    // Helper to build page link
                                    function buildPageLink($page, $queryParams, $baseURL)
                                    {
                                        $queryParams['page'] = $page;
                                        return $baseURL . '?' . http_build_query($queryParams);
                                    }

                                    $prevPage = $page - 1;
                                    $nextPage = $page + 1;

                                    // First Page
                                    if ($page > 1) {
                                        echo '<a class="btn btn-sm btn-primary" href="' . buildPageLink(1, $queryParams, $baseURL) . '">First</a> ';
                                        echo '<a class="btn btn-sm btn-secondary" href="' . buildPageLink($prevPage, $queryParams, $baseURL) . '">Previous</a> ';
                                    }

                                    // Current Page Display
                                    echo '<span class="btn btn-sm btn-light disabled">Page ' . $page . ' of ' . $totalPages . '</span> ';

                                    // Next / Last Page
                                    if ($page < $totalPages) {
                                        echo '<a class="btn btn-sm btn-secondary" href="' . buildPageLink($nextPage, $queryParams, $baseURL) . '">Next</a> ';
                                        echo '<a class="btn btn-sm btn-primary" href="' . buildPageLink($totalPages, $queryParams, $baseURL) . '">Last</a>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <!-- <script>
                                // JavaScript for "Check All"
                                document.getElementById('checkAll').addEventListener('change', function() {
                                    const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
                                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                                });
                            </script> -->
                            <script>
                                const checkAll = document.getElementById('checkAll');
                                const allIdsInput = document.getElementById('allIDS');
                                const bulkBtn = document.getElementById('bulkJNT');
                                const bulksBtn = document.getElementById('bulkJNTS');
                                const selectedCount = document.getElementById('selectedCount');

                                function updateAllIds() {
                                    const checkedValues = Array.from(document.querySelectorAll('input[name="order_ids[]"]:checked'))
                                        .map(cb => cb.value);
                                    const allIds = checkedValues.join(',');

                                    allIdsInput.value = allIds;

                                    selectedCount.textContent = checkedValues.length > 0 ?
                                        `${checkedValues.length} selected` :
                                        '0 selected';

                                    // Enable or disable the button
                                    bulkBtn.disabled = allIds === '';
                                    bulksBtn.disabled = allIds === '';
                                }

                                // Handle "Check All"
                                checkAll.addEventListener('change', function() {
                                    const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
                                    checkboxes.forEach(cb => cb.checked = this.checked);
                                    updateAllIds();
                                });

                                // Handle individual checkbox change
                                document.querySelectorAll('input[name="order_ids[]"]').forEach(cb => {
                                    cb.addEventListener('change', updateAllIds);
                                });

                                // Intercept button click
                                bulkBtn.addEventListener('click', function(e) {
                                    if (this.disabled) {
                                        e.preventDefault(); // prevent form submission
                                        alert("Please select at least one order before proceeding.");
                                    }
                                });

                                // Initial check on page load
                                updateAllIds();
                            </script>
                            <iframe name="hiddenIframe" style="display:none;"></iframe>
                            <script>
                                document.getElementById('printForm').addEventListener('submit', function() {
                                    // Wait a few seconds for download, then redirect
                                    setTimeout(function() {
                                        window.location.href = "<?= $domainURL ?>process-order"; // Change this to your target page
                                    }, 3000); // 3 seconds
                                });
                            </script>
                            <div class="bg-modal">

                                <div class="modal-details">
                                    <i style="position: absolute;
    top: 7px;
    right: 10px;
    font-size: 30px !important;
    color:  red !important;
    cursor: pointer;" class="fa-solid fa-square-xmark end-bg-modal"></i>

                                    <i style="position: absolute;
    top: 7px;
    right: 50px;
    font-size: 30px !important;
    color:  #8b8be9 !important;
    cursor: pointer;" class="fa-solid fa-pen-to-square edit-order-data"></i><i style="position: absolute;
    top: 7px;
    right: 50px;
    display: none;
    font-size: 30px !important;
    color:  #8b8be9 !important;
    cursor: pointer;" class="fa-solid fa-left-long close-order-data"></i>
                                    <input type="hidden" id="orderNoID" value="">
                                    <div id="details-buyer"></div>

                                </div>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    $(".end-bg-modal").click(function() {
                                        $(".bg-modal").hide();
                                        $('body').css('overflow-y', 'auto');
                                        $("#details-buyer").text("");
                                    })
                                    $(".open-details").click(function() {
                                        var orderID = $(this).data("id");

                                        $(".bg-modal").show();
                                        $('body').css('overflow-y', 'hidden');
                                        $("#orderNoID").val(orderID);
                                        $("#details-buyer").load("<?= $domainURL ?>details-buyer?order_id=" + orderID);
                                    })
                                    $(".edit-order-data").click(function() {
                                        var orderIDs = $("#orderNoID").val();

                                        $(this).hide();
                                        $(".close-order-data").show();

                                        $("#details-buyer").load("<?= $domainURL ?>update-buyer?order_id=" + orderIDs);
                                    })
                                    $(".close-order-data").click(function() {
                                        var orderIDs = $("#orderNoID").val();

                                        $(this).hide();
                                        $(".edit-order-data").show();

                                        $("#details-buyer").load("<?= $domainURL ?>details-buyer?order_id=" + orderIDs);
                                    })
                                });
                            </script>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>