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
                    <span>Brand -
                        <?= $dataBrand["name"] ?>
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
                <h5 style="margin-bottom: 20px;">This page are showing all items in
                    <?= $dataBrand["name"] ?> Brand.
                </h5>

                <div class="row property__gallery">
                    <?php
                    while ($rowNew = $query->fetch_assoc()) {
                        $proid = $rowNew["id"];
                        $sqlProImage = "SELECT * FROM product_image WHERE product_id='$proid' ORDER BY id ASC LIMIT 1";
                        $queryProImage = $conn->query($sqlProImage);
                        $rowProImage = $queryProImage->fetch_array();

                        $rowPrice = getPriceOnCountry($country, $proid);
                        $stock1 = stockBalanceIndividual($proid);
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mix category<?= $rowNew["category_id"] ?>">
                            <div class="product__item">
                                <div class="product__item__pic set-bg" style="position: relative;"
                                    data-setbg="<?= $domainURL ?>assets/images/products/<?= $rowProImage["image"] ?>">

                                    <?php
                                    if ($stock1["physical_stock"] < 1) {
                                    ?>
                                        <div style="
                                            position: absolute;
                                            border:1px solid #ccc;
                                            border-radius: 5px;
                                            width: 100%;
                                            height: 100%;
                                            z-index: 9999;
                                            background: rgba(255,255,255,0.75);
                                        ">
                                            <img src="https://rozeyana.com/assets/images/out-of-stock.png" style="
                                                width: 100%;
                                            ">
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <!-- <div class="label new">New</div> -->
                                    <ul class="product__hover">
                                        <li><a href="<?= $domainURL ?>assets/images/products/<?= $rowProImage["image"] ?>"
                                                class="image-popup"><span class="arrow_expand"></span></a></li>
                                        <!-- <li><a href="#"><span class="icon_heart_alt"></span></a></li> -->
                                        <?php
                                        if ($stock1["physical_stock"] >= 1) {
                                        ?>
                                            <li><a href="<?= $domainURL ?>product-details/<?= $rowNew["id"] ?>"><span
                                                    class="icon_bag_alt"></span></a></li>
                                        <?php
                                        } else {
                                        ?>
                                            <li><a href="javascript:void(0);" class="disabled"><span class="icon_bag_alt"></span></a></li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="product__item__text" style="position: relative;">
                                    <?php
                                    if ($stock1["physical_stock"] < 1) {
                                    ?>
                                        <div style="
                                            position: absolute;
                                            width: 100%;
                                            height: 100%;
                                            z-index: 9999;
                                            background: rgba(255,255,255,0.75);
                                        ">
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <h6><a href="<?= $domainURL ?>product-details/<?= $rowNew["id"] ?>"><?= $rowNew["name"] ?></a>
                                    </h6>
                                    <div class="rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="product__price">
                                        <?= $data["sign"] ?>
                                        <?= number_format($rowPrice["sale"], 2) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>
            </div>
        </div>

    </div>
</section>

<!-- Shop Cart Section End -->

<?php
include "e-footer-keya88.php";
?>