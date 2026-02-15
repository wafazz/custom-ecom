<?php include "header.php"; ?>
<?php include "menu.php"; ?>
<!-- fashion section start -->
<div class="fashion_section" style="background: #fff;
    margin-top: -190px;
    padding-top: 30px;">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="container">
            
            
            <div class="row">
                <style>
                    .login-form{
                        display: block;
                        max-width: 900px;
                        width: calc(100% - 40px);
                        margin-left: auto;
                        margin-right: auto;
                        padding: 10px 20px;
                        border: 1px solid;
                        border-radius: 5px;
                    }

                    .login-form ul li{
                        display: inline-block;
                        float: left;
                        background: #f39b07;
                        padding: 5px 10px;
                        cursor: pointer;
                        color: #fff;
                        margin-right:5px;
                        margin-bottom:5px;
                    }

                    .member-tab{
                        display: block;
                        background: bisque;
                        padding: 10px 15px;
                        border: 1px solid orange;
                        border-radius: 5px;
                        margin: 3px;
                        text-align: center;
                    }
                    
                </style>
                <div class="col-12">
                    
                    <div class="login-form">
                    <div class="row" style="margin-bottom:20px;">
                        <div class="col-12">
                        <ul>
                            <li class="lgn"><a href="<?= $domainURL; ?>my-account">Main</a></li>
                            <li class="lgn"><a href="<?= $domainURL; ?>my-profile">Profile</a></li>
                            <li class="lgn"><a href="<?= $domainURL; ?>">Buy Now</a></li>
                            <li class="lgn"><a href="<?= $domainURL; ?>my-order">Order History</a></li>
                            <li class="lgn"><a href="<?= $domainURL; ?>my-point">Point History</a></li>
                        </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-lg-3 col-12">
                                    <div class="member-tab">
                                        <h4>Active Point</h4>
                                        <h3><?= $memberPoint["point"]; ?></h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="member-tab">
                                        <h4>Expired Point</h4>
                                        <h3 style="color:red;"><?= $memberPoint["pointExp"]; ?></h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="member-tab">
                                        <h4>My Order </h4>
                                        <h3><?= $memberPoint["countOrder"]; ?></h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="member-tab">
                                        <h4>My Order Value</h4>
                                        <h3><?= number_format($memberPoint["orderAmount"], 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $( document ).ready(function() {
                            $(".lgn").click(function(){
                                $(this).addClass( "activee");
                                $(".reg").removeClass( "activee");
                                $(".form-login").show();
                                $(".form-register").hide();
                            });
                            $(".reg").click(function(){
                                $(this).addClass( "activee");
                                $(".lgn").removeClass( "activee");
                                $(".form-login").hide();
                                $(".form-register").show();
                            });


                        });
                    </script>
                    
                    </div>
                   
                </div>
            
            </div>
            
               
            
        </div>
       
    </div>
</div>
<!-- fashion section end -->
<!-- electronic section start -->

<!-- jewellery  section start -->

<!-- jewellery  section end -->
<?php include "footer.php"; ?>