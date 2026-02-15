<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
$conn = getDbConnection();

//echo $cc;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://fusionkeymall.com/assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="https://fusionkeymall.com/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://fusionkeymall.com/assets/dist/css/adminlte.min.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?= $domainURL ?>"><img src="<?= $domainURL ?>assets/images/LOGO-ROZYANA-06.png" style="width:100%;"></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">

    <?php
        $return = $_GET["session_id"];

        $sessionMatch = isset($_SESSION["session_id"]) && $return === $_SESSION["session_id"];

        $sessionid = $_SESSION["session_id"];

        if(isset($_SESSION["session_id"]) && $return === $_SESSION["session_id"]){

          unset($_SESSION["session_id"]);
          $validateTemp = "SELECT * FROM order_temp_data WHERE `session_id`='$sessionid'";
          $query = $conn->query($validateTemp);

          $row = $query->fetch_array();

          //SELECT `id`, `session_id`, `first_name`, `last_name`, `add_1`, `add_2`, `city`, `state`, `postcode`, `country_name`, `country_id`, `phone`, `email`, `remark`, `created_at`, `updated_at`, `deleted_at`, `method`, `currency_sign`, `amount`, `status` FROM `order_temp_data` WHERE 1

          $first_name = $row["first_name"];
          $last_name = $row["last_name"];
          $add_1 = $row["add_1"];
          $add_2 = $row["add_2"];
          $city = $row["city"];
          $state = $row["state"];
          $postcode = $row["postcode"];
          $country_name = $row["country_name"];
          $phone = $row["phone"];
          $email = $row["email"];
          $remark = $row["remark"];
          $method = $row["method"];
          $amount = $row["amount"];
          $currency_sign = $row["currency_sign"];
          $country_id = $row["country_id"];
          $country_name = $row["country_name"];

          $sql = "SELECT * FROM `cart` WHERE `session_id`='$sessionid'AND `deleted_at` IS NULL AND `status` IN(0,1)";
          $query = $conn->query($sql);

          $x=0;
          $string = "";
          $qty = "0";
          while($s = $query->fetch_array()){
            if($x == 0){
              $string = "[".$s["pv_id"]."]";
            }else{
              $string = ",[".$s["pv_id"]."]";
            }
            $qty += $s["quantity"];
            $x++;
          }

          if($method == "fpx"){
            $patMet = "Online Banking";
          }else if($method == "card"){
            $patMet = "Credit/Debit Card";
          }

          $product_var_id = $string;

          $sqlAdd = "INSERT INTO `customer_orders`(`id`, `session_id`, `order_to`, `product_var_id`, `total_qty`, `total_price`, `postage_cost`, `currency_sign`, `country_id`, `country`, `state`, `city`, `postcode`, `address_2`, `address_1`, `customer_name`, `customer_name_last`, `customer_phone`, `customer_email`, `status`, `payment_channel`, `payment_code`, `payment_url`, `ship_channel`, `courier_service`, `awb_number`, `tracking_url`, `created_at`, `updated_at`, `deleted_at`, `remark_comment`, `tracking_milestone`, `to_myr_rate`, `myr_value_include_postage`, `myr_value_without_postage`) VALUES (NULL,'$sessionid','1','$product_var_id','$qty','$amount','0.00','$currency_sign','$country_id','$country_name','$state','$city','$postcode','$add_2','$add_1','$first_name','$last_name','$phone','$email','1','$patMet','Stripe Channel','Stripe Channel','','','','','','','','','','','','')";
        }


    ?>
    <script>
  document.addEventListener('DOMContentLoaded', function () {
    <?php if ($sessionMatch): ?>
      Swal.fire({
        icon: 'success',
        title: 'Payment Successful',
        text: 'Thank you for your order!',
        confirmButtonText: 'Continue'
      }).then(() => {
        window.location.href = '<?= $domainURL ?>main';
      });
    <?php else: ?>
      Swal.fire({
        icon: 'error',
        title: 'Payment Failed',
        text: 'Something went wrong. Please try again or contact support.',
        confirmButtonText: 'Retry'
      }).then(() => {
        window.location.href = '<?= $domainURL ?>checkout';
      });
    <?php endif; ?>
  });
</script>

    
        
        
      

      
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="https://fusionkeymall.com/assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://fusionkeymall.com/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://fusionkeymall.com/assets/dist/js/adminlte.min.js"></script>
</body>
</html>
