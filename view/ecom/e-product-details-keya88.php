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
                    <a href="<?= $domainURL ?>categories/<?= $dataProduct["category_id"] ?>"><?= $dataCategory["name"] ?></a>
                    <span>
                        <?= $dataProduct["name"] ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__left product__thumb nice-scroll">
                        <?php
                        $thumb = getAllProductImage($id);
                        $tt = 1;
                        while ($timg = $thumb->fetch_array()) {
                            ?>
                            <a class="pt active" href="#product-<?= $tt ?>">
                                <img src="<?= $domainURL ?>assets/images/products/<?= $timg["image"] ?>" alt="">
                            </a>
                            <?php
                            $tt++;
                        }
                        ?>

                    </div>
                    <div class="product__details__slider__content">
                        <div class="product__details__pic__slider owl-carousel">
                            <?php
                            $shown = getAllProductImage($id);
                            $rr = 1;
                            while ($simg = $shown->fetch_array()) {
                                ?>
                                <img data-hash="product-<?= $rr ?>" class="product__big__img"
                                    src="<?= $domainURL ?>assets/images/products/<?= $simg["image"] ?>" alt="">
                                <?php
                                $rr++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product__details__text">
                    <h3>
                        <?= $dataProduct["name"] ?> <span>Brand:
                            <?= $dataBrand["name"] ?>
                        </span>
                    </h3>
                    <h6>Sold (<b><?= $sold ?></b>)</h6>
                    <div class="rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <span>( 138 reviews )</span>
                    </div>
                    <div class="product__details__price">
                        <?= $data["sign"] ?>
                        <?= $pPrice["sale"] ?>

                        <?php
                        if ($pPrice["sale"] < $pPrice["market"]) {
                            ?>
                            <span>
                                <?= $data["sign"] ?>
                                <?= $pPrice["market"] ?>
                            </span>
                            <?php
                        }
                        ?>

                    </div>
                    <?php if ($productType === 'variable' && count($allVariants) > 1): ?>
                    <div class="product__details__variant" style="margin-bottom: 15px;">
                        <span>Variant:</span>
                        <select id="variant-selector" class="form-control" style="max-width:300px;display:inline-block;margin-left:10px;" onchange="onVariantChange()">
                            <?php foreach ($allVariants as $v):
                                $vs = $variantStocks[$v['id']];
                                $vStock = $vs['physical_stock'];
                                $vMax = $vs['max_purchase'];
                            ?>
                                <option value="<?= $v['id'] ?>"
                                    data-stock="<?= $vStock ?>"
                                    data-max="<?= $vMax ?>">
                                    <?= htmlspecialchars($v['variant_name'] ?? $v['sku']) ?>
                                    <?= $vStock < 1 ? '(Out of Stock)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="product__details__button">
                        <div class="quantity">
                            <span>Quantity:</span>
                            <div class="pro-qty">
                                <?php
                                if ($stock["physical_stock"] >= 1) {
                                    if ($stock["physical_stock"] > $stock["max_purchase"]) {
                                        $max = $stock["max_purchase"];
                                    } else {
                                        $max = $stock["physical_stock"];
                                    }
                                    ?>
                                    <input type="text" id="qty-input" value="1" min="1" step="1" max="<?= $max; ?>"
                                        name="qty">
                                    <?php
                                } else {
                                    ?>
                                    <input type="text" disabled value="1" name="qty">
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                        <?php
                        if ($productType === 'variable' && count($allVariants) > 1) {
                            // For variable products, default to first variant
                            $firstVariant = $allVariants[0];
                            $firstStock = $variantStocks[$firstVariant['id']]['physical_stock'];
                            if ($firstStock >= 1) {
                            ?>
                                <button class="cart-btn add-to-cart-btn" id="add-to-cart-btn" data-product-id="<?= $id ?>"
                                    data-variant-id="<?= $firstVariant['id'] ?>">
                                    <span class="icon_bag_alt"></span> Add to cart
                                </button>
                            <?php } else { ?>
                                <a class="cart-btn" id="add-to-cart-btn" style="cursor:no-drop;" disabled onClick="alert('Out of Stock');"><span
                                        class="icon_bag_alt"></span> Add to cart</a>
                            <?php }
                        } elseif ($stock["physical_stock"] >= 1) {
                            ?>
                            <button class="cart-btn add-to-cart-btn" data-product-id="<?= $id ?>"
                                data-variant-id="<?= $dataProduct['variant_id'] ?>">
                                <span class="icon_bag_alt"></span> Add to cart
                            </button>
                            <?php
                        } else {
                            ?>
                            <a class="cart-btn" style="cursor:no-drop;" disabled onClick="alert('Out of Stock');"><span
                                    class="icon_bag_alt"></span> Add to cart</a>
                            <?php
                        }
                        ?>

                    </div>
                    <div class="product__details__widget">
                        <ul>
                            <li>
                                <span>Availability:</span>
                                <div class="stock__checkbox">
                                    <label for="stockin" id="stock-display">
                                        <?php
                                        if ($stock["physical_stock"] >= 1) {
                                            ?>
                                            In Stock (<b>
                                                <?= $stock["physical_stock"] ?>
                                            </b>)
                                            <?php
                                        } else {
                                            ?>
                                            <span style="color:red;font-weight:bold;">Out of Stock</span>
                                            <?php
                                        }
                                        ?>

                                    </label>
                                </div>
                            </li>
                            <li>
                                <i class="fa-solid fa-truck-fast"></i> Standard delivery in 3-7 working days (Malaysia)
                            </li>
                            <li>
                                <i class="fa-solid fa-truck-fast"></i> Standard delivery in 7-14 working days (Oversea)
                            </li>
                            <li>
                                <i class="fa-solid fa-shop"></i> Visit our offline shop for mor deals
                            </li>
                            <li>
                                <i class="fa-solid fa-road-circle-check"></i> FREE & Easy Return within 14 days upon delivery
                            </li>
                        </ul>
                    </div>


                    <script>
                        function onVariantChange() {
                            var sel = document.getElementById('variant-selector');
                            if (!sel) return;
                            var opt = sel.options[sel.selectedIndex];
                            var vStock = parseInt(opt.getAttribute('data-stock'));
                            var vMax = parseInt(opt.getAttribute('data-max'));
                            var variantId = sel.value;

                            // Update stock display
                            var stockLabel = document.getElementById('stock-display');
                            if (stockLabel) {
                                if (vStock >= 1) {
                                    stockLabel.innerHTML = 'In Stock (<b>' + vStock + '</b>)';
                                } else {
                                    stockLabel.innerHTML = '<span style="color:red;font-weight:bold;">Out of Stock</span>';
                                }
                            }

                            // Update quantity max
                            var qtyInput = document.getElementById('qty-input');
                            if (qtyInput) {
                                var maxVal = (vStock > vMax) ? vMax : vStock;
                                if (vStock >= 1) {
                                    qtyInput.disabled = false;
                                    qtyInput.max = maxVal;
                                    qtyInput.value = 1;
                                } else {
                                    qtyInput.disabled = true;
                                    qtyInput.value = 1;
                                }
                            }

                            // Update add-to-cart button
                            var btn = document.getElementById('add-to-cart-btn');
                            if (btn) {
                                if (vStock >= 1) {
                                    $(btn).replaceWith('<button class="cart-btn add-to-cart-btn" id="add-to-cart-btn" data-product-id="<?= $id ?>" data-variant-id="' + variantId + '"><span class="icon_bag_alt"></span> Add to cart</button>');
                                    bindAddToCart();
                                } else {
                                    $(btn).replaceWith('<a class="cart-btn" id="add-to-cart-btn" style="cursor:no-drop;" disabled onClick="alert(\'Out of Stock\');"><span class="icon_bag_alt"></span> Add to cart</a>');
                                }
                            }
                        }

                        function bindAddToCart() {
                            $('.add-to-cart-btn').off('click').on('click', addToCartHandler);
                        }

                        function addToCartHandler(e) {
                            e.preventDefault();

                            let productID = $(this).data('product-id');
                            let variantID = $(this).data('variant-id');
                            let quantity = $('#qty-input').val();

                            $.ajax({
                                url: '<?= $domainURL ?>add-to-cart',
                                method: 'POST',
                                data: {
                                    p_id: productID,
                                    pv_id: variantID,
                                    qty: quantity
                                },
                                success: function (response) {
                                    if (response === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Added to Cart',
                                            text: 'Your item has been added to the cart!',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Failed to add to cart: ' + response
                                        });
                                    }
                                    $("#count1").load("<?= $domainURL ?>count-cart");
                                    $("#count2").load("<?= $domainURL ?>count-cart");
                                    $("#count10").load("<?= $domainURL ?>count-cart");
                                    $("#count11").load("<?= $domainURL ?>count-cart");
                                    $(".list-add-cart1").load("<?= $domainURL ?>list-cart");
                                    $(".list-add-cart2").load("<?= $domainURL ?>list-cart");
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Connection Error',
                                        text: 'Error connecting to server.'
                                    });
                                    $("#count1").load("<?= $domainURL ?>count-cart");
                                    $("#count2").load("<?= $domainURL ?>count-cart");
                                    $(".list-add-cart1").load("<?= $domainURL ?>list-cart");
                                    $(".list-add-cart2").load("<?= $domainURL ?>list-cart");
                                }
                            });
                        }
                    </script>
                    <script>
                        $('.add-to-cart-btn').on('click', function (e) {
                            e.preventDefault();

                            let productID = $(this).data('product-id');
                            let variantID = $(this).data('variant-id');
                            let quantity = $('#qty-input').val();

                            $.ajax({
                                url: '<?= $domainURL ?>add-to-cart',
                                method: 'POST',
                                data: {
                                    p_id: productID,
                                    pv_id: variantID,
                                    qty: quantity
                                },
                                success: function (response) {
                                    if (response === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Added to Cart',
                                            text: 'Your item has been added to the cart!',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });

                                        $("#count1").load("<?= $domainURL ?>count-cart");
                                        $("#count2").load("<?= $domainURL ?>count-cart");
                                        $("#count10").load("<?= $domainURL ?>count-cart");
                                        $("#count11").load("<?= $domainURL ?>count-cart");
                                        $(".list-add-cart1").load("<?= $domainURL ?>list-cart");
                                        $(".list-add-cart2").load("<?= $domainURL ?>list-cart");
                                        // Optionally update cart count here
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Failed to add to cart: ' + response
                                        });

                                        $("#count1").load("<?= $domainURL ?>count-cart");
                                        $("#count2").load("<?= $domainURL ?>count-cart");
                                        $(".list-add-cart1").load("<?= $domainURL ?>list-cart");
                                        $(".list-add-cart2").load("<?= $domainURL ?>list-cart");
                                    }
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Connection Error',
                                        text: 'Error connecting to server.'
                                    });

                                    $("#count1").load("<?= $domainURL ?>count-cart");
                                    $("#count2").load("<?= $domainURL ?>count-cart");
                                    $(".list-add-cart1").load("<?= $domainURL ?>list-cart");
                                    $(".list-add-cart2").load("<?= $domainURL ?>list-cart");
                                }
                            });
                        });
                    </script>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="product__details__tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Description</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Specification</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Reviews ( 2 )</a>
                        </li> -->
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                            <h6>Description</h6>
                            <p>
                                <?= nl2br($dataProduct["description"]) ?>
                            </p>
                        </div>
                        <div class="tab-pane" id="tabs-2" role="tabpanel">
                            <h6>Specification</h6>
                            <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut loret fugit, sed
                                quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt loret.
                                Neque porro lorem quisquam est, qui dolorem ipsum quia dolor si. Nemo enim ipsam
                                voluptatem quia voluptas sit aspernatur aut odit aut loret fugit, sed quia ipsu
                                consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Nulla
                                consequat massa quis enim.</p>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget
                                dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes,
                                nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium
                                quis, sem.</p>
                        </div>
                        <div class="tab-pane" id="tabs-3" role="tabpanel">
                            <h6>Reviews ( 2 )</h6>
                            <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut loret fugit, sed
                                quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt loret.
                                Neque porro lorem quisquam est, qui dolorem ipsum quia dolor si. Nemo enim ipsam
                                voluptatem quia voluptas sit aspernatur aut odit aut loret fugit, sed quia ipsu
                                consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Nulla
                                consequat massa quis enim.</p>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget
                                dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes,
                                nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium
                                quis, sem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-lg-12 text-center">
                <div class="related__title">
                    <h5>RELATED PRODUCTS</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="img/product/related/rp-1.jpg">
                        <div class="label new">New</div>
                        <ul class="product__hover">
                            <li><a href="img/product/related/rp-1.jpg" class="image-popup"><span
                                        class="arrow_expand"></span></a></li>
                            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                            <li><a href="#"><span class="icon_bag_alt"></span></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="#">Buttons tweed blazer</a></h6>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="product__price">$ 59.0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="img/product/related/rp-2.jpg">
                        <ul class="product__hover">
                            <li><a href="img/product/related/rp-2.jpg" class="image-popup"><span
                                        class="arrow_expand"></span></a></li>
                            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                            <li><a href="#"><span class="icon_bag_alt"></span></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="#">Flowy striped skirt</a></h6>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="product__price">$ 49.0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="img/product/related/rp-3.jpg">
                        <div class="label stockout">out of stock</div>
                        <ul class="product__hover">
                            <li><a href="img/product/related/rp-3.jpg" class="image-popup"><span
                                        class="arrow_expand"></span></a></li>
                            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                            <li><a href="#"><span class="icon_bag_alt"></span></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="#">Cotton T-Shirt</a></h6>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="product__price">$ 59.0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="img/product/related/rp-4.jpg">
                        <ul class="product__hover">
                            <li><a href="img/product/related/rp-4.jpg" class="image-popup"><span
                                        class="arrow_expand"></span></a></li>
                            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                            <li><a href="#"><span class="icon_bag_alt"></span></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="#">Slim striped pocket shirt</a></h6>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="product__price">$ 59.0</div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</section>
<!-- Product Details Section End -->

<!-- Instagram Begin -->
<!-- <div class="instagram">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="img/instagram/insta-1.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="img/instagram/insta-2.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="img/instagram/insta-3.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="img/instagram/insta-4.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="img/instagram/insta-5.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="img/instagram/insta-6.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- Instagram End -->

<?php
include "e-footer-keya88.php";
?>