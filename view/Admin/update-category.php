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
                            <a href="<?= $domainURL ?>category-product" class="btn btn-outline-primary back-btn">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <h6>Category (Update)</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;overflow-x: auto;">

                        <form id="productForm" action="<?= rtrim($domainURL, '/') . '/update-category/' . $id ?>"
                            method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Category ID :</label> <b>
                                    <?= $id ?>
                                </b>

                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">Category Name <i
                                                class="fa-solid fa-star-of-life required-item"></i></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter category name" style="margin-bottom: 20px;"
                                            value="<?= $data["name"] ?>" required />
                                    </div>
                                    <div class="col-md-6">

                                        <?php
                                        if (!empty($data["image"])) {
                                            ?>
                                            <label for="image" style="display:block;">Current Image</label>
                                            <div class="col-md-6 col-6 existing-image" data-index="<?= $index ?>">
                                                <div style="position: relative;">
                                                    <img src="<?= $domainURL ?>assets/images/brand-category/<?= $data["image"] ?>"
                                                    class="img-thumbnail" style="width:100%; height: auto; margin-bottom: 5px; cursor: pointer;"
                                                    onclick="previewImage(this.src)">
                                                    <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($imagePath) ?>">
                                                    
                                                </div>
                                            </div>
                                            
                                            <?php
                                        }
                                        ?>
                                        <label for="image" style="display:block;">Upload New Image <small
                                                style="color:red;">(Leave blank if you don't want to change
                                                image.)</small></label>
                                        <input id="image" type="file" class="form-control image-input" name="files[]"
                                            accept="image/*" style="display:block;margin-bottom: 20px;">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="saveQuery">Save & Update</button>
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