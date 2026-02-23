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
                            <h6>SenangPay - Setting</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block; margin-left: 20px; margin-right: 20px; margin-bottom: 20px;">

                        <?php if (isset($senangpay['type']) && $senangpay['type'] == 'production'): ?>
                            <p>Now <b>"PRODUCTION"</b> mode is <span style="color:green;font-weight:bold;">ACTIVE</span>. Click button below to switch to <b>"SANDBOX"</b> mode.</p>
                            <form action="" method="POST">
                                <input type="hidden" value="sandbox" name="type">
                                <button class="btn btn-danger" type="submit" name="saveMode">Switch to Sandbox</button>
                            </form>
                        <?php else: ?>
                            <p>Now <b>"SANDBOX"</b> mode is <span style="color:green;font-weight:bold;">ACTIVE</span>. Click button below to switch to <b>"PRODUCTION"</b> mode.</p>
                            <form action="" method="POST">
                                <input type="hidden" value="production" name="type">
                                <button class="btn btn-success" type="submit" name="saveMode">Switch to Production</button>
                            </form>
                        <?php endif; ?>

                        <hr>
                        <h4>Production</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="merchantIdP">Merchant ID</label>
                                    <input type="text" name="merchantIdP" id="merchantIdP" value="<?= htmlspecialchars($senangpay['pro_merchant_id'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="secretKeyP">Secret Key</label>
                                    <input type="text" name="secretKeyP" id="secretKeyP" value="<?= htmlspecialchars($senangpay['pro_secret_key'] ?? '') ?>" class="form-control">
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
                                    <label for="merchantIdS">Merchant ID</label>
                                    <input type="text" name="merchantIdS" id="merchantIdS" value="<?= htmlspecialchars($senangpay['merchant_id'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="secretKeyS">Secret Key</label>
                                    <input type="text" name="secretKeyS" id="secretKeyS" value="<?= htmlspecialchars($senangpay['secret_key'] ?? '') ?>" class="form-control">
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
