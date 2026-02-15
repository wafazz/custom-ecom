<?php
include "e-header-keya88.php";
include "e-menu-keya88.php";
?>


<style>
    .fullwidth-slider {
        margin-bottom: 40px;
    }

    .owl-carousel.full-slider .item img {
        height: auto;
        width: 100%;
        object-fit: cover;
    }

    .fullwidth-slider .item {
        position: relative;
        overflow: hidden;
    }

    .fullwidth-slider .progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 5px;
        background: #ff4081;
        animation: slideBar 6s linear infinite;
        width: 100%;
        z-index: 10;
    }

    @keyframes slideBar {
        0% {
            width: 0;
        }

        100% {
            width: 100%;
        }
    }

    /* Hide default owl dots */
    .owl-dots {
        display: none !important;
    }

    .badge-sale {
        position: absolute;
        top: 10px;
        left: 10px;
        background: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 3px 8px;
        border-radius: 4px;
        z-index: 10;
    }
</style>

<!-- Full Width Responsive Slider Begin -->
<!-- <section class="fullwidth-slider">
    <div class="container-fluid px-0">
        <div class="owl-carousel owl-theme full-slider">
            <div class="item">
                <img src="<?= $domainURL ?>assets/ecom/img/banner/banner-slide1.webp" class="img-fluid w-100" alt="Slide 1">
                <div class="progress-bar"></div>
            </div>
            <div class="item">
                <img src="<?= $domainURL ?>assets/ecom/img/banner/banner-slide2.webp" class="img-fluid w-100" alt="Slide 2">
                <div class="progress-bar"></div>
            </div>
            <div class="item">
                <img src="<?= $domainURL ?>assets/ecom/img/banner/banner-slide3.webp" class="img-fluid w-100" alt="Slide 3">
                <div class="progress-bar"></div>
            </div>
        </div>
    </div>
</section> -->
<!-- <section class="fullwidth-slider">
    <div class="container-fluid px-0">
        <div class="owl-carousel owl-theme full-slider">
            <div class="item">
                <div class="slide-container">
                    <img src="<?= $domainURL ?>assets/ecom/img/banner/slider1.webp" class="img-fluid w-100" alt="Slide 1">
                    <div class="progress-bar"></div>
                </div>
            </div>
            <div class="item">
                <div class="slide-container">
                    <img src="<?= $domainURL ?>assets/ecom/img/banner/slider2.webp" class="img-fluid w-100" alt="Slide 2">
                    <div class="progress-bar"></div>
                </div>
            </div>
            <div class="item">
                <div class="slide-container">
                    <img src="<?= $domainURL ?>assets/ecom/img/banner/slider3.webp" class="img-fluid w-100" alt="Slide 3">
                    <div class="progress-bar"></div>
                </div>
            </div>
        </div>
    </div>
</section> -->
<!-- Full Width Responsive Slider End -->

<script>
    $(document).ready(function() {
        const animationsIn = ['fadeIn'];
        const animationsOut = ['fadeOut'];

        const owl = $('.owl-carousel.full-slider');

        owl.owlCarousel({
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 6000, // Set to 6s
            dots: false,
            nav: false,
            animateIn: 'fadeIn', // Placeholder
            animateOut: 'fadeOut', // Placeholder
            smartSpeed: 700
        });

        owl.on('translate.owl.carousel', function(e) {
            const current = $('.owl-item', owl).eq(e.item.index);
            const randomOut = animationsOut[Math.floor(Math.random() * animationsOut.length)];
            current.find('.item').removeClass().addClass(`item animate__animated animate__${randomOut}`);
        });

        owl.on('translated.owl.carousel', function(e) {
            const current = $('.owl-item', owl).eq(e.item.index);
            const randomIn = animationsIn[Math.floor(Math.random() * animationsIn.length)];
            current.find('.item').removeClass().addClass(`item animate__animated animate__${randomIn}`);
        });
    });
</script>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .categories__item {
        height: 300px;
        background-size: cover;
        background-position: center;
        position: relative;
        margin: 10px;
        border-radius: 8px;
        overflow: hidden;
    }

    .categories__text {
        background-color: rgba(0, 0, 0, 0.5);
        color: #fff;
        padding: 15px;
        position: absolute;
        bottom: 0;
        width: 100%;
        text-align: center;
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: #000;
        /* Customize arrow color */
    }

    @media (max-width: 768px) {
        .categories__item {
            height: 200px;
        }
    }
