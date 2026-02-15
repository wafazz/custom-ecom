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
                            <h6>List Available Country</h6>
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
                        
                        <table id="categoryTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Currency Sign</th>
                                    <th style="max-width: 70px !important;">Rate</th>
                                    <th>Phone Code</th>
                                    <th>Last Update</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($row['id']) ?></td>
                                            <td>
                                                <?= htmlspecialchars($row['name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['sign']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['rate']) ?>
                                            </td>   
                                            <!-- <img src="<?= $domainURL ?>assets/images/brand-category/<?= htmlspecialchars($row['image']) ?>" width="50"></td> -->
                                            <td>
                                                <?= htmlspecialchars($row['phone_code']) ?>
                                            </td>
                                            <td>
                                                <?= dateFromat1($row['updated_at']) ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($row['status'] == "1"){
                                                    ?>
                                                    <span class="btn btn-outline-success">Active</span>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <span class="btn btn-outline-danger">Inactive</span>
                                                    <?php
                                                }
                                                ?>

                                                <button class="btn btn-info" data-id="<?= htmlspecialchars($row['id']) ?>" data-name="<?= htmlspecialchars($row['name']) ?>" data-code="<?= htmlspecialchars($row['sign']) ?>" data-rate="<?= htmlspecialchars($row['rate']) ?>" data-status="<?= htmlspecialchars($row['status']) ?>" onclick="openModal(this)">Update</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">No country found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <script>
                            $(document).ready(function () {
                                $('#categoryTable').DataTable();
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