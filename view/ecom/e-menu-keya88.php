<?php
$sqlLogo = "SELECT * FROM `image_setting` WHERE `use_type`='logo' AND sorting='1'";
$queryLogo = $conn->query($sqlLogo);
$rowLogo = $queryLogo->fetch_assoc();
?>

<body>
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
    <i id="showcounter" class="fa-solid fa-chevron-right" style="display:none;position: fixed;
        z-index: 999;
        background: rgba(0,0,0,0.8);
        padding: 5px 10px;
        margin-top: 82px;
        color: white;
    cursor: pointer;" aria-hidden="true"></i>

    <i id="showcounter10" onclick="window.location.href = '<?= $domainURL ?>checkout'" class="fa-solid fa-cart-arrow-down" style="
                    position: fixed;
                    z-index: 999;
                    cursor: pointer;
                    margin-top: 118px;
                    background: black;
                    color: #fff;
                    padding: 15px;
                    display:none;
                ">
                    <span id="count11" style="
                        position: absolute;
                        font-size: 11px;
                        top: 4px;
                        right: 2px;
                        background: red;
                        padding: 5px;
                        border-radius: 10px;
                    "><?= $countCart["count"] ?></span>
                </i>
    <div id="thecounter" style="
        position: fixed;
        z-index: 999;
        background: rgba(0,0,0,0.7);
        padding: 5px 10px;
        margin-top: 82px;
        color: white;
    ">
        <div style="position:relative;">
            <i id="hidecounter" class="fa-solid fa-chevron-left" style="position: absolute;
    right: -40px;
    background: rgba(0, 0, 0, 0.8);
    top: -5px;
    padding: 5px 5px 5px 15px;
    cursor: pointer;"></i>

            <div style="
                    position: absolute;
                    right: -62px;
                    top: 30px;
                    font-size: 20px;
                    background: black;
                    padding: 10px 15px;
                ">
                <i onclick="window.location.href = '<?= $domainURL ?>checkout'" class="fa-solid fa-cart-arrow-down" style="
                    position: relative;
                    cursor: pointer;
                ">
                    <span id="count10" style="
                        position: absolute;
                        font-size: 11px;
                        top: -11px;
                        right: -13px;
                        background: red;
                        padding: 5px;
                        border-radius: 10px;
                    "><?= $countCart["count"] ?></span>
                </i>
            </div>

        </div>
        <h5 style="
            color: red;
            font-weight: bold;
        ">
            Live Counter
        </h5>
        <span style="
            color: greenyellow;
        ">
            <i class="fa-regular fa-eye" aria-hidden="true"></i> <span id="liveOnlibe">0</span> <span class="blink">Live</span></span>
        <br>
        <span id="liveToday" style="font-weight:bold;">0</span> <span class="blinks">Today Visitors</span>

        <br>
        <span id="liveUser" style="font-weight:bold;">0</span> <span class="blinks">Unique Visitors</span>

        <br>
        <span style="color:white;font-weight:bold;font-size:12px;">Since: <i>14 Aug 2025</i></span>
    </div>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Offcanvas Menu Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="offcanvas__close">+</div>
        <ul class="offcanvas__widget">
            <li><span class="icon_search search-switch"></span></li>
            <li class="cart-dropdown" style="position: relative;">
                <a href="javascript:void(0);" class="cart-toggle">
                    <span class="icon_bag_alt"></span>
                    <div class="tip qty-cartp1" id="count1"><?= $countCart["count"] ?></div>
                </a>

                <!-- Dropdown outside the <a> -->
                <ul class="cart-item"
                    style="display: none; position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #ccc; width: 250px; z-index: 999;margin-left:25px !important;">
                    <span class="list-add-cart1">
                        <?php
                        if ($listCart->num_rows < "1") {
                        ?>
                            <li style="color:grey !important;">no item in cart</li>
                        <?php
                        } else {
                        ?>
                            <li><b>CART ITEMS</b></li>
                            <?php
                            while ($row = $listCart->fetch_array()) {

                                $product = GetProductDetails($row["p_id"]);

                            ?>
                                <li>
                                    <table style="width:100%;">
                                        <tr>
                                            <td><?= $product["name"] ?></td>
                                            <td style="width:40px;vertical-align:top;text-align:right;">x<b><?= $row["quantity"] ?></b></td>
                                        </tr>
                                    </table>
                                </li>
                            <?php

                            }
                            ?>
                            <li><button class="btn btn-dark" onClick="window.location.href = '<?= $domainURL ?>checkout'">CHECKOUT</button></li>
                        <?php
                        }
                        ?>
                    </span>
                </ul>
            </li>
        </ul>
        <div class="offcanvas__logo">
            <a href="<?= $domainURL ?>"><img src="<?= $domainURL ?><?= $rowLogo["image_path"] ?>" alt=""></a>
        </div>
        <div id="mobile-menu-wrap"></div>
        <div class="offcanvas__auth">
            <a><?= $data["name"] ?> (<?= $data["sign"] ?>)</a>
            <a href="<?= $domainURL ?>change-country">Change Country</a>
        </div>
    </div>
    <!-- Offcanvas Menu End -->

    <!-- Header Section Begin -->
    <header class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-lg-2">
                    <div class="header__logo">
                        <a href="<?= $domainURL ?>"><img src="<?= $domainURL ?><?= $rowLogo["image_path"] ?>"
                                alt=""></a>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-7">
                    <nav class="header__menu">
                        <ul>
                            <li class="active"><a href="<?= $domainURL ?>">Home</a></li>

                            <li><a href="#">Brands</a>
                                <ul class="dropdown">
                                    <?php
                                    while ($br = $brands->fetch_array()) {
                                    ?>
                                        <li><a href="<?= $domainURL ?>brands/<?= $br["id"] ?>"><?= $br["name"] ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li><a href="#">Categories</a>
                                <ul class="dropdown">
                                    <?php
                                    while ($ct = $categories->fetch_array()) {
                                    ?>
                                        <li><a href="<?= $domainURL ?>categories/<?= $ct["id"] ?>"><?= $ct["name"] ?></a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li><a href="<?= $domainURL ?>promo-item">Promos</a></li>
                            <li><a href="<?= $domainURL ?>checkout">Checkout</a></li>

                            <li><a href="#">More...</a>
                                <ul class="dropdown">

                                    <li><a href="<?= $domainURL ?>track-order">Tracking</a></li>
                                    <li><a href="<?= $domainURL ?>contact">Contact</a></li>
                                    <li><a href="<?= $domainURL ?>customer/support-ticket">Support Tickets</a></li>

                                </ul>
                            </li>

                            <!-- <li><a href="<?= $domainURL ?>all-items">Shop</a></li> -->

                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="header__right">

                        <table>
                            <tr>
                                <td>
                                    <div class="header__right__auth">
                                        <a><?= $data["name"] ?> (<?= $data["sign"] ?>)</a>
                                        <a href="<?= $domainURL ?>change-country">Change Country</a>
                                    </div>
                                </td>
                                <td>
                                    <ul class="header__right__widget">
                                        <li><span class="icon_search search-switch"></span></li>
                                        <li class="cart-dropdown" style="position: relative;">
                                            <a href="javascript:void(0);" class="cart-toggle">
                                                <span class="icon_bag_alt"></span>
                                                <div class="tip qty-cart2" id="count2"><?= $countCart["count"] ?></div>
                                            </a>

                                            <!-- Dropdown outside the <a> -->
                                            <ul class="cart-item"
                                                style="display: none; position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #ccc; width: 250px; z-index: 999;">
                                                <span class="list-add-cart2">
                                                    <?php
                                                    if ($listCart2->num_rows < "1") {
                                                    ?>
                                                        <li style="color:grey !important;">no item in cart</li>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <li><b>CART ITEMS</b></li>
                                                        <?php
                                                        while ($rows = $listCart2->fetch_array()) {

                                                            $products = GetProductDetails($rows["p_id"]);

                                                        ?>
                                                            <li>
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td><?= $products["name"] ?></td>
                                                                        <td style="width:40px;vertical-align:top;text-align:right;">x<b><?= $rows["quantity"] ?></b></td>
                                                                    </tr>
                                                                </table>
                                                            </li>
                                                        <?php

                                                        }
                                                        ?>
                                                        <li><button class="btn btn-dark" onClick="window.location.href = '<?= $domainURL ?>checkout'">CHECKOUT</button></li>
                                                    <?php
                                                    }
                                                    ?>
                                                </span>
                                            </ul>
                                        </li>
                                    </ul>
                                    <script>
                                        $(document).ready(function() {
                                            // Toggle on click or touch
                                            $(document).on('click touchend', '.cart-toggle', function(e) {
                                                e.preventDefault();
                                                e.stopPropagation();

                                                const $dropdown = $(this).closest('.cart-dropdown').find('.cart-item');
                                                $('.cart-item').not($dropdown).slideUp(150); // close others
                                                $dropdown.stop(true, true).slideToggle(150);
                                            });

                                            // Close when clicking outside
                                            $(document).on('click touchend', function(e) {
                                                if (!$(e.target).closest('.cart-dropdown').length) {
                                                    $('.cart-item').slideUp(150);
                                                }
                                            });
                                        });
                                    </script>
                                </td>
                            </tr>
                        </table>




                    </div>
                </div>
            </div>
            <div class="canvas__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header Section End -->