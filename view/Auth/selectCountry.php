<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V3</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="<?= $mainDomain; ?>/assets/assets-login-logout/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/css/util.css">
	<link rel="stylesheet" type="text/css" href="<?= $mainDomain; ?>/assets/assets-login-logout/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url('<?= $mainDomain; ?>assets/assets-login-logout/images/bg-01.jpg');">
			<div class="wrap-login100">
            <form class="login100-form validate-form" autocomplete="off" action="" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                <span class="login100-form-logo">
                    <img class="login-logo" src="<?= $mainDomain; ?>/assets/images/LOGO-ROZYANA-06.png">
                </span>

                <span class="login100-form-title p-b-34 p-t-27">
                    Select Your Country
                </span>

                

                <script>
                $(document).ready(function() {
                    if ($(".errorMessage").is(":visible")) {
                    setTimeout(function() {
                        $(".errorMessage").fadeOut("slow");
                    }, 3000); // 3-second delay
                    }
                });
                </script>

                <!-- EMAIL FIELD -->
                <div class="wrap-input100 validate-input" data-validate="select country">
                    <select class="input100" 
                        name="country" 
                        autocomplete="off" 
                        autocorrect="off" 
                        autocapitalize="none" 
                        spellcheck="false">
                        <option selected disabled readonly value="">select one</option>
                        <?php
                        foreach($listCountry as $row){
                            ?>
                            <option value="<?= $row["id"] ?>"><?= $row["name"] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn" type="submit">
                        Proceed
                    </button>
                </div>

                
                
            </form>

			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logoutvendor/bootstrap/js/popper.js"></script>
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/daterangepicker/moment.min.js"></script>
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="<?= $mainDomain; ?>/assets/assets-login-logout/js/main.js"></script>

</body>
</html>