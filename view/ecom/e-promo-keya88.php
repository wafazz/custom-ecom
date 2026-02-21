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
                    <span>Promo Items</span>
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
                <h5 style="margin-bottom: 20px;">This page are showing all PROMO ITEMS.
                </h5>

                <div class="row property__gallery">
                    <?php
                    if (!empty($results)) {
                        foreach ($results as $rows) {
                            $discount = $rows['market_price'] - $rows['sale_price'];


                            $bs_productids = $rows["product_id"];
                            $bsPrices = getPriceOnCountry($country, $bs_productids);
                            $images = getProductImageSingle($bs_productids)

                                ?>

                            
                            <div class="col-lg-3 col-md-4 col-sm-6 mix category<?= $rows["category_id"] ?>">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg"
                                        data-setbg="<?= $domainURL ?>assets/images/products/<?= $images["image"] ?>">
                                        <div class="label sale">SALE</div>
                                        <ul class="product__hover">
                                            <li><a href="<?= $domainURL ?>assets/images/products/<?= $images["image"] ?>"
                                                    class="image-popup"><span class="arrow_expand"></span></a></li>
                                            <!-- <li><a href="#"><span class="icon_heart_alt"></span></a></li> -->
                                            <li><a href="<?= $domainURL ?>product-details/<?= $rows["product_id"] ?>"><span
                                                        class="icon_bag_alt"></span></a></li>
                                        </ul>
                                    </div>
                                    <div class="product__item__text">
                                        <h6><a href="<?= $domainURL ?>product-details/<?= $rows["product_id"] ?>"><?= $rows["product_name"] ?></a>
                                        </h6>
                                        <div class="rating">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="product__price" style="color: #ddd;
    text-decoration: line-through;">
                                        <?= $data["sign"] ?>
                                            <?= number_format($bsPrices["market"], 2) ?>
                                        </div>
                                        <div class="product__price">
                                        <?= $data["sign"] ?>
                                            <?= number_format($bsPrices["sale"], 2) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php
                        }
                    } else {
                        echo "No promo items found.";
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