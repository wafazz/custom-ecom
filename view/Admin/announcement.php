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
                            <h6><?= $pageName ?></h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;">

                        <button id="openRegister" class="btn btn-info" style="margin-bottom:15px;">Add New</button>
                        <form id="registerForm" action="" method="post" novalidate class="registerForm" style="display:none;padding: 10px;
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

                                        fetch('upload.php', {
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
                                <input type="text" class="form-control" id="title" name="title" required>

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
                                    placeholder="Annoucement/Blog post description"></textarea>
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

                        <style>
                            /* Default: make table scrollable on small screens */
                            .table-responsive {
                                overflow-x: auto;
                            }

                            #stockTable {
                                min-width: 700px;
                                /* Minimum width before horizontal scroll appears */
                                width: 100%;
                                /* Always take full width */
                                border-collapse: collapse;
                            }

                            #stockTable thead tr {
                                background-color: #f6f6f6;
                            }

                            #stockTable th {
                                font-weight: 100;
                                font-size: 14px;
                                ;
                                --font: "Roboto", "Droid Sans", Arial, sans-serif;
                            }

                            #stockTable tbody tr.header-list {
                                background-color: #f6f6f6;
                            }

                            #stockTable tbody tr.header-list td,
                            #stockTable tbody tr.details td {
                                padding: 10px 15px;
                                font-weight: 100;
                                font-size: 14px;
                                ;
                                --font: "Roboto", "Droid Sans", Arial, sans-serif;
                                vertical-align: top;
                            }

                            #stockTable tbody tr.details {
                                border: 1px solid #f6f6f6;
                            }

                            .wrap-img {
                                float: left;
                                margin-right: 15px;
                                width: 150px;
                                height: auto;
                            }

                            .text-wrap-image p {
                                font-weight: 100;
                                font-size: 14px;
                                ;
                                --font: "Roboto", "Droid Sans", Arial, sans-serif;
                                color: #000000;

                            }

                            @media (min-width: 768px) {
                                .table-responsive {
                                    overflow-x: visible;
                                }

                                #stockTable {
                                    width: 100%;
                                    /* Take full width on desktop */
                                }
                            }
                        </style>

                        <div class="table-responsive">
                            <table id='stockTable' cellpadding='8' cellspacing='0'>

                                <tbody>
                                    <?php
                                    if (empty($listNews)) {
                                    ?>
                                        <tr class="seperator">
                                            <td colspan="6"></td>
                                        </tr>
                                        <tr class="header-list">
                                            <td colspan="6" style="text-align:center;">
                                                <img src="<?= $domainURL ?>assets/images/no-data.png" style="display: block;
                                                    width: 60px;
                                                    margin-left: auto;
                                                    margin-right: auto;">
                                                no data
                                            </td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($listNews as $row) {
                                            $session_id = $row["session_id"];
                                        ?>
                                            <tr class="seperator">
                                                <td colspan="6"></td>
                                            </tr>
                                            <tr class="header-list">
                                                <td colspan="3"><i class="fa-solid fa-right-long"></i>
                                                    <?= $row["title"] ?><br>
                                                    <small><i>by: <strong><?= $row["post_by"] ?></strong> on <b><?= dateFromat1($row['created_at']) ?></b></i></small>
                                                </td>
                                                <td colspan="2" style="text-align:right;">Last update on <b><?= dateFromat1($row['updated_at']) ?></b></td>
                                                <td colspan="1" style="text-align:center;"><i class="fa-solid fa-pen-to-square text-info" style="font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    margin-right: 10px;" onClick="window.location.href = '<?= $domainURL ?>update-post/<?= $row['id'] ?>'"></i> <i class="fa-solid fa-trash text-danger delete-btn" style="font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    margin-right: 10px;" data-url="<?= $domainURL ?>delete-post/<?= $row['id'] ?>"></i></td>
                                            </tr>
                                            <tr class="details">
                                                <td colspan="6"><?= $row["contents"] ?></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>


                                </tbody>
                            </table>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    document.querySelectorAll(".delete-btn").forEach(function(btn) {
                                        btn.addEventListener("click", function() {
                                            const url = this.getAttribute("data-url");

                                            Swal.fire({
                                                title: "Are you sure?",
                                                text: "This action cannot be undone.",
                                                icon: "warning",
                                                showCancelButton: true,
                                                confirmButtonColor: "#d33",
                                                cancelButtonColor: "#3085d6",
                                                confirmButtonText: "Yes, delete it!",
                                                cancelButtonText: "Cancel"
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = url;
                                                }
                                            });
                                        });
                                    });
                                });
                                // JavaScript for "Check All"
                                document.getElementById('checkAll').addEventListener('change', function() {
                                    const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
                                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                                });
                            </script>
                            <iframe name="hiddenIframe" style="display:none;"></iframe>
                            <script>
                                document.getElementById('printForm').addEventListener('submit', function() {
                                    // Wait a few seconds for download, then redirect
                                    setTimeout(function() {
                                        window.location.href = "<?= $domainURL ?>process-order"; // Change this to your target page
                                    }, 3000); // 3 seconds
                                });
                            </script>
                            <div class="bg-modal">

                                <div class="modal-details">
                                    <i style="position: absolute;
    top: 7px;
    right: 10px;
    font-size: 30px !important;
    color:  red !important;
    cursor: pointer;" class="fa-solid fa-square-xmark end-bg-modal"></i>

                                    <i style="position: absolute;
    top: 7px;
    right: 50px;
    font-size: 30px !important;
    color:  #8b8be9 !important;
    cursor: pointer;" class="fa-solid fa-pen-to-square edit-order-data"></i><i style="position: absolute;
    top: 7px;
    right: 50px;
    display: none;
    font-size: 30px !important;
    color:  #8b8be9 !important;
    cursor: pointer;" class="fa-solid fa-left-long close-order-data"></i>
                                    <input type="hidden" id="orderNoID" value="">
                                    <div id="details-buyer"></div>

                                </div>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    $(".end-bg-modal").click(function() {
                                        $(".bg-modal").hide();
                                        $('body').css('overflow-y', 'auto');
                                        $("#details-buyer").text("");
                                    })
                                    $(".open-details").click(function() {
                                        var orderID = $(this).data("id");

                                        $(".bg-modal").show();
                                        $('body').css('overflow-y', 'hidden');
                                        $("#orderNoID").val(orderID);
                                        $("#details-buyer").load("<?= $domainURL ?>details-buyer?order_id=" + orderID);
                                    })
                                    $(".edit-order-data").click(function() {
                                        var orderIDs = $("#orderNoID").val();

                                        $(this).hide();
                                        $(".close-order-data").show();

                                        $("#details-buyer").load("<?= $domainURL ?>update-buyer?order_id=" + orderIDs);
                                    })
                                    $(".close-order-data").click(function() {
                                        var orderIDs = $("#orderNoID").val();

                                        $(this).hide();
                                        $(".edit-order-data").show();

                                        $("#details-buyer").load("<?= $domainURL ?>details-buyer?order_id=" + orderIDs);
                                    })
                                });
                            </script>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>