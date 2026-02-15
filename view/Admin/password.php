<?php
include "01-header.php";
include "01-menu.php";
?>



<!-- End Navbar -->
<div class="container-fluid py-4">
    <div class="row">

    </div>
    <div class="row my-4">
        <div class="col-lg-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6><?= $pageName ?></h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">
                        <form id="" action="<?= $domainURL ?>my-pass" method="post" style="padding: 10px;
    border: 1px solid red;
    border-radius: 5px;margin-bottom:15px;">
                            

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cpass" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="cpass" name="cpass" value="" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="npass" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="npass" name="npass" value="" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cnpass" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="cnpass" name="cnpass" value="" required>
                                </div>
                            </div>



                            <input type="submit" class="btn btn-primary w-100" value="Update & Save"></input>
                        </form>



                    </div>
                </div>
            </div>

            
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>