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
              <h6>Update About Us</h6>
            </div>

          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">
            <form id="productForm" action="<?= rtrim($domainURL, '/') . '/setting-about-us' . $id ?>" method="post"
              enctype="multipart/form-data">
              <!-- Product Basic Info -->
              
              <div class="form-group">
                <label for="description">Description <i class="fa-solid fa-star-of-life required-item"></i></label>
                <style>
                  .tox-menubar {
                    display: none !important;
                  }
                </style>
                <script>
                  tinymce.init({
                    selector: 'textarea',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                  });
                </script>
                <textarea id="description" class="form-control" name="description" rows="3"
                  placeholder="About Us details"><?= $row["description"] ?></textarea>

              </div>
              

              <!-- Submit Button -->
              <div style="margin-top:20px;">
                <button type="submit">Update & Save About Us</button>
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