</style>

<section class="categories">
    <div class="container-fluid">
        <div class="col-lg-12">

            <!-- Swiper container -->
            <div class="swiper myCategorySwiper">
                <div class="swiper-wrapper">

                    <?php
                    // Assume $categories2 is your query result
                    $items = [];
                    while ($ct2 = $categories2->fetch_array()) {
                        $items[] = $ct2;
                    }

                    foreach ($items as $ct2) {
                        $imageURL = $domainURL . 'assets/images/brand-category/' . $ct2["image"];
                    ?>
                        <div class="swiper-slide">
                            <div class="categories__item" style="background-image: url('<?= $imageURL ?>')">
                                <div class="categories__text bg-item-text">
                                    <h4><?= htmlspecialchars($ct2['name']) ?></h4>
                                    <p>
                                        <?php
                                        $useCat = countUsedCategory($ct2['id']);
                                        echo $useCat["used"] . " items";
                                        ?>
                                    </p>
                                    <a href="<?= $domainURL ?>categories/<?= $ct2['id'] ?>">Shop now</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>

                <!-- Swiper Navigation Buttons -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>

        </div>
    </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Swiper Initialization -->
<script>
    var swiper = new Swiper(".myCategorySwiper", {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3,
            },
        },
    });
</script>



<!-- Product Section Begin -->
<!-- Product Section -->
<style>
    section.spad {
        position: relative;
    }

    section.spad .owl-carousel .owl-nav {
        position: absolute !important;
        top: -78px;
        right: 0;
    }

    section.spad .owl-carousel .owl-nav .owl-prev,
    section.spad .owl-carousel .owl-nav .owl-next {
        font-weight: bold !important;
        font-size: 30px !important;
        background: transparent !important;
        color: #000 !important;
        border-radius: 50% !important;
        line-height: 40px !important;
        text-align: center !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: background 0.3s ease !important;
        position: absolute;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 10 !important;
    }

    .best-seller-carousel .owl-nav.disabled {
        display: block !important;
    }



    section.spad .owl-nav .owl-prev {
        right: 30px !important;
    }

    section.spad .owl-nav .owl-next {
        right: 10px !important;
    }



    section.spad .owl-nav .owl-prev:hover,
    section.spad .owl-nav .owl-next:hover {
        background: rgba(0, 0, 0, 0.25) !important;
        border-width: 0px;
    }

    /* Override default dots container */
    .owl-dots {
        text-align: center !important;
        margin-top: 20px;
    }

    /* Hide old default circle styles and use rectangle instead */
    .owl-dot span {
        display: none !important;
        /* hides the original dot circle span */
    }

    .owl-dot {
        width: 30px !important;
        height: 8px !important;
        background-color: #ccc !important;
        display: inline-block !important;
        margin: 5px 4px !important;
        border-radius: 4px !important;
        transition: background 0.3s ease !important;
        border: none !important;
    }

    /* Active rectangle */
    .owl-dot.active {
        background-color: #ff4081 !important;
    }
