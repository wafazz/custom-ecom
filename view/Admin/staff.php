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
                            <h6>Staff List</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">
                        <button id="openRegister" class="btn btn-info" style="margin-bottom:15px;">Register New</button>
                        <form id="registerForm" action="" method="post" novalidate class="registerForm" style="display:none;padding: 10px;
    border: 1px solid red;
    border-radius: 5px;margin-bottom:15px;">
                            <div class="mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                <select class="form-select" id="designation" name="designation" required>
                                    <option value="" disabled selected>Select designation</option>
                                    <option value="2">Account</option>
                                    <option value="3">Staff Admin</option>
                                    <option value="4">Staff Sales</option>
                                    <option value="5">Staff Logistic</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="passwordHelp" class="form-text">Min 8 chars, 1 uppercase, 1 special character</div>
                                <div class="error" id="passwordError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                <div class="error" id="confirmError"></div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <script>
                            $(document).ready(function() {
                                $("#openRegister").click(function() {
                                    $(".registerForm").toggle();
                                })
                            });

                            document.getElementById("registerForm").addEventListener("submit", function(e) {
                                e.preventDefault();

                                const password = document.getElementById("password").value;
                                const confirmPassword = document.getElementById("confirmPassword").value;
                                const email = document.getElementById("email").value;
                                const passwordError = document.getElementById("passwordError");
                                const confirmError = document.getElementById("confirmError");
                                const emailError = document.getElementById("emailError");

                                passwordError.innerText = "";
                                confirmError.innerText = "";
                                if (emailError) emailError.remove();

                                const specialChar = /[!@#$%^&*(),.?":{}|<>]/;
                                const upperCase = /[A-Z]/;

                                let valid = true;

                                if (password.length < 8) {
                                    passwordError.innerText = "Password must be at least 8 characters.";

                                    passwordError.className = "error text-danger";
                                    valid = false;
                                } else if (!specialChar.test(password)) {
                                    passwordError.innerText = "Password must include at least one special character.";

                                    passwordError.className = "error text-danger";
                                    valid = false;
                                } else if (!upperCase.test(password)) {
                                    passwordError.innerText = "Password must include at least one uppercase letter.";

                                    passwordError.className = "error text-danger";
                                    valid = false;
                                }

                                if (password !== confirmPassword) {
                                    confirmError.innerText = "Passwords do not match.";

                                    confirmError.className = "error text-danger";
                                    valid = false;
                                }

                                if (!valid) return;

                                // Check if email exists via AJAX
                                fetch("<?= $domainURL ?>check_email.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded",
                                        },
                                        body: "email=" + encodeURIComponent(email)
                                    })
                                    .then(response => response.text())
                                    .then(data => {
                                        if (data === "exists") {
                                            const emailField = document.getElementById("email");
                                            const error = document.createElement("div");
                                            error.className = "error text-danger";
                                            error.id = "emailError";
                                            error.innerText = "Email already registered.";
                                            emailField.parentNode.appendChild(error);
                                        } else {
                                            // Submit form (optional: convert to AJAX submission or traditional POST)
                                            document.getElementById("registerForm").submit();
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Error checking email:", err);
                                    });
                            });
                        </script>



                        <table id="categoryTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Regiter At</th>
                                    <th>Updated At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td style="text-align:center;">
                                            <?= htmlspecialchars($row['id']) ?>

                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['f_name']) ?> <?= htmlspecialchars($row['l_name']) ?>
                                        </td>
                                        <td>
                                            <?php if ($row['role'] == "1"): ?>
                                                <p>HQ/Owner</p>
                                            <?php elseif ($row['role'] == "2"): ?>
                                                <p>Account</p>
                                            <?php elseif ($row['role'] == "3"): ?>
                                                <p>Staff Admin</p>
                                            <?php elseif ($row['role'] == "4"): ?>
                                                <p>Staff Marketing</p>
                                            <?php elseif ($row['role'] == "5"): ?>
                                                <p>Staff Logistic</p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= dateFromat1($row['created_at']) ?>
                                        </td>
                                        <td>
                                            <?= dateFromat1($row['updated_at']) ?>
                                        </td>
                                        <td>
                                            <?php if (is_null($row['deleted_at'])): ?>
                                                <?php if ($row['status'] == 1): ?>
                                                    <span class="btn btn-success">Active</span>
                                                <?php elseif ($row['status'] == 2): ?>
                                                    <span class="btn btn-warning">Banned</span>
                                                <?php elseif ($row['status'] == 0): ?>
                                                    <span class="btn btn-info">In-Active</span>

                                                <?php endif; ?>

                                            <?php else: ?>
                                                <span class="btn btn-danger">Deleted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (is_null($row['deleted_at'])): ?>
                                                <a href="<?= $this->domainURL ?>edit-user/<?= $row['id'] ?>" data-bs-toggle="tooltip" title="Edit/Update User">
                                                    <span class="btn btn-info"><i class="fa-solid fa-pen-to-square"></i></span>
                                                </a>

                                                <?php if ($row['status'] == 1): ?>
                                                    <a class="bann-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="Bann User">
                                                        <span class="btn btn-warning"><i class="fa-solid fa-eye-slash"></i></span>
                                                    </a>
                                                <?php elseif ($row['status'] == 2): ?>
                                                    <a class="unbann-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="Unbann User">
                                                        <span class="btn btn-warning"><i class="fa-solid fa-eye"></i></span>
                                                    </a>
                                                <?php endif; ?>

                                                <a class="delete-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="Delete User">
                                                    <span class="btn btn-danger"><i class="fa-solid fa-trash"></i></span>
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-danger"><i class="fa-solid fa-user-xmark"></i> Deleted User<br><?= $row['deleted_at'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">no data.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            });
                            $(document).ready(function() {
                                $('#categoryTable').DataTable();

                                $('.bann-btn').click(function() {
                                    var userid = $(this).data("id");
                                    var redirectURL = "<?= $this->domainURL ?>banned-user/" + userid;

                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: 'Proceed to bann user profile (ID: ' + userid + ')?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: 'Confirm',
                                        cancelButtonText: 'Cancel',
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = redirectURL;
                                        }
                                    });
                                });

                                $('.unbann-btn').click(function() {
                                    var userid = $(this).data("id");
                                    var redirectURL = "<?= $this->domainURL ?>unbanned-user/" + userid;

                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: 'Proceed to unbann user profile (ID: ' + userid + ')?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: 'Confirm',
                                        cancelButtonText: 'Cancel',
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = redirectURL;
                                        }
                                    });
                                });
                                $('.delete-btn').click(function() {
                                    var userid = $(this).data("id");
                                    var redirectURL = "<?= $this->domainURL ?>delete-user/" + userid;

                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: 'Proceed to delete user profile (ID: ' + userid + ')? Once confirm cannot roll back.',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, confirm delete',
                                        cancelButtonText: 'Cancel',
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = redirectURL;
                                        }
                                    });
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