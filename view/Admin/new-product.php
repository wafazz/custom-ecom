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
              <h6>Create New Product</h6>
            </div>

          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">
            <form id="productForm" action="" method="post" enctype="multipart/form-data">
              <!-- Product Basic Info -->
              <div class="form-group">
                <label for="name">Product Name <i class="fa-solid fa-star-of-life required-item"></i></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name"
                  required />
              </div>
              <div class="form-group">
                <label for="slug">Slug <i class="fa-solid fa-star-of-life required-item"></i></label>
                <input type="text" class="form-control" id="slug" name="slug" placeholder="URL friendly slug"
                  required />
              </div>

              <div class="form-group">
                <label for="imageInput" class="form-label">Image Source</label>
                <input type="file" class="form-control" id="imageInput" name="imageInput" accept="image/*">
                <br>

                <div id="previewContainer"></div>

                <script>
                  const input = document.getElementById('imageInput');
                  const preview = document.getElementById('previewContainer');

                  input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    // 👁️ Preview image using FileReader
                    const reader = new FileReader();
                    // reader.onload = function(e) {
                    //     preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px;"><br>`;
                    // };
                    reader.readAsDataURL(file);

                    // 📤 Upload image using AJAX
                    const formData = new FormData();
                    formData.append('image', file);

                    fetch('<?= $domainURL ?>upload.php', {
                        method: 'POST',
                        body: formData
                      })
                      .then(response => response.text())
                      .then(imageUrl => {
                        if (imageUrl !== 'error') {
                          var fullUrl = "<?= $domainURL ?>" + imageUrl;
                          preview.innerHTML += `
                        <p>
                            Url: ${fullUrl}<br>
                            <span class="btn btn-info" onclick="copyToClipboard('${fullUrl}')">📋 Copy URL</span>
                        </p>
                    `;
                        } else {
                          alert('Upload failed');
                        }
                      });
                  });

                  function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                      alert('URL copied to clipboard!');
                    }).catch(() => {
                      alert('Failed to copy URL');
                    });
                  }
                </script>

              </div>
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
                  placeholder="Product description"></textarea>

              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="brand">Brand <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <select id="brand" class="select2 form-control " name="brand"
                      style="width:100% !important; margin-bottom: 1rem !important;;" required>
                      <option value="">Choose Brand</option>
                      <?= $options['brands'] ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="category">Category <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <select id="category" class="select2 form-control " name="category"
                      style="width:100% !important; margin-bottom: 1rem !important;" required>
                      <option value="">Choose Category</option>
                      <?= $options['categories'] ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-3">
                    <label for="weight">Weight (g) <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <input type="number" class="form-control" id="weight" name="weight" min="1" step="1"
                      placeholder="Enter Weight in gram" required />
                  </div>
                  <div class="col-md-3">
                    <label for="length">Length (mm) <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <input type="number" class="form-control" id="length" name="length" min="1" step="1"
                      placeholder="Enter Length in mm" required />
                  </div>
                  <div class="col-md-3">
                    <label for="width">Width (mm) <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <input type="number" class="form-control" id="width" name="width" min="1" step="1"
                      placeholder="Enter Width in mm" required />
                  </div>
                  <div class="col-md-3">
                    <label for="height">Height (mm) <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <input type="number" class="form-control" id="height" name="height" min="1" step="1"
                      placeholder="Enter Height in mm" required />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Product Type</label>
                <label><input type="radio" name="type" value="simple" checked onchange="toggleVariantSection()" /> Simple</label>
                <label><input type="radio" name="type" value="variable" onchange="toggleVariantSection()" /> Variable</label>
              </div>

              <!-- Simple product variant fields -->
              <div id="simpleVariantSection">
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <label for="sku">SKU <i class="fa-solid fa-star-of-life required-item"></i></label>
                      <input type="text" class="form-control" id="sku" name="sku" placeholder="Enter SKU" required />
                    </div>
                    <div class="col-md-6">
                      <label for="maxP">Max Purchase Per Order <i class="fa-solid fa-star-of-life required-item"></i></label>
                      <input type="number" class="form-control" id="maxP" name="maxP" min="1" step="1" placeholder="Enter Max Purchase Per Order" required />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Variable product variant fields -->
              <div id="variableVariantSection" style="display:none;">
                <div class="form-group">
                  <label>Variants</label>
                  <div id="variantRows">
                    <div class="row mb-2 variant-row">
                      <div class="col-md-4">
                        <input type="text" class="form-control" name="variants[0][name]" placeholder="Variant Name (e.g. Small, Red XL)" />
                      </div>
                      <div class="col-md-3">
                        <input type="text" class="form-control" name="variants[0][sku]" placeholder="SKU" />
                      </div>
                      <div class="col-md-3">
                        <input type="number" class="form-control" name="variants[0][maxP]" placeholder="Max Purchase" min="1" step="1" />
                      </div>
                      <div class="col-md-2">
                        <span class="btn btn-info" onclick="addVariantRow()"><i class="fa-solid fa-plus"></i></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="capPrice">Capital Price (RM)<i
                        class="fa-solid fa-star-of-life required-item"></i></label>
                    <input type="number" class="form-control" id="capPrice" name="capPrice"
                      placeholder="Enter Capital Price" min="0.01" step="0.01" required />
                  </div>
                </div>
              </div>

              <script>
                var variantIndex = 1;
                function toggleVariantSection() {
                  var type = document.querySelector('input[name="type"]:checked').value;
                  var simpleSection = document.getElementById('simpleVariantSection');
                  var variableSection = document.getElementById('variableVariantSection');
                  if (type === 'variable') {
                    simpleSection.style.display = 'none';
                    variableSection.style.display = 'block';
                    toggleRequired(false);
                  } else {
                    simpleSection.style.display = 'block';
                    variableSection.style.display = 'none';
                    toggleRequired(true);
                  }
                }
                function toggleRequired(isSimple) {
                  var skuField = document.getElementById('sku');
                  var maxPField = document.getElementById('maxP');
                  if (skuField) skuField.required = isSimple;
                  if (maxPField) maxPField.required = isSimple;
                }
                function addVariantRow() {
                  var container = document.getElementById('variantRows');
                  var row = document.createElement('div');
                  row.className = 'row mb-2 variant-row';
                  row.innerHTML = '<div class="col-md-4"><input type="text" class="form-control" name="variants[' + variantIndex + '][name]" placeholder="Variant Name (e.g. Small, Red XL)" /></div>' +
                    '<div class="col-md-3"><input type="text" class="form-control" name="variants[' + variantIndex + '][sku]" placeholder="SKU" /></div>' +
                    '<div class="col-md-3"><input type="number" class="form-control" name="variants[' + variantIndex + '][maxP]" placeholder="Max Purchase" min="1" step="1" /></div>' +
                    '<div class="col-md-2"><span class="btn btn-danger" onclick="removeVariantRow(this)"><i class="fa-solid fa-minus"></i></span></div>';
                  container.appendChild(row);
                  variantIndex++;
                }
                function removeVariantRow(btn) {
                  btn.closest('.variant-row').remove();
                }
              </script>

              <div class="form-group">
                <div class="row mb-2 file-input-row" style="padding-left: 7px;
    padding-right: 7px;">
                  <?php while ($row = $country->fetch_array(MYSQLI_ASSOC)) { ?>
                    <div class="col-md-3" style="padding: 5px 5px;
    border-radius: 5px;">
                      <div style="border: 1px solid #ddd;
    padding: 10px 15px;
    border-radius: 5px;display:block">
                        <label style="display:block;">Price
                          <?= htmlspecialchars($row["name"]) ?> <i class="fa-solid fa-star-of-life required-item"></i>
                        </label>
                        <label for="mp_<?= $row["id"]; ?>">Market Price (<?= htmlspecialchars($row["sign"]) ?>)</label>
                        <input type="number" class="form-control" id="mp_<?= $row["id"]; ?>" name="mp[<?= $row["id"]; ?>]"
                          placeholder="Market Price <?= htmlspecialchars($row["name"]) ?>" min="0.01" step="0.01"
                          required />
                        <label for="sp_<?= $row["id"]; ?>">Sale Price (<?= htmlspecialchars($row["sign"]) ?>)</label>
                        <input type="number" class="form-control" id="sp_<?= $row["id"]; ?>" name="sp[<?= $row["id"]; ?>]"
                          placeholder="Sale Price <?= htmlspecialchars($row["name"]) ?>" min="0.01" step="0.01"
                          required />
                      </div>

                    </div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group" id="fileInputs">

                <div class="row mb-2 file-input-row">
                  <div class="col-12">
                    <label>Image/s <i class="fa-solid fa-star-of-life required-item"></i></label>
                    <input type="file" class="form-control" name="files[]" accept="image/*"
                      style="float: left;width:calc(100% - 48px);display:inline-block !important;" required>
                    <span class="btn btn-info" onclick="addFileInput()"
                      style="float: left;width:48px;display:inline-block !important;"><i class="fa-solid fa-plus"
                        style="margin-left: -6px;"></i></span>

                  </div>
                </div>
              </div>

              <script>
                function addFileInput() {
                  const container = document.getElementById("fileInputs");
                  const currentInputs = container.querySelectorAll(".file-input-row").length;

                  if (currentInputs >= 5) {
                    alert("Maximum of 5 files allowed.");
                    return;
                  }

                  const row = document.createElement("div");
                  row.className = "row mb-2 file-input-row";
                  row.innerHTML = `
                      <div class="col-12">
                        <input type="file" class="form-control" name="files[]" accept="image/*" style="float: left;width:calc(100% - 48px);display:inline-block !important;">
                        <span class="btn btn-danger" onclick="removeFileInput(this)" style="float: left;width:48px;display:inline-block !important;"><i class="fa-solid fa-minus" style="margin-left: -6px;"></i></span>
                        
                      </div>
                    `;
                  container.appendChild(row);
                }

                function removeFileInput(icon) {
                  const row = icon.closest(".file-input-row");
                  row.remove();
                }
              </script>

              <!-- Submit Button -->
              <div style="margin-top:20px;">
                <button type="submit">Save Product</button>
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