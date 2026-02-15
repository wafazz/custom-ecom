<!DOCTYPE html>
<html lang="en">
<head>
	<title>Rozeyana.com || Login</title>
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
                    <img class="login-logo" src="<?= $mainDomain; ?><?= $row["image_path"] ?>">
                </span>

                <span class="login100-form-title p-b-34 p-t-27">
                    Log in
                </span>

                <?php if ($errorMessage): ?>
                    <p class="errorMessage"><?php echo $errorMessage; ?></p>
                <?php endif; ?>

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
                <div class="wrap-input100 validate-input" data-validate="Enter your email">
                    <input class="input100" 
                        type="email" 
                        name="user_email" 
                        placeholder="Enter your email"
                        autocomplete="off" 
                        autocorrect="off" 
                        autocapitalize="none" 
                        spellcheck="false">
                    <span class="focus-input100" data-placeholder="&#x2709;"></span>
                </div>

                <!-- PASSWORD FIELD -->
                <div class="wrap-input100 validate-input" data-validate="Enter your password">
                    <input class="input100" 
                        type="password" 
                        name="user_pass" 
                        placeholder="Enter your password"
                        autocomplete="new-password"
                        autocorrect="off" 
                        autocapitalize="none" 
                        spellcheck="false">
                    <span class="focus-input100" data-placeholder="&#xf191;"></span>
                </div>

                <div class="contact100-form-checkbox">
                    <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
                    <label class="label-checkbox100" for="ckb1">
                        Remember me
                    </label>
                </div>

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn" type="submit">
                        Login
                    </button>
                </div>

                <div class="text-center p-t-30 p-d-30">
                    <a class="txt1" href="#">
                        Forgot Password?
                    </a>
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