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
                            <h6>Staff Details</h6>
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
                                <select class="form-select" id="designation" name="designation" required>

                                    <?php if ($row["role"] == 2): ?>
                                        <option value="2" selected>Account</option>
                                    <?php else: ?>
                                        <option value="2">Account</option>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 3): ?>
                                        <option value="3" selected>Staff Admin</option>

                                    <?php else: ?>
                                        <option value="3">Staff Admin</option>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 4): ?>
                                        <option value="4" selected>Staff Sales</option>

                                    <?php else: ?>
                                        <option value="4">Staff Sales</option>
                                    <?php endif; ?>

                                    <?php if ($row["role"] == 5): ?>
                                        <option value="5" selected>Staff Logistic</option>

                                    <?php else: ?>
                                        <option value="5">Staff Logistic</option>
                                    <?php endif; ?>

                                </select>
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
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $row["email"] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Phone</label>
                                    <input type="email" class="form-control" id="phone" name="phone" value="<?= $row["phone"] ?>" required>
                                </div>
                            </div>



                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </form>



                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Permissions Control</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">
                        <div class="row">

                            <?php

                            while ($rowp = $permission->fetch_array(MYSQLI_ASSOC)) {

                                $userIds = $rowp["allowed_user"];
                                $userArray = explode(',', $userIds);
                                if (in_array('[' . $id . ']', $userArray)) {
                            ?>
                                    <div class="col-md-3">
                                        <div class="card set-permission" data-id="<?= $rowp["id"] ?>" data-name="<?= $rowp["name"] ?>" data-status="1" data-user="<?= $id ?>" style="padding: 10px 15px;
    margin-bottom: 10px;
    background: lawngreen;
    font-weight: bold;
    color: #000;cursor:pointer;">
                                            <?= $rowp["name"] ?> (<?= $id ?>)
                                            <p class="allowed" style="margin: 0px;">Permission Granted</p>
                                        </div>

                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="col-md-3">
                                        <div class="card set-permission" data-id="<?= $rowp["id"] ?>" data-name="<?= $rowp["name"] ?>" data-status="2" data-user="<?= $id ?>" style="padding: 10px 15px;
    margin-bottom: 10px;
    background: indianred;
    font-weight: bold;
    color: #000;cursor:pointer;">
                                            <?= $rowp["name"] ?> (<?= $id ?>)
                                            <p class="allowed" style="margin: 0px;">No Permission</p>
                                        </div>

                                    </div>
                            <?php
                                }
                            }

                            ?>

                        </div>

                        <!-- Include SweetAlert2 -->
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                        <script>
                            $('.set-permission').click(function() {
                                var $card = $(this);
                                var id = $card.data('id');
                                var user = $card.data('user');
                                var name = $card.data('name');
                                var status = $card.attr('data-status'); // Get current status from DOM attribute

                                // Determine action
                                let newStatus = (status == 1 || status == '1') ? '2' : '1';
                                let actionText = newStatus == '2' ? 'revoke permission from' : 'grant permission to';

                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: `You are about to ${actionText} "${name}"`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, do it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Send to server
                                        $.get("<?= $domainURL ?>set-user-permission", {
                                            id: id,
                                            status: status,
                                            user: user
                                        }, function(response) {
                                            // Toggle UI
                                            if (status == 1 || status == '1') {
                                                $card.attr('data-status', '2');
                                                $card.css('background-color', 'indianred');
                                                $card.find('.allowed').text('No Permission');
                                            } else {
                                                $card.attr('data-status', '1');
                                                $card.css('background-color', 'lawngreen');
                                                $card.find('.allowed').text('Permission Granted');
                                            }

                                            // Show success alert
                                            Swal.fire({
                                                title: 'Updated!',
                                                text: 'Permission updated successfully.',
                                                icon: 'success',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                        });
                                    }
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