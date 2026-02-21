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
                            <h6>Category (Add New & Update)</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <button class="open-btn" onclick="openModal()">Add New <?= $nameBtn; ?></button>
                    <button class="open-btn" id="btnDeleteSelected" style="background-color:#d33;color:#fff;display:none;margin-left:10px;" onclick="bulkDeleteCategory()">Delete Selected (<span id="selectedCount">0</span>)</button>

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

                            /* EXCEPTION: left-align the "name" column (3rd column now, after checkbox) */
                            #categoryTable td:nth-child(3) {
                                text-align: left;
                                vertical-align: top;
                            }
                        </style>
                        <table id="categoryTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:30px;"><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th style="max-width: 70px !important;">Image</th>
                                    <th>Sort Order</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $row): ?>
                                        <?php $countC = getUsedCategory($row['id']); ?>
                                        <tr>
                                            <td>
                                                <?php if ($countC["count"] < "1"): ?>
                                                    <input type="checkbox" class="row-check" value="<?= $row['id'] ?>">
                                                <?php else: ?>
                                                    <input type="checkbox" disabled title="Has <?= $countC['count'] ?> product(s)">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['id']) ?>
                                                <i class="fa-solid fa-pen-to-square" style="display: block;
    width: 40px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 10px;
    margin-bottom: 10px;color:blue;cursor:pointer;"
                                                    onClick="window.location.href = '<?= $domainURL; ?>update-category/<?= $row['id'] ?>'"></i>

                                                <?php
                                                if ($countC["count"] < "1") {
                                                    ?>
                                                    <i class="fa-solid fa-trash" style="display: block;
        width: 40px;
        margin-left: auto;
        margin-right: auto;
        margin-top: 10px;
        margin-bottom: 10px;color:red;cursor:pointer;" onclick="confirmDelete(this)"></i>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <i class="fa-solid fa-trash" style="display: block;
        width: 40px;
        margin-left: auto;
        margin-right: auto;
        margin-top: 10px;
        margin-bottom: 10px;color:grey;cursor:no-drop;"
                                                        onclick="alert('Cannot delete \'Category: <?= $row['name']; ?>\'. <?= $countC['count']; ?> product was attached to this category.')"></i>
                                                    <?php
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['slug']) ?>
                                            </td>
                                            <td>
                                            <div class="col-md-12 col-12 existing-image" data-index="<?= $index ?>">
                                                <div style="position: relative;">
                                                    <img src="<?= $domainURL ?>assets/images/brand-category/<?= htmlspecialchars($row['image']) ?>"
                                                    class="img-thumbnail" style="width:100%; height: auto; margin-bottom: 5px; cursor: pointer;"
                                                    onclick="previewImage(this.src)">
                                                    <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($imagePath) ?>">
                                                    
                                                </div>
                                            </div>    
                                            <!-- <img src="<?= $domainURL ?>assets/images/brand-category/<?= htmlspecialchars($row['image']) ?>" width="50"></td> -->
                                            <td>
                                                <?= htmlspecialchars($row['sort_order']) ?>
                                            </td>
                                            <td>
                                                <?= dateFromat1($row['created_at']) ?>
                                            </td>
                                            <td>
                                                <?= dateFromat1($row['updated_at']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10">No soft-deleted categories found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <script>
                            $(document).ready(function () {
                                $('#categoryTable').DataTable({
                                    columnDefs: [{ orderable: false, targets: 0 }]
                                });

                                $('#selectAll').on('change', function() {
                                    var checked = this.checked;
                                    $('#categoryTable .row-check').each(function() {
                                        this.checked = checked;
                                    });
                                    updateSelectedCount();
                                });

                                $('#categoryTable').on('change', '.row-check', function() {
                                    var total = $('#categoryTable .row-check').length;
                                    var checked = $('#categoryTable .row-check:checked').length;
                                    $('#selectAll').prop('checked', total === checked && total > 0);
                                    updateSelectedCount();
                                });
                            });

                            function updateSelectedCount() {
                                var count = $('#categoryTable .row-check:checked').length;
                                $('#selectedCount').text(count);
                                $('#btnDeleteSelected').toggle(count > 0);
                            }

                            function bulkDeleteCategory() {
                                var ids = [];
                                $('#categoryTable .row-check:checked').each(function() {
                                    ids.push($(this).val());
                                });
                                if (ids.length === 0) return;

                                Swal.fire({
                                    title: 'Delete ' + ids.length + ' categories?',
                                    text: "This action cannot be undone!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, delete all!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: '<?= $domainURL ?>bulk-delete-category',
                                            method: 'POST',
                                            data: { ids: ids },
                                            success: function(resp) {
                                                var data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                                                Swal.fire('Deleted!', data.message, 'success').then(() => {
                                                    location.reload();
                                                });
                                            },
                                            error: function() {
                                                Swal.fire('Error', 'Failed to delete categories.', 'error');
                                            }
                                        });
                                    }
                                });
                            }
                        </script>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>