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
                            <a href="<?= $domainURL ?>announcement-blog" class="btn btn-outline-primary back-btn">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <h6><?= $pageName ?></h6>
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
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= $row["title"] ?>" required>

                            </div>

                            <div class="mb-3">
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
                                    placeholder="Annoucement/Blog post description"><?= $row["contents"] ?></textarea>
                            </div>



                            <button type="submit" class="btn btn-primary w-100">Submit & Save</button>
                        </form>

                        <script>
                            $(document).ready(function() {
                                $("#openRegister").click(function() {
                                    $(".registerForm").toggle();
                                })
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