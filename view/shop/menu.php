<body>
    <!-- banner bg main start -->
     <style>
        .logo{
            padding: 15px 0px !important;
        }
     </style>
    <div class="banner_bg_main">
        <!-- header top section start -->
        <div class="container">
            <div class="header_section_top">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="custom_menu">
                            <span class="refer">Refer by: <span class="referBold"><?= $_SESSION["referName"]; ?></span></span>
                            <ul>
                                <?php if (!empty($category)): ?>
                                
                                    <?php foreach ($category as $cat): ?>
                                        <li><a href="<?= $domainURL."category/".$cat['id']; ?>"><?= htmlspecialchars($cat['cat_name']); ?></a></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <li><a href="<?= $domainURL."cart"; ?>">Cart</a></li>
                                <li><a href="<?= $domainURL."checkout"; ?>">Checkout</a></li>
                                <li class="my-account">
                                    <?php
                                    if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"]))
                                    {
                                        ?>
                                        <a href="<?= $domainURL."my-account"; ?>">My Account</a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="<?= $domainURL."secure-account"; ?>">Login/Register</a>
                                        <?php
                                    }
                                    ?>
                                </li>
                            </ul>
                            <?php
                                if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"]))
                                {
                                    $memeberDetails = memberData($_SESSION["membership"]);
                                    ?>
                                    <div class="row">
                                        <div class="col-12" style="color:#fff;">
                                        Welcome back <b><?= $memeberDetails["name"]; ?></b>. [<a style="color:red;" href="<?= $domainURL; ?>logout">Logout</a>]
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- header top section start -->
        <!-- logo section start -->
        <div class="logo_section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="logo"><a href="<?= $domainURL; ?>"><img src="https://fusionkeymall.com/assets/images/fkm-new.png" style="display: block;
    max-width: 300px;
    width: calc(100% - 100px);
    margin-left: auto;
    margin-right: auto;"></a></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- logo section end -->
        <!-- header section start -->
        <div class="header_section">
            <div class="container">
                <div class="containt_main">
                    <div id="mySidenav" class="sidenav">
                        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                        <a href="<?= $domainURL; ?>">Home</a>
                        <?php if (!empty($category)): ?>
                                
                                <?php foreach ($category as $cat): ?>
                                    <a href="<?= $domainURL."category/".$cat['id']; ?>"><?= htmlspecialchars($cat['cat_name']); ?></a>
                                <?php endforeach; ?>
                        <?php endif; ?>
                        <a href="<?= $domainURL; ?>cart">Cart</a>
                        <a href="<?= $domainURL; ?>checkout">Checkout</a>
                        <?php
                        if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"]))
                        {
                            ?>
                            <a href="<?= $domainURL."my-account"; ?>">My Account</a>
                            <?php
                        }else{
                            ?>
                            <a href="<?= $domainURL."secure-account"; ?>">Login/Register</a>
                            <?php
                        }
                        ?>
                        <!-- <a href="fashion.html">Fashion</a>
                        <a href="electronic.html">Electronic</a>
                        <a href="jewellery.html">Jewellery</a> -->
                    </div>
                    <span class="toggle_icon" onclick="openNav()"><img src="<?= $domainURL; ?>images/toggle-icon.png"></span>
                    
                    <div class="main">
                        <!-- Another variation with a button -->
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search products">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="button"
                                    style="background-color: #f26522; border-color:#f26522 ">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="header_box">
                        
                        <div class="login_menu">
                            <ul>
                                <li><a href="<?= $domainURL; ?>cart" style="position:relative;font-size: 22px;">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <span class="cart-qty" style="position: absolute;
    font-size: 13px;
    min-width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
    background: chocolate;
    border-radius: 5px;
    width: fit-content;">0</span>
                                    <script>
                                        $( document ).ready(function() {
                                            $(".cart-qty").load("<?= $domainURL; ?>data-cart");
                                        });
                                    </script>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- header section end -->
        <!-- banner section start -->
        <div class="banner_section layout_padding">
            <div class="container">
                <div id="my_slider" class="carousel slide" data-ride="carousel">
                <?php
                    if($pageid == 1){
                    ?>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h1 class="banner_taital">Get Start <br>Your favriot shoping</h1>
                                    <div class="buynow_bt"><a href="#">Buy Now</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h1 class="banner_taital">Get Start <br>Your favriot shoping</h1>
                                    <div class="buynow_bt"><a href="#">Buy Now</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h1 class="banner_taital">Get Start <br>Your favriot shoping</h1>
                                    <div class="buynow_bt"><a href="#">Buy Now</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#my_slider" role="button" data-slide="prev">
                        <i class="fa fa-angle-left"></i>
                    </a>
                    <a class="carousel-control-next" href="#my_slider" role="button" data-slide="next">
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <?php
                    }else{
                    ?>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h1 class="banner_taital" style="margin-bottom: 150px !important;"><?= $pageName; ?></h1>
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
        <!-- banner section end -->
    </div>
    <!-- banner bg main end -->