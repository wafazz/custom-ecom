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
                            <h6>Profile</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">
                        <form id="registerForm" action="" method="post" novalidate class="registerForm" style="padding: 10px;
    border: 1px solid red;
    border-radius: 5px;margin-bottom:15px;">
                            <div class="mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                    <?php if ($row["role"] == 1): ?>
                                        <span class="btn btn-info" style="display: block;width: fit-content;">HQ/Owner</span>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 2): ?>
                                        <span class="btn btn-info" style="display: block;width: fit-content;">Account</span>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 3): ?>
                                        <span class="btn btn-info" style="display: block;width: fit-content;">Staff Admin</span>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 4): ?>
                                        <span class="btn btn-info" style="display: block;width: fit-content;">Staff Sales</span>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 5): ?>
                                        <span class="btn btn-info" style="display: block;width: fit-content;">Staff Logistic</span>
                                    <?php endif; ?>

                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname" value="<?= $row["f_name"] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" value="<?= $row["l_name"] ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" readonly disabled id="email" name="email" value="<?= $row["email"] ?>" style="cursor:no-drop;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Phone</label>
                                    <input type="email" class="form-control" id="phone" name="phone" value="<?= $row["phone"] ?>" required>
                                </div>
                            </div>



                            <button type="submit" class="btn btn-primary w-100">Update & Save</button>
                        </form>



                    </div>
                </div>
            </div>

            
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>