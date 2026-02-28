<?php
include "01-header.php";
include "01-menu.php";
?>

<!-- End Navbar -->
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-lg-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>NinjaVan - Setting</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display:block;margin-left:20px;margin-right:20px;margin-bottom:20px;">

                        <?php if ($ninjavan["status"] == "1") { ?>
                            <p>Now <b>"PRODUCTION API"</b> is <span style="color:green;font-weight:bold;">ACTIVE</span>. If you want to disable <b>"PRODUCTION API"</b> please click button below to activate <b>"SANDBOX API"</b>.</p>
                            <form action="" method="POST">
                                <input type="hidden" value="0" name="production_sandbox">
                                <button class="btn btn-danger" type="submit" name="saveAPI">Deactivate Production</button>
                            </form>
                        <?php } else if ($ninjavan["status"] == "0") { ?>
                            <p>Now <b>"SANDBOX API"</b> is <span style="color:green;font-weight:bold;">ACTIVE</span>. If you want to disable <b>"SANDBOX API"</b> please click button below to activate <b>"PRODUCTION API"</b>.</p>
                            <form action="" method="POST">
                                <input type="hidden" value="1" name="production_sandbox">
                                <button class="btn btn-success" type="submit" name="saveAPI">Activate Production</button>
                            </form>
                        <?php } ?>

                        <hr>
                        <h4>Production</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username_p">Client ID</label>
                                    <input type="text" name="username" id="username_p" value="<?= isset($ninjavan['username_production']) ? $ninjavan['username_production'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="key_p">Client Secret</label>
                                    <input type="text" name="key" id="key_p" value="<?= isset($ninjavan['key_production']) ? $ninjavan['key_production'] : '' ?>" class="form-control">
                                </div>
                                <input type="hidden" name="password" value="">
                                <input type="hidden" name="cuscode" value="">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="createToken" id="createTokenP" value="1">
                                        <label class="form-check-label" for="createTokenP">Create new token on Save</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" name="saveProduction">Save Production</button>
                                </div>
                            </div>
                        </form>

                        <hr>
                        <h4>Sandbox</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username_s">Client ID</label>
                                    <input type="text" name="username" id="username_s" value="<?= isset($ninjavan['username_sanbox']) ? $ninjavan['username_sanbox'] : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="key_s">Client Secret</label>
                                    <input type="text" name="key" id="key_s" value="<?= isset($ninjavan['key_sandbox']) ? $ninjavan['key_sandbox'] : '' ?>" class="form-control">
                                </div>
                                <input type="hidden" name="password" value="">
                                <input type="hidden" name="cuscode" value="">
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

    <?php include "01-footer.php"; ?>
