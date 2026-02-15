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

                        <h5>Logo</h5>

                        <div class="row">
                            <?php
                            foreach ($rows as $row) {

                                if ($row["sorting"] == "1") {
                                    $pointer = "no-drop";
                                    $urll = "";
                                } else {
                                    $pointer = "pointer";
                                    $urll = "href='{$domainURL}set-logo?id={$row["id"]}'";
                                }
                            ?>
                                <div class="col-lg-3 col-md-6">
                                    <div style="margin: 10px;
    padding: 10px;
    background: #f4f4f4;
    border-radius: 10px;position:relative;cursor:<?= $pointer ?>;">
                                        <a <?= $urll ?>><img src="<?= $domainURL ?><?= $row["image_path"] ?>" style="width:100%;">
                                            <?php
                                            if ($row["sorting"] == "1") {
                                            ?>
                                                <i class="fa-solid fa-check-to-slot" style="color: green;
    font-size: 24px;
    position: absolute;
    left: 10px;"></i>
                                            <?php
                                            }
                                            ?>
                                        </a>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <span class="btn btn-info addLogo"><i class="fa-solid fa-plus"></i> New Logo</span>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row logoForm" style="display:none;">
                                <div class="col-lg-12">
                                    <h6>Upload New Logo</h6>
                                </div>
                                <div class="col-lg-8" style="margin-bottom:15px;">
                                    <input class="form-control" type="file" name="file[]">
                                    <small><i>Best image size is <b>500px x 118px</b>.</i></small>
                                </div>
                                <div class="col-lg-4">
                                    <input type="checkbox" name="defaultLogo" value="1" style="margin-bottom:15px;"> Set as default logo
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" name="uploadLogo" class="btn btn-primary">Upload Now</button>
                                </div>
                            </div>
                        </form>
                        <script>
                            $(document).on('click', '.addLogo', function() {
                                let $btn = $(this);
                                let $icon = $btn.find('i');

                                $('.logoForm').slideToggle(200); // Show/hide form

                                if ($btn.hasClass('btn-info')) {
                                    // Switch to red with minus icon
                                    $btn.removeClass('btn-info').addClass('btn-danger');
                                    $icon.removeClass('fa-plus').addClass('fa-minus');
                                } else {
                                    // Switch back to blue with plus icon
                                    $btn.removeClass('btn-danger').addClass('btn-info');
                                    $icon.removeClass('fa-minus').addClass('fa-plus');
                                }
                            });
                        </script>
                        <hr style="border: 1px solid;">

                    </div>
                </div>
            </div>


        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>