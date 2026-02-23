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
                            <h6>Bayarcash - Setting</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block; margin-left: 20px; margin-right: 20px; margin-bottom: 20px;">

                        <?php if (isset($bayarcash['type']) && $bayarcash['type'] == 'production'): ?>
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
                                <div class="col-md-4 mb-3">
                                    <label for="prodApiToken">API Token</label>
                                    <input type="text" name="prodApiToken" id="prodApiToken" value="<?= htmlspecialchars($bayarcash['api_token'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="prodSecretKey">Secret Key</label>
                                    <input type="text" name="prodSecretKey" id="prodSecretKey" value="<?= htmlspecialchars($bayarcash['secret_key'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="prodPortalKey">Portal Key</label>
                                    <input type="text" name="prodPortalKey" id="prodPortalKey" value="<?= htmlspecialchars($bayarcash['portal_key'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" name="saveProduction">Save</button>
                                </div>
                            </div>
                        </form>

                        <h4>Sandbox</h4>
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="sandboxApiToken">API Token</label>
                                    <input type="text" name="sandboxApiToken" id="sandboxApiToken" value="<?= htmlspecialchars($bayarcash['sandbox_api_token'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="sandboxSecretKey">Secret Key</label>
                                    <input type="text" name="sandboxSecretKey" id="sandboxSecretKey" value="<?= htmlspecialchars($bayarcash['sandbox_secret_key'] ?? '') ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="sandboxPortalKey">Portal Key</label>
                                    <input type="text" name="sandboxPortalKey" id="sandboxPortalKey" value="<?= htmlspecialchars($bayarcash['sandbox_portal_key'] ?? '') ?>" class="form-control">
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
