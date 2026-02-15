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

                    <span>
                        Track Your Order
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

            <div class="col-lg-5">
                <label>Email</label>
                <input type="email" id="t_email" class="form-control" style="margin-bottom:15px;">
            </div>
            <div class="col-lg-5">
                <label>Order No.</label>
                <input type="number" min="1" step="1" id="t_orderid" class="form-control" style="margin-bottom:15px;">
            </div>
            <div class="col-lg-2">
                <button class="btn btn-danger" id="trackorder" style="margin-top: 31px;
    width: 100%;">Track</button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div id="track-result"></div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                $("#trackorder").click(function(){
                    var temail = $("#t_email").val();
                    var torderid = $("#t_orderid").val();

                    $("#track-result").load("<?= $domainURL ?>tracks?id=" + torderid + "&email=" + temail);

                })
            });
        </script>

    </div>
</section>


<?php
include "e-footer-keya88.php";
?>