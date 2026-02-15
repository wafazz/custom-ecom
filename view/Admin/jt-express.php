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
                            <h6>J&T - Setting</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">


                        <?php
                        if($jt["status"] == "1")
                        {
                            ?>
<p>Now <b>"PRODUCTION API"</b> is <span style="color:green;font-weight:bold;">ACTIVE</span>. If you want to disable <b>"PRODUCTION API"</b> please click button bellow to activate <b>"SANDBOX API"</b>.</p>
<form action="" method="POST">
    <input type="hidden" value="0" name="production_sandbox">
    <button class="btn btn-danger" type="submit" name="saveAPI">Deactivate Productio</button>
</form>
                            <?php
                        }else if($jt["status"] == "0")
                        {
                            ?>
<p>Now <b>"SANDBOX API"</b> is <span style="color:green;font-weight:bold;">ACTIVE</span>. If you want to disable <b>"SANDBOX API"</b> please click button bellow to activate <b>"PRODUCTION API"</b>.</p>
<form action="" method="POST">
    <input type="hidden" value="1" name="production_sandbox">
    <button class="btn btn-success" type="submit" name="saveAPI">Activate Production</button>
</form>
                            <?php
                        }
                        ?>
                        <hr>
                        <h4>Production</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" id="username" value="<?= isset($jt['username_production']) ? $jt['username_production'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passwordP">Password</label>
                                    <input type="text" name="password" id="password" value="<?= isset($jt['password_production']) ? $jt['password_production'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cuscode">Customer Code</label>
                                    <input type="text" name="cuscode" id="cuscode" value="<?= isset($jt['cuscode_production']) ? $jt['cuscode_production'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="key">Key</label>
                                    <input type="text" name="key" id="key" value="<?= isset($jt['key_production']) ? $jt['key_production'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" name="saveProduction">Save Production</button>
                                </div>
                            </div>
                        </form>

                        <h4>Sandbox</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" id="username" value="<?= isset($jt['username_sanbox']) ? $jt['username_sanbox'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passwordP">Password</label>
                                    <input type="text" name="password" id="password" value="<?= isset($jt['password_sandbox']) ? $jt['password_sandbox'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cuscode">Customer Code</label>
                                    <input type="text" name="cuscode" id="cuscode" value="<?= isset($jt['cuscode_sandbox']) ? $jt['cuscode_sandbox'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="key">Key</label>
                                    <input type="text" name="key" id="key" value="<?= isset($jt['key_sandbox']) ? $jt['key_sandbox'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" name="saveSandbox">Save Sandbox</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>