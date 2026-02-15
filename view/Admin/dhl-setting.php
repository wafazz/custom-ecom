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
                            <h6>DHL - Setting</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">


                        <?php
                        if($dhl["production_sandbox"] == "1")
                        {
                            ?>
<p>Now <b>"PRODUCTION API"</b> is <span style="color:green;font-weight:bold;">ACTIVE</span>. If you want to disable <b>"PRODUCTION API"</b> please click button bellow to activate <b>"SANDBOX API"</b>.</p>
<form action="" method="POST">
    <input type="hidden" value="2" name="production_sandbox">
    <button class="btn btn-danger" type="submit" name="saveAPI">Deactivate Productio</button>
</form>
                            <?php
                        }else if($dhl["production_sandbox"] == "2")
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
                                    <label for="clientidP">Client ID</label>
                                    <input type="text" name="clientidP" id="clientidP" value="<?= isset($dhl['clientid']) ? $dhl['clientid'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passwordP">Password</label>
                                    <input type="text" name="passwordP" id="passwordP" value="<?= isset($dhl['password']) ? $dhl['password'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="checkbox" id="createToken" name="createToken" value="1"> <label for="createToken">Create new token on Save.</label>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" name="saveProduction">Save</button>
                                </div>
                            </div>
                        </form>

                        <h4>Sandbox</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="clientidS">Client ID</label>
                                    <input type="text" name="clientidS" id="clientidS" value="<?= isset($dhl['clientid_test']) ? $dhl['clientid_test'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passwordS">Password</label>
                                    <input type="text" name="passwordS" id="passwordS" value="<?= isset($dhl['password_test']) ? $dhl['password_test'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" name="saveSandbox">Save</button>
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