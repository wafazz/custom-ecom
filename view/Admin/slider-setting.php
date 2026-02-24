<?php
include "01-header.php";
include "01-menu.php";
?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-lg-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6><?= $pageName ?></h6>
                            <p class="text-sm">Manage homepage fullwidth slider images. Drag rows to reorder.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="margin: 0 20px 20px;">

                        <span class="btn btn-info addSlider"><i class="fa-solid fa-plus"></i> Add Slider</span>

                        <form action="" method="post" enctype="multipart/form-data" class="sliderForm" style="display:none; margin-bottom: 20px;">
                            <input type="hidden" name="action" value="upload">
                            <div class="row">
                                <div class="col-lg-12 mb-2">
                                    <h6>Upload New Slider</h6>
                                </div>
                                <div class="col-lg-4 mb-2">
                                    <label class="form-label">Image <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" name="slider_image" accept="image/*" required>
                                    <small><i>Recommended: <b>1920px x 600px</b> (landscape). Max 2MB.</i></small>
                                </div>
                                <div class="col-lg-4 mb-2">
                                    <label class="form-label">Title (optional)</label>
                                    <input class="form-control" type="text" name="title" placeholder="e.g. New Collection">
                                </div>
                                <div class="col-lg-4 mb-2">
                                    <label class="form-label">Link URL (optional)</label>
                                    <input class="form-control" type="text" name="link_url" placeholder="e.g. /promo-item">
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <?php if (empty($sliders)): ?>
                            <p class="text-muted">No sliders yet. Click "Add Slider" to upload one.</p>
                        <?php else: ?>
                            <form id="reorderForm" action="" method="post">
                                <input type="hidden" name="action" value="reorder">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0" id="sliderTable">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:40px;"></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Preview</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Link</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sliderBody">
                                            <?php foreach ($sliders as $index => $sl): ?>
                                                <tr data-id="<?= $sl['id'] ?>">
                                                    <td style="cursor:grab;" class="drag-handle">
                                                        <i class="fa-solid fa-grip-vertical text-secondary"></i>
                                                        <input type="hidden" name="order[]" value="<?= $sl['id'] ?>">
                                                    </td>
                                                    <td>
                                                        <div class="existing-image" style="width:200px;">
                                                            <img src="<?= $domainURL . $sl['image'] ?>" style="width:100%; border-radius:6px; cursor:pointer;" onclick="previewImage(this.src)">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-sm"><?= htmlspecialchars($sl['title'] ?? '-') ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="text-sm"><?= htmlspecialchars($sl['link_url'] ?? '-') ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($sl['status'] == 1): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary mb-0 editSliderBtn"
                                                            data-id="<?= $sl['id'] ?>"
                                                            data-title="<?= htmlspecialchars($sl['title'] ?? '') ?>"
                                                            data-link="<?= htmlspecialchars($sl['link_url'] ?? '') ?>"
                                                            data-image="<?= $domainURL . $sl['image'] ?>">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                        <?php if ($sl['status'] == 1): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning mb-0" onclick="toggleSlider(<?= $sl['id'] ?>, 0)">
                                                                <i class="fa-solid fa-eye-slash"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-outline-success mb-0" onclick="toggleSlider(<?= $sl['id'] ?>, 1)">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger mb-0" onclick="deleteSlider(<?= $sl['id'] ?>)">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-dark btn-sm">
                                        <i class="fa-solid fa-arrows-up-down"></i> Save Order
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Slider Modal -->
    <div class="modal fade" id="editSliderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="slider_id" id="editSliderId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Slider</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <img id="editSliderPreview" src="" style="max-width:100%; border-radius:6px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Replace Image (optional)</label>
                            <input class="form-control" type="file" name="slider_image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input class="form-control" type="text" name="title" id="editSliderTitle">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link URL</label>
                            <input class="form-control" type="text" name="link_url" id="editSliderLink">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden forms for toggle/delete -->
    <form id="toggleForm" action="" method="post" style="display:none;">
        <input type="hidden" name="action" value="toggle">
        <input type="hidden" name="slider_id" id="toggleSliderId">
        <input type="hidden" name="status" id="toggleSliderStatus">
    </form>
    <form id="deleteForm" action="" method="post" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="slider_id" id="deleteSliderId">
    </form>

    <?php include "01-footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle add slider form
    $('.addSlider').on('click', function() {
        let $btn = $(this);
        let $icon = $btn.find('i');
        $('.sliderForm').slideToggle(200);
        if ($btn.hasClass('btn-info')) {
            $btn.removeClass('btn-info').addClass('btn-danger');
            $icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            $btn.removeClass('btn-danger').addClass('btn-info');
            $icon.removeClass('fa-minus').addClass('fa-plus');
        }
    });

    // Edit slider modal
    $(document).on('click', '.editSliderBtn', function() {
        $('#editSliderId').val($(this).data('id'));
        $('#editSliderTitle').val($(this).data('title'));
        $('#editSliderLink').val($(this).data('link'));
        $('#editSliderPreview').attr('src', $(this).data('image'));
        var modal = new bootstrap.Modal(document.getElementById('editSliderModal'));
        modal.show();
    });

    // Sortable drag-and-drop
    var el = document.getElementById('sliderBody');
    if (el) {
        Sortable.create(el, {
            handle: '.drag-handle',
            animation: 150
        });
    }
});

function toggleSlider(id, status) {
    document.getElementById('toggleSliderId').value = id;
    document.getElementById('toggleSliderStatus').value = status;
    document.getElementById('toggleForm').submit();
}

function deleteSlider(id) {
    Swal.fire({
        title: 'Delete this slider?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteSliderId').value = id;
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
