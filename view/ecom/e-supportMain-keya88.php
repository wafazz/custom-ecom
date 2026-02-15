<?php
include "e-header-keya88.php";
include "e-menu-keya88.php";
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?= $domainURL ?>main"><i class="fa fa-home"></i> Home</a>

                    <span>
                        Support Ticket
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row" style="margin-bottom: 30px;">

            <div class="col-lg-12">
                <h4>Track Your Ticket</h4>
            </div>

            <div class="col-lg-10">
                <label>Ticket ID/Number:</label>
                <input type="text" id="t_ticketid" class="form-control" style="margin-bottom:15px;">
            </div>
            <div class="col-lg-2">
                <button class="btn btn-danger" id="trackorder" style="margin-top: 31px;
    width: 100%;">Track</button>
            </div>
            <?php if (!empty($successTicket)): ?>
            <div class="col-lg-12" style="margin-top:20px;">
                <div class="alert alert-success" role="alert">
                    Your ticket has been created successfully. You can view your ticket <a href="<?= $ticketURL ?>">here</a>.
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">

            <div class="col-lg-12">
                <h4>Create New Ticket</h4>
            </div>

            <div class="col-lg-12">
                <form action="" method="POST" enctype="multipart/form-data" style="max-width:100%;margin:auto;font-family:Arial;">

                    <h2 style="margin-bottom:20px;">Submit Support Ticket</h2>

                    <div class="row">
                        <div class="col-md-6" style="margin-bottom:15px;">
                            <label>Full Name</label>
                            <input type="text" name="full_name" required
                                style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;">
                        </div>
                        <div class="col-md-6" style="margin-bottom:15px;">
                            <label>Email</label>
                            <input type="email" name="email" required
                                style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;">
                        </div>
                    </div>

                    <label>Title</label>
                    <input type="text" name="title" required
                        style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;">

                    <label>Description</label>
                    <textarea name="description" rows="5" required
                        style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;"></textarea>

                    <label>Priority</label>
                    <select name="priority" required
                        style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>

                    <label>Order ID (optional)</label>
                    <input type="text" name="order_id"
                        style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;">

                    <label>Attachment (Image or Video)</label>
                    <input type="file" id="attachments" name="attachments[]" multiple
                        accept="image/*,video/*,application/pdf"
                        style="width:100%;padding:10px;margin:8px 0;" onchange="if(this.files[0].size>50*1024*1024){alert('Max 50MB');this.value='';}">

                    <div id="preview" style="margin-top:15px;display:flex;flex-wrap:wrap;gap:10px;"></div>

                    <button type="submit" name="newTicket"
                        style="background:#007bff;color:#fff;padding:12px 20px;border:none;border-radius:5px;cursor:pointer;margin-top:20px;">
                        Submit Ticket
                    </button>

                </form>
                <script>
                    document.getElementById('attachments').addEventListener('change', function(event) {
                        const preview = document.getElementById('preview');
                        preview.innerHTML = "";

                        Array.from(event.target.files).forEach(file => {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                let element;

                                if (file.type.startsWith("image/")) {
                                    element = document.createElement("img");
                                    element.src = e.target.result;
                                    element.style.width = "120px";
                                    element.style.height = "120px";
                                    element.style.objectFit = "cover";
                                    element.style.border = "1px solid #ccc";
                                    element.style.borderRadius = "5px";
                                } else if (file.type.startsWith("video/")) {
                                    element = document.createElement("video");
                                    element.src = e.target.result;
                                    element.controls = true;
                                    element.style.width = "150px";
                                    element.style.height = "120px";
                                    element.style.border = "1px solid #ccc";
                                    element.style.borderRadius = "5px";
                                } else if (file.type === "application/pdf") {
                                    element = document.createElement("div");
                                    element.style.width = "150px";
                                    element.style.padding = "10px";
                                    element.style.border = "1px solid #ccc";
                                    element.style.borderRadius = "5px";
                                    element.style.display = "flex";
                                    element.style.flexDirection = "column";
                                    element.style.alignItems = "center";
                                    element.style.fontSize = "14px";
                                    element.style.background = "#f8f8f8";

                                    const icon = document.createElement("div");
                                    icon.innerHTML = "📄";
                                    icon.style.fontSize = "40px";
                                    icon.style.marginBottom = "5px";

                                    const name = document.createElement("span");
                                    name.innerText = file.name;

                                    element.appendChild(icon);
                                    element.appendChild(name);
                                }

                                preview.appendChild(element);
                            };

                            reader.readAsDataURL(file);
                        });
                    });
                </script>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $("#trackorder").click(function() {
                    var tticketid = $("#t_ticketid").val();

                    window.location.href = "<?= $domainURL ?>customer/tiket-details?id=" + tticketid;

                })
            });
        </script>

    </div>
</section>


<?php
include "e-footer-keya88.php";
?>