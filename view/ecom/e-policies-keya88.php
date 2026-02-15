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
                    Policy
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
            
            <div class="col-lg-12 conts">
            <?= $row["description"] ?>
            </div>
        </div>
        
    </div>
</section>


<?php
include "e-footer-keya88.php";
?>