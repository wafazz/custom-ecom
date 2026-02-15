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
                            <h6>Add New Country</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">

                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;overflow-x: auto;">
                        <style>
                            thead th {
                                text-align: center !important;
                            }

                            #categoryTable td {
                                text-align: center;
                                vertical-align: top;
                            }

                            /* EXCEPTION: left-align the "name" column (2nd column) */
                            #categoryTable td:nth-child(2) {
                                text-align: left;
                                vertical-align: top;
                            }
                        </style>

                        <form action="" method="post">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label>Country</label>
                                    <select class="form-control" name="country" id="country" required>
                                        <option value="" selected disabled>-- Select Country --</option>
                                        <?php while ($row1 = $result1->fetch_assoc()) {
                                            $disabled = in_array($row1["name"], $savedCountryNames) ? 'disabled' : '';
                                            ?>
                                            <option value="<?= $row1["name"] ?>" data-phone="<?= $row1["phone_code"] ?>"
                                                <?= $disabled ?>>
                                                <?= $row1["name"] ?>     <?= $disabled ? '(Added)' : '' ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Currency</label>
                                    <input class="form-control" name="currency" required>
                                </div>
                                <div class="col-lg-3">
                                    <label>Currency Rate</label>
                                    <input class="form-control" type="number" min="0.01" step="0.01" name="rate" required>
                                </div>
                                <div class="col-lg-3">
                                    <label>Phone Code</label>
                                    <input class="form-control" name="phone_code" id="phone_code" readonly required>
                                </div>
                                <div class="col-lg-12">
                                    <br>
                                    <br>
                                    <button class="btn btn-info" name="addCountry">Add New Country</button>
                                </div>
                            </div>
                        </form>

                        <script>
                            $(document).ready(function () {
                                $('#country').on("change", function () {
                                    var phonec = $(this).find(":selected").data("phone");
                                    $('#phone_code').val(phonec); // Optional: fill the input field
                                    // alert(phonec); // Optional: for debugging
                                });
                            });
                        </script>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>