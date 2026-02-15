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
                        max-width: 500px;
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
                        border-left: 1px solid #f39b07;
                        border-right: 1px solid #f39b07;
                        border-bottom: 1px solid #f39b07;
                    }
                    .activee{
                        background: #fff !important;
                        color:#f39b07 !important;
                        font-weight: bold !important;;
                        cursor: no-drop !important;;
                    }
                </style>
                <div class="col-12">
                    <?php
                        if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"])){
                        ?>
                        <style>
                            .afterLogin{
                                display: inline-block;
                                margin-bottom: 5px;
                                margin-right: 5px;
                            }
                        </style>
                        <?= $dataMember["name"]; ?>, You are already signed.
                        <br>
                        <br>
                        <a href="<?= $domainURL; ?>my-account" class="btn btn-info afterLogin">My Account</a>
                        <a href="<?= $domainURL; ?>" class="btn btn-info afterLogin">Shop Homepage</a>
                        <a href="<?= $domainURL; ?>cart" class="btn btn-info afterLogin">Cart Page</a>
                        <a href="<?= $domainURL; ?>logout" class="btn btn-danger afterLogin">Logout</a>
                        <?php
                        }else{
                    ?>
                    <div class="login-form">
                    <div class="row" style="margin-bottom:20px;">
                        <div class="col-12">
                        <ul>
                            <li class="lgn activee">Login</li>
                            <li class="reg">Register</li>
                        </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                        <?php
                        if(isset($_SESSION["errorLogin"])){
                            $loginShow = "";
                            $regShow = 'style="display:none;"';
                            ?>
                            <style>
                                .errorSign li{
                                    background: red !important;
    display: block !important;
    width: 100% !important;
    float:none;
                                }
                                .errorSign{
                                    margin-bottom:10px !important;
                                }
                            </style>
                            <ul class="errorSign">
                                <?= $_SESSION["errorLogin"]; ?>
                            </ul>
                            <?php
                            unset($_SESSION["errorLogin"]);
                        }else if(isset($_SESSION["errorReg"])){
                            $regShow = "";
                            $loginShow = 'style="display:none;"';
                            ?>
                            <style>
                                .errorSign li{
                                    background: red !important;
    display: block !important;
    width: 100% !important;
    float:none;
                                }
                                .errorSign{
                                    margin-bottom:10px !important;
                                }
                            </style>
                            <ul class="errorSign">
                                <?= $_SESSION["errorReg"]; ?>
                            </ul>
                            <?php
                            unset($_SESSION["errorReg"]);
                        }else{

                            $loginShow = "";
                            $regShow = 'style="display:none;"';
                        }
                        ?>
                        <form action="<?= $domainURL; ?>login" method="post" class="form-login" <?= $loginShow; ?>>

                        Email:
                        <input class="form-control" type="email" name="email" style="margin-bottom:15px;">
                        Password:
                        <input class="form-control" type="password" name="password" style="margin-bottom:15px;">
                        <button type="submit" name="submitLogin" class="btn btn-warning">Login</button>

                        </form>
                        <form action="<?= $domainURL; ?>signup" method="post" class="form-register" <?= $regShow; ?>>

                        Full Name:
                        <input class="form-control" type="text" name="full-name" style="margin-bottom:15px;" placeholder="full name">
                        Phone No:
                        <div class="row" style="margin-bottom:15px;">
                            <div class="col-4" style="padding-right: 0px;">
                            <select class="form-control" name="ccode">
                                <option value="60">60</option>
                                <option value="60">65</option>
                            </select>
                            </div>
                            <div class="col-8" style="padding-left: 0px;">
                            <input class="form-control" type="number" name="phone" placeholder="phone no">
                            </div>
                            <div class="col-12" style="padding-left: 0px;">
                            <small style="display: block;
    margin-left: 20px;"><i style="color:red;">Important reminder:<br>Please use whatsapp phone number, because we will sent TAC through whatsapp.</i></small>
                            </div>
                        </div>
                        Email:
                        <input class="form-control" type="email" name="email" style="margin-bottom:15px;" placeholder="email">
                        Password:
                        <input class="form-control" type="password" name="password" style="margin-bottom:15px;" placeholder="password">
                        Confirm Password:
                        <input class="form-control" type="password" name="cpassword" style="margin-bottom:15px;" placeholder="confirm password">
                        <button type="submit" name="submitRegister" class="btn btn-warning">Register</button>

                        </form>
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
                    <?php
                        }
                    ?>
                    
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