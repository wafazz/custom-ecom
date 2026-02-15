<?php include "header.php"; ?>
<?php include "menu.php"; ?>
<!-- fashion section start -->



<style>
    .slider-main {
        display: block;
        width: 100%;
        overflow: hidden;
    }

    .banner-slider {
        position: relative;
        width: 100%;
        height: auto;
        padding-top: 31.25%;
    }

    .slider {
        width: 100%;
        height: auto;
        position: absolute;
        top: 0;
        left: 100%; /* All slides start off-screen to the right */
        opacity: 0;
        transition: left 0.5s ease-in-out, opacity 0.5s;
    }

    .slider:first-child {
        left: 0; /* Show first slide initially */
        opacity: 1;
    }

    .slider.active {
        left: 0; /* Active slide moves into view */
        opacity: 1;
        z-index: 1;
    }

    .slider img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Navigation Buttons */
    button.prev, button.next {
        position: absolute;
        /* top: 50%; */
        transform: translateY(-50%);
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        z-index: 10;
        margin-top: -16.75%;
        width: 55px;
        height: 55px;
        background-color: #30302e;
        opacity: 1;
        font-size: 30px;
        color: #ffffff;
        line-height: 37px;
    }

    .prev { left: 10px; }
    .next { right: 10px; }

    .action-btn{
        display: block;
        position: absolute;
        bottom: 5px;
        /* padding-bottom: 50px; */
        margin-left: 50%;
    }
</style>



<div class="slider-main">
    <div class="banner-slider">
        <div class="slider active">
            <img src="<?= $domainURL; ?>images/banner/banner1.png" alt="Banner 1">
            <button class="btn btn-warning action-btn">more &#10095;&#10095;</button>
        </div>
        <div class="slider">
            <img src="<?= $domainURL; ?>images/banner/banner2.png" alt="Banner 2">
            <button class="btn btn-warning action-btn">details &#10095;&#10095;</button>
        </div>
    </div>

    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
</div>


<script>
    $(document).ready(function() {
        let currentIndex = 0;
        let slides = $(".slider");
        let totalSlides = slides.length;

        function showSlide(index) {
            slides.eq(currentIndex).removeClass("active").css({ left: "100%", opacity: 0, "z-index": -1 });

            currentIndex = index;
            slides.eq(currentIndex).css({ left: "-100%", opacity: 0, "z-index": 1 }).addClass("active").animate({ left: "0", opacity: 1 }, 500);
        }

        $(".next").click(function() {
            let nextIndex = (currentIndex + 1) % totalSlides;
            showSlide(nextIndex);
        });

        $(".prev").click(function() {
            let prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            showSlide(prevIndex);
        });

        // Auto Slide
        setInterval(function() {
            $(".next").click();
        }, 5000);
    });
</script>


<div class="fashion_section">
<div class="main_slider">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            
                <div class="container">
                    <!-- <h1 class="fashion_taital">Man & Woman Fashion</h1> -->
                    <div class="fashion_section_2">
                        <style>
                            .flexx {
                                display: flex;
                                justify-content: center; /* Center the columns horizontally */
                                gap: 15px; /* Optional: Space between columns */
                            }
                            .col-lg-3 {
                                /* Optional: Set a fixed width for columns, or it will take 3 grid units by default */
                                flex: 0 0 25%;  /* Equivalent to col-lg-3, but you can adjust the percentage if needed */
                            }
                            .tshirt_img{
                                min-height:0px !important;
                                margin: 0px !important;
                            }
                            .jewellery_img{
                                min-height:0px !important;
                                margin: 10px 0px !important;
                            }
                        </style>
                        <div class="row flexx">
                            <?php if (!empty($category)): ?>
                                    
                                <?php foreach ($category as $cat): ?>
                                    
                                    <div class="col-lg-3 col-sm-3 centerize">
                                        <div class="box_main">
                                        <a href="<?= $domainURL."category/".$cat['id']; ?>">
                                            <h4 class="shirt_text"><?= htmlspecialchars($cat['cat_name']); ?></h4>
                                            <div class="tshirt_img"><img src="https://fusionkeymall.com/assets/images/product/<?= htmlspecialchars($cat['cat_image']); ?>"></div>
                                        </a>
                                            
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            
                        </div>
                    </div>
                </div>
            
        </div>
        <a class="carousel-control-prev" href="#main_slider" role="button" data-slide="prev">
            <i class="fa fa-angle-left"></i>
        </a>
        <a class="carousel-control-next" href="#main_slider" role="button" data-slide="next">
            <i class="fa fa-angle-right"></i>
        </a>
    </div>
</div>
</div>


<!-- fashion section end -->
<!-- electronic section start -->

<!-- jewellery  section start -->
<div class="jewellery_section" style="margin-top: 80px;">
    <div id="jewellery_main_slider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
        <h1 class="fashion_taital">All Product</h1>
        
                                    
            <div class="carousel-item active">
                <div class="container">
                    <div class="fashion_section_2">
                        <div class="row">
                    <?php if (!empty($product)): ?>
                        <?php $count = 0; // Initialize counter?>
                        <?php foreach ($product as $pro): ?>
                            <div class="col-lg-3 col-sm-12">
                                <div class="box_main">
                                    <a href="<?= $domainURL."product-details/".$pro['product_id']; ?>"><div class="jewellery_img"><img src="https://fusionkeymall.com/assets/images/product/<?= $pro['product_image']; ?>"></div></a>
                                    <a href="<?= $domainURL."product-details/".$pro['product_id']; ?>"><p class="" style="margin:0px !important;font-weight:bold;"><?= htmlspecialchars($pro['product_name']); ?></p></a>
                                    
                                    <p class="price_text">Price <span style="color: #262626;">MYR <?= number_format($pro['selling_price'], 2); ?></span></p>
                                    <div class="btn_main">
                                        <div class="buy_bt"><a href="<?= $domainURL."product-details/".$pro['product_id']; ?>">Buy Now</a></div>
                                        <div class="seemore_bt"><a href="<?= $domainURL."product-details/".$pro['product_id']; ?>">See More</a></div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            if ($count % 4 == 0 && $count != 0) {
            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="container">
                    <div class="fashion_section_2">
                        <div class="row">
            <?php
                            }

                            $count++;
                        ?>  
                        <?php endforeach; ?>
                        
                    <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            
        </div>
        <a class="carousel-control-prev" href="#jewellery_main_slider" role="button" data-slide="prev">
            <i class="fa fa-angle-left"></i>
        </a>
        <a class="carousel-control-next" href="#jewellery_main_slider" role="button" data-slide="next">
            <i class="fa fa-angle-right"></i>
        </a>
        <div class="loader_main">
            <div class="loader"></div>
        </div>
    </div>
</div>
<!-- jewellery  section end -->
<?php include "footer.php"; ?>