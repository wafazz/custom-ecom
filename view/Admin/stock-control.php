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
                            <h6>Stock Control</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;overflow-x: auto !important;">

                        <?php
                        if ($result->num_rows > 0) {
                            // Group rows by product_id
                            $products = [];
                            while ($row = $result->fetch_assoc()) {
                                $pid = $row['product_id'];
                                if (!isset($products[$pid])) {
                                    $products[$pid] = [
                                        'info' => $row,
                                        'variants' => []
                                    ];
                                }
                                $products[$pid]['variants'][] = $row;
                            }
                        ?>
                            <style>
                                #stockTable th,
                                #stockTable td {
                                    text-align: center;
                                    vertical-align: top;

                                }

                                #stockTable td.product-name,
                                #stockTable td.price {
                                    text-align: left;
                                }

                                #stockTable td.price {
                                    min-width: 250px !important;
                                }
                            </style>

                            <table id='stockTable' border='1' cellpadding='8' cellspacing='0'>
                                <thead>
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Retail/Sale Price</th>
                                        <th>Physical Stock Balance</th>
                                        <th>Total Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($products as $pid => $pData) {
                                        $row = $pData['info'];
                                        $variants = $pData['variants'];
                                        $isVariable = (count($variants) > 1);
                                        $firstVariant = $variants[0];
                                    ?>
                                        <tr>
                                            <td>
                                                <?= $row['product_id'] ?> <i class="fa-solid fa-pen-to-square" style="display: block;
    width: 40px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 10px;
    margin-bottom: 10px;color:blue;cursor:pointer;"
                                                    onClick="window.location.href = '<?= $domainURL; ?>update-product/<?= $row['product_id'] ?>'"></i>

                                                <?php
                                                if ($row['product_status'] == "1") {
                                                ?>
                                                    <div class="toggle-btn" data-id="<?= $row['product_id'] ?>">
                                                        <img class="btnOn" src="<?= $domainURL; ?>assets/images/on.png" style="display:block;width:65px;cursor:pointer;margin-left:auto;margin-right:auto;">
                                                        <img class="btnOff" src="<?= $domainURL; ?>assets/images/off.png" style="display:none;width:65px;cursor:pointer;margin-left:auto;margin-right:auto;">
                                                    </div>
                                                <?php
                                                } else if ($row['product_status'] == "0") {
                                                ?>
                                                    <div class="toggle-btn" data-id="<?= $row['product_id'] ?>">
                                                        <img class="btnOn" src="<?= $domainURL; ?>assets/images/on.png" style="display:none;width:65px;cursor:pointer;margin-left:auto;margin-right:auto;">
                                                        <img class="btnOff" src="<?= $domainURL; ?>assets/images/off.png" style="display:block;width:65px;cursor:pointer;margin-left:auto;margin-right:auto;">
                                                    </div>
                                                <?php
                                                }
                                                if (roleVerify("button-delete-product", $_SESSION['user']->id) == 0) {
                                                ?>
                                                    <i class="fa-solid fa-trash no-permission-btn" style="display: block;
    width: 40px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 10px;
    margin-bottom: 10px;color:red;cursor:pointer;"></i>
                                                <?php
                                                } else {
                                                ?>
                                                    <i class="fa-solid fa-trash" style="display: block;
    width: 40px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 10px;
    margin-bottom: 10px;color:red;cursor:pointer;" onclick="confirmDelete(this)"></i>
                                                <?php
                                                }
                                                ?>

                                            </td>
                                            <td class='product-name'>
                                                <?= htmlspecialchars($row['product_name']) ?>
                                                <br>
                                                <?php if (!empty($row['product_image'])): ?>
                                                    <img src="<?= $productImageDIR; ?><?= htmlspecialchars($row['product_image']) ?>"
                                                        width="60">
                                                <?php else: ?>
                                                    <span>No Image</span>
                                                <?php endif; ?>

                                                <?php if ($isVariable): ?>
                                                    <br>
                                                    <small style="color:#888;"><?= count($variants) ?> variants</small>
                                                    <select class="form-control variant-dropdown mt-1" data-product="<?= $pid ?>" onchange="switchVariant(this)" style="font-size:12px; appearance:auto; -webkit-appearance:menulist; -moz-appearance:menulist; padding-right:25px;">
                                                        <?php foreach ($variants as $v): ?>
                                                            <option value="<?= $v['variant_id'] ?>"
                                                                data-sku="<?= htmlspecialchars($v['sku']) ?>"
                                                                data-stock="<?= $v['physical_stock'] ?>"
                                                                data-sold="<?= $v['total_sold'] ?>"
                                                                data-name="<?= htmlspecialchars($v['variant_name'] ?? $v['sku']) ?>">
                                                                <?= htmlspecialchars($v['variant_name'] ?? $v['sku']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif; ?>
                                            </td>
                                            <td class="sku-cell-<?= $pid ?>">
                                                <?= htmlspecialchars($firstVariant['sku']) ?>
                                            </td>
                                            <td class="price">
                                                <p>Retails</p>
                                                <?php
                                                getPriceCountryMP($row['product_id'])
                                                ?>
                                                <p>Sale</p>
                                                <?php
                                                getPriceCountrySP($row['product_id'])
                                                ?>
                                            </td>
                                            <td>
                                                <span class="stock-cell-<?= $pid ?> btn <?= $firstVariant['physical_stock'] >= 101 ? 'btn-outline-success' : 'btn-outline-danger' ?>" style="display: block;
    margin-bottom: 10px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    font-size: 20px;">
                                                    <?= $firstVariant['physical_stock'] ?>
                                                </span>

                                                <?php
                                                if (roleVerify("button-add-deduct-stock", $_SESSION['user']->id) == 0) {
                                                ?>
                                                    <button class="btn btn-info no-permission-btn">
                                                        Add/Deduct Stock
                                                    </button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button class="btn btn-info stock-btn-<?= $pid ?>" onclick="openModal(this)"
                                                        data-id="<?= $firstVariant['variant_id'] ?>" data-name="<?= htmlspecialchars($row['product_name']) ?><?= $isVariable ? ' (' . htmlspecialchars($firstVariant['variant_name'] ?? $firstVariant['sku']) . ')' : '' ?>">
                                                        Add/Deduct Stock
                                                    </button>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="sold-cell-<?= $pid ?> btn btn-outline-secondary" style="display: block;
    margin-bottom: 10px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    font-size: 20px;"><?= $firstVariant['total_sold'] ?></span>
                                            </td>
                                        </tr>

                                    <?php
                                    }

                                    echo "
        </tbody>
    </table>
    ";
                                    ?>
                                    <script>
                                        function switchVariant(sel) {
                                            var pid = $(sel).data('product');
                                            var opt = sel.options[sel.selectedIndex];
                                            var sku = opt.getAttribute('data-sku');
                                            var stock = opt.getAttribute('data-stock');
                                            var sold = opt.getAttribute('data-sold');
                                            var vName = opt.getAttribute('data-name');
                                            var vid = sel.value;

                                            // Update SKU
                                            $('.sku-cell-' + pid).text(sku);

                                            // Update stock
                                            var stockEl = $('.stock-cell-' + pid);
                                            stockEl.text(stock);
                                            stockEl.removeClass('btn-outline-success btn-outline-danger');
                                            stockEl.addClass(parseInt(stock) >= 101 ? 'btn-outline-success' : 'btn-outline-danger');

                                            // Update sold
                                            $('.sold-cell-' + pid).text(sold);

                                            // Update Add/Deduct Stock button
                                            var btn = $('.stock-btn-' + pid);
                                            btn.data('id', vid);
                                            btn.attr('data-id', vid);
                                            var productName = btn.data('name').split('(')[0].trim();
                                            btn.data('name', productName + ' (' + vName + ')');
                                            btn.attr('data-name', productName + ' (' + vName + ')');
                                        }

                                        $(document).ready(function() {
                                            $('#stockTable').DataTable({
                                                responsive: true,
                                                pageLength: 25,
                                                order: [[0, 'asc']],
                                                columnDefs: [
                                                    { type: 'num', targets: 0 }
                                                ]
                                            });
                                        });
                                    </script>
                                <?php
                            } else {
                                echo "
    <style>
        #stockTable th, #stockTable td { text-align: center; }
    </style>
    <table id='stockTable'>
        <thead><tr><th colspan='10'>No records found.</th></tr></thead>
    </table>";
                            }
                            $conn->close();
                                ?>

                                <script>
                                    $('.no-permission-btn').click(function(e) {
                                        e.preventDefault(); // Prevent default button action if needed

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Access Denied',
                                            text: 'You do not have permission to perform this action.',
                                            confirmButtonColor: '#d33'
                                        });
                                    });
                                </script>

                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(document).on("click", ".btnOn", function() {
                    let parent = $(this).closest(".toggle-btn");
                    let productId = parent.data("id");

                    // AJAX request to disable (stat=0)
                    $.get("<?= $domainURL ?>set-product-enable-disable.php", {
                        id: productId,
                        stat: 0
                    }, function(response) {
                        console.log("Response:", response);
                    });

                    parent.find(".btnOn").hide();
                    parent.find(".btnOff").show();
                });

                $(document).on("click", ".btnOff", function() {
                    let parent = $(this).closest(".toggle-btn");
                    let productId = parent.data("id");

                    // AJAX request to enable (stat=1)
                    $.get("<?= $domainURL ?>set-product-enable-disable.php", {
                        id: productId,
                        stat: 1
                    }, function(response) {
                        console.log("Response:", response);
                    });

                    parent.find(".btnOff").hide();
                    parent.find(".btnOn").show();
                });
            });
        </script>

    </div>

    <?php
    include "01-footer.php";
    ?>