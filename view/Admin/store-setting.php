<?php
include "01-header.php";
include "01-menu.php";
?>

<!-- End Navbar -->
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-lg-8 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6><?= $pageName ?></h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display:block; margin:0 20px 20px;">
                        <form action="<?= $domainURL ?>store-setting" method="post" style="padding:10px; border:1px solid #dee2e6; border-radius:5px;">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" value="<?= htmlspecialchars($storeSettings['company_name'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="footer_address" class="form-label">Footer Address</label>
                                    <textarea class="form-control" id="footer_address" name="footer_address" rows="3"><?= htmlspecialchars($storeSettings['footer_address'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="footer_phone" class="form-label">Footer Phone</label>
                                    <input type="text" class="form-control" id="footer_phone" name="footer_phone" value="<?= htmlspecialchars($storeSettings['footer_phone'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="footer_email" class="form-label">Footer Email</label>
                                    <input type="email" class="form-control" id="footer_email" name="footer_email" value="<?= htmlspecialchars($storeSettings['footer_email'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="footer_copyright_text" class="form-label">Copyright Text</label>
                                    <input type="text" class="form-control" id="footer_copyright_text" name="footer_copyright_text" value="<?= htmlspecialchars($storeSettings['footer_copyright_text'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="footer_copyright_url" class="form-label">Copyright URL</label>
                                    <input type="text" class="form-control" id="footer_copyright_url" name="footer_copyright_url" value="<?= htmlspecialchars($storeSettings['footer_copyright_url'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="google_maps_embed" class="form-label">Google Maps Embed URL</label>
                                    <input type="text" class="form-control" id="google_maps_embed" name="google_maps_embed" value="<?= htmlspecialchars($storeSettings['google_maps_embed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                                    <small class="text-muted">Paste the src URL from Google Maps embed iframe</small>
                                </div>
                            </div>

                            <hr>
                            <label class="form-label fw-bold">Payment Channels</label>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="bayarcash_enabled" name="bayarcash_enabled" value="1" <?= ($storeSettings['bayarcash_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="bayarcash_enabled">Bayarcash</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="cod_enabled" name="cod_enabled" value="1" <?= ($storeSettings['cod_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="cod_enabled">COD</label>
                                    </div>
                                </div>
                            </div>

                            <input type="submit" class="btn btn-primary w-100" value="Update & Save">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "01-footer.php"; ?>
