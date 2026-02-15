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
                            <h6>ACCESS DENIED</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">

                        <div class="text-center p-4">
                            <i class="fa fa-lock fa-3x text-danger mb-3"></i>
                            <h4 class="text-danger">Access Denied</h4>
                            <p class="text-muted">
                                You do not have the necessary permissions to access this page.
                                <br>
                                If you believe this is an error, please contact your system administrator.
                            </p>
                            <a href="<?= $domainURL ?>dashboard" class="btn btn-primary mt-3">
                                <i class="fa fa-lock"></i> Access Denied
                            </a>
                        </div>



                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>