</style>
<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="section-title">
                    <h4>New Arrival</h4>
                </div>
            </div>
        </div>

        <!-- Owl Carousel Start -->
        <div class="owl-carousel new-arrivals-carousel owl-theme">
            <?php
            while ($rowNew = $newArrival->fetch_array()) {
                $proid = $rowNew["id"];
                $sqlProImage = "SELECT * FROM product_image WHERE product_id='$proid' ORDER BY id ASC LIMIT 1";
                $queryProImage = $conn->query($sqlProImage);
                $rowProImage = $queryProImage->fetch_array();
                $rowPrice = getPriceOnCountry($country, $proid);
                $stock1 = stockBalanceIndividual($proid);
            ?>
                <div class="item mix category<?= $rowNew["category_id"] ?>">
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
                            <div class="label new">New</div>
                            <ul class="product__hover">
                                <li><a href="<?= $domainURL ?>assets/images/products/<?= $rowProImage["image"] ?>"
                                        class="image-popup"><span class="arrow_expand"></span></a></li>
                                <?php
                                if ($stock1["physical_stock"] >= 1) {
                                ?>
                                    <li><a href="<?= $domainURL ?>product-details/<?= $proid ?>"><span class="icon_bag_alt"></span></a></li>
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
                            <h6><a href="<?= $domainURL ?>product-details/<?= $rowNew["id"] ?>"><?= $rowNew["name"] ?></a></h6>
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="product__price">
                                <?= $data["sign"] ?><?= number_format($rowPrice["sale"], 2) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- Owl Carousel End -->
    </div>
</section>

<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="section-title">
                    <h4>Top 8 Best Seller</h4>
                </div>
            </div>
        </div>

        <!-- Owl Carousel Start -->
        <div class="owl-carousel best-seller-carousel owl-theme">
            <?php
            $sql = "
                            SELECT
                                p.id AS product_id,
                                ANY_VALUE(p.name) AS product_name,
                                ANY_VALUE(p.slug) AS slug,
                                ANY_VALUE(p.description) AS description,
                                ANY_VALUE(p.price_capital) AS price_capital,
                                SUM(c.quantity) AS total_sold,
                                ANY_VALUE(v.price_sale) AS price_sale,
                                ANY_VALUE(v.price_retail) AS price_retail,
                                ANY_VALUE(pv.image) AS product_image
                            FROM cart c
                            JOIN products p ON c.p_id = p.id
                            LEFT JOIN product_variants v ON c.pv_id = v.id
                            LEFT JOIN (
                                SELECT product_id, MIN(image) AS image
                                FROM product_variants
                                WHERE image IS NOT NULL AND image != ''
                                GROUP BY product_id
                            ) pv ON pv.product_id = p.id
                            WHERE
                                c.deleted_at IS NULL
                                AND p.status = 1
                                AND p.deleted_at IS NULL
                                AND v.deleted_at IS NULL
                                AND c.status = 1
                            GROUP BY p.id
                            ORDER BY total_sold DESC
                            LIMIT 8
                        ";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bs_productid = $row["product_id"];
                    $bsPrice = getPriceOnCountry($country, $bs_productid);
                    $image = getProductImageSingle($bs_productid);
                    $stock2 = stockBalanceIndividual($bs_productid);
            ?>

                    <div class="item mix ">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" style="position:relative;"
                                data-setbg="<?= $domainURL ?>assets/images/products/<?= $image["image"] ?>" style="position: relative;">
                                <?php
                                if ($stock2["physical_stock"] < 1) {
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
                                <div class="label new">Sold <b><?= $row["total_sold"] ?></b></div>
                                <ul class="product__hover">
                                    <li><a href="<?= $domainURL ?>assets/images/products/<?= $image["image"] ?>"
                                            class="image-popup"><span class="arrow_expand"></span></a></li>
                                    <?php
                                    if ($stock2["physical_stock"] >= 1) {
                                    ?>
                                        <li><a href="<?= $domainURL ?>product-details/<?= $bs_productid ?>"><span class="icon_bag_alt"></span></a></li>
                                    <?php
                                    } else {
                                    ?>
                                        <li><a href="javascript:void(0);" class="disabled"><span class="icon_bag_alt"></span></a></li>
                                    <?php
                                    }
                                    ?>

                                </ul>
                            </div>
                            <div class="product__item__text" style="position:relative;">
                                <?php
                                if ($stock2["physical_stock"] < 1) {
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
                                <h6><a href="<?= $domainURL ?>product-details/<?= $bs_productid ?>"><?= substr($row["product_name"], 0, 30); ?></a></h6>
                                <div class="rating">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                                <div class="product__price">
                                    <?= $data["sign"] ?><?= number_format($bsPrice["sale"], 2) ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No best-selling products found.</p>";
            }
            ?>


        </div>
        <!-- Owl Carousel End -->
    </div>
</section>



<script>
    $(document).ready(function() {
        // New Arrivals Carousel
        $('.new-arrivals-carousel').owlCarousel({
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            margin: 20,
            nav: true,
            dots: true,
            responsive: {
                0: {
                    items: 2
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                }
            }
        });

        // Best Sellers Carousel
        $('.best-seller-carousel').owlCarousel({
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            margin: 20,
            nav: true,
            dots: true,
            responsive: {
                0: {
                    items: 2
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                }
            }
        });

        // Set background images
        $('.set-bg').each(function() {
            var bg = $(this).data('setbg');
            $(this).css('background-image', 'url(' + bg + ')');
        });
    });
</script>



<section class="trend spad">
    <div class="container">
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>PROMO</h4>
                    </div>
                    <div class="row">
                        <?php
                        $sqls = "
                        SELECT 
                            p.id AS product_id,
                            p.name AS product_name,
                            p.slug,
                            cpp.market_price,
                            cpp.sale_price,
                            cpp.country_id
                        FROM list_country_product_price cpp
                        JOIN products p ON cpp.product_id = p.id
                        WHERE 
                            cpp.country_id = '$country'
                            AND cpp.market_price > cpp.sale_price
                            AND p.status = 1
                            AND p.deleted_at IS NULL
                        ORDER BY (cpp.market_price - cpp.sale_price) DESC
                        LIMIT 20
                        ";

                        $results = $conn->query($sqls);


                        // if (!$results) {
                        //     echo "SQL Error: " . $conn->error;
                        //     exit;
                        // }

                        if ($results->num_rows > 0) {
                            while ($rows = $results->fetch_assoc()) {
                                $discount = $rows['market_price'] - $rows['sale_price'];


                                $bs_productids = $rows["product_id"];
                                $bsPrices = getPriceOnCountry($country, $bs_productids);
                                $images = getProductImageSingle($bs_productids);

                                $stock3 = stockBalanceIndividual($bs_productids);

                        ?>
                                <div class="col-lg-4 trend__item">
                                    <div class="row">
                                        <div class="col-6 " style="position:relative;">
                                            <?php
                                            if ($stock3["physical_stock"] < 1) {
                                            ?>
                                                <div style="
                                                    position: absolute;
                                                    left: -10px;
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
                                            <a href="<?= $domainURL ?>product-details/<?= $rows["product_id"] ?>"><img
                                                    src="<?= $domainURL ?>assets/images/products/<?= $images["image"] ?>"
                                                    alt="">
                                                <span class="badge-sale">SALE</span></a>

                                        </div>
                                        <div class="col-6 " style="position:relative;">

                                        <?php
                                        if ($stock3["physical_stock"] < 1) {
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
                                            <h6><a href="<?= $domainURL ?>product-details/<?= $bs_productids ?>"><?= substr($rows["product_name"], 0, 30); ?></a></h6>
                                            <div class="rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                            <div class="product__price" style="text-decoration: line-through;
    color: darkgray;">
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
    </div>
</section>
<!-- Trend Section End -->

<!-- Discount Section Begin -->
<!-- <section class="discount">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="discount__pic">
                    <img src="img/discount.jpg" alt="">
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="discount__text">
                    <div class="discount__text__title">
                        <span>Discount</span>
                        <h2>Summer 2019</h2>
                        <h5><span>Sale</span> 50%</h5>
                    </div>
                    <div class="discount__countdown" id="countdown-time">
                        <div class="countdown__item">
                            <span>22</span>
                            <p>Days</p>
                        </div>
                        <div class="countdown__item">
                            <span>18</span>
                            <p>Hour</p>
                        </div>
                        <div class="countdown__item">
                            <span>46</span>
                            <p>Min</p>
                        </div>
                        <div class="countdown__item">
                            <span>05</span>
                            <p>Sec</p>
                        </div>
                    </div>
                    <a href="#">Shop now</a>
                </div>
            </div>
        </div>
    </div>
</section> -->
<!-- Discount Section End -->

<!-- Services Section Begin -->
<section class="services spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-car"></i>
                    <h6>Fast Shipping</h6>
                    <p>For all oder</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-money"></i>
                    <h6>Money Back Guarantee</h6>
                    <p>If good have Problems</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-support"></i>
                    <h6>Online Support 24/7</h6>
                    <p>Dedicated support</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-headphones"></i>
                    <h6>Payment Secure</h6>
                    <p>100% secure payment</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Services Section End -->

<!-- Instagram Begin -->
<!-- <div class="instagram">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= $domainURL ?>assets/ecom/img/instagram/insta-1.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= $domainURL ?>assets/ecom/img/instagram/insta-2.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= $domainURL ?>assets/ecom/img/instagram/insta-3.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= $domainURL ?>assets/ecom/img/instagram/insta-4.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= $domainURL ?>assets/ecom/img/instagram/insta-5.jpg">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="#">@ ashion_shop</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= $domainURL ?>assets/ecom/img/instagram/insta-6.jpg">
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