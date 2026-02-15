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
                        Support Ticket Details
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
                <?php
                if ($verifyTicket->num_rows > 0) {
                    // Ticket found, display details
                    $ticketDetails = $verifyTicket->fetch_assoc();
                    $ticket_nos = $ticketDetails['ticket_no'];
                    $priority = $ticketDetails['priority'];
                    $status = $ticketDetails['status'];
                    switch ($priority) {
                        case 'urgent':
                            $btnClass = 'text-danger';
                            break;
                        case 'high':
                            $btnClass = 'text-warning';
                            break;
                        case 'medium':
                            $btnClass = 'text-primary';
                            break;
                        case 'low':
                            $btnClass = 'text-success';
                            break;
                        default:
                            $btnClass = 'text-secondary'; // fallback
                    }

                    switch ($status) {
                        case 'closed':
                            $btnClassS = 'text-danger';
                            break;
                        case 'reply':
                            $btnClassS = 'text-success';
                            break;
                        case 'new':
                            $btnClassS = 'text-success';
                            break;
                        default:
                            $btnClassS = 'text-success'; // fallback
                    }

                    $priorityText = ucfirst($priority); // Capitalize first letter
                    $statusText = ucfirst($status); // Capitalize first letter
                ?>
                    <ul style="list-style: none;">
                        <li>Ticket Number: <b><?= $ticket_no ?></b></li>
                        <li>Ticket Priority: <span class="<?= $btnClass ?>" style="font-weight:bold;">
                                <?= $priorityText ?>
                            </span></li>
                        <li>Ticket Status: <span class="<?= $btnClassS ?>" style="font-weight:bold;">
                                <?= $statusText ?>
                            </span> <button class="btn btn-danger" id="closeTicketBtn">Close this Ticket</button></li>
                        <li>Title:<br><b><?= htmlspecialchars($ticketDetails['title']) ?></b></li>
                    </ul>
                    <script>
                        document.getElementById("closeTicketBtn").addEventListener("click", function() {

                            Swal.fire({
                                title: "Are you sure?",
                                text: "Do you really want to close this ticket?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes, Close",
                                cancelButtonText: "Cancel",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "<?= $domainURL ?>ticket/close-ticket?ticket_id=<?= $ticketDetails['id'] ?>";
                                }
                            });

                        });
                    </script>
                <?php
                }
                ?>

            </div>

            <div class="col-lg-12" style="margin-top: 30px;">
                <div style="max-height:800px !important; overflow-x: hidden !important;; margin-bottom:20px;">
                    <?php

                    if ($verifyTicket->num_rows > 0) {


                    ?>
                        <div class="row" style="margin-bottom:20px;">
                            <div class="col-lg-12">
                                <div class="col-12">
                                    <div style="display:block;right:0;margin-left: auto;max-width:800px;width:calc(90% - 20px);padding:10px;border: 1px solid #ccc; border-radius: 10px 0px 10px 10px;background: ghostwhite;">
                                        <b><i>You</i></b>
                                        <p><?= nl2br(htmlspecialchars($ticketDetails['description'])) ?></p>
                                        <span style="font-size:12px;color:#888;"><i><?= date('d M Y, h:i A', strtotime($ticketDetails['created_at'])) ?></i></span>

                                        <?php
                                        $attachments = mainTicketAttachments($ticketDetails['id']);
                                        if ($attachments->num_rows > 0) {
                                        ?>
                                            <div style="display:block;">
                                                <span style="font-weight:bold;font-size:11px;display:block;width:100%;">Attachments</span>

                                                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;">
                                                    <?php
                                                    while ($attach = $attachments->fetch_assoc()) {

                                                        $fileUrl = $domainURL . $attach["file_path"]; // adjust path
                                                        $fileType = $attach["file_type"];

                                                        if ($fileType == 'video/mp4' || $fileType == 'video/avi' || $fileType == 'video/mov') {
                                                            $icon = $domainURL . "assets/images/video-icon.png";
                                                        } else if ($fileType == 'application/pdf') {
                                                            $icon = $domainURL . "assets/images/pdf-icon.png";
                                                        } else {
                                                            $icon = $domainURL . "assets/images/image-icon.png";
                                                        }
                                                    ?>

                                                        <img
                                                            src="<?= $icon ?>"
                                                            style="width:100px;height:100px;cursor:pointer;object-fit:contain;"
                                                            onclick="openAttachmentPopup('<?= $fileUrl ?>', '<?= $fileType ?>')" />

                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php
                                        }


                                        ?>



                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php
                    }

                    $replies = replyTicket($ticketDetails['id']);
                    if ($replies->num_rows <= 0) {
                    ?>
                        <div class="row" style="margin-bottom:20px;">
                            <div class="col-lg-12">
                                <div class="col-12">
                                    <div style="display:block;left:0;margin-right: auto;max-width:800px;width:calc(90% - 20px);padding:10px;border: 1px solid #ccc; border-radius: 0px 10px 10px 10px;background: #f1f1f1;">
                                        <b><i>Support Team</i></b>
                                        <p>No replies yet.</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php
                    } else {
                        while ($theReply = $replies->fetch_assoc()) {

                            if ($theReply['user_type'] == 'customer') {
                        ?>
                                <div class="row" style="margin-bottom:20px;">
                                    <div class="col-lg-12">
                                        <div class="col-12">
                                            <div style="display:block;right:0;margin-left: auto;max-width:800px;width:calc(90% - 20px);padding:10px;border: 1px solid #ccc; border-radius: 10px 0px 10px 10px;background: ghostwhite;">
                                                <b><i>You</i></b>
                                                <p><?= nl2br(htmlspecialchars($theReply['message'])) ?></p>
                                                <span style="font-size:12px;color:#888;"><i><?= date('d M Y, h:i A', strtotime($theReply['created_at'])) ?></i></span>

                                                <?php
                                                $rattachments = replyTicketAttachments($theReply['id']);
                                                if ($rattachments->num_rows > 0) {
                                                ?>
                                                    <div style="display:block;">
                                                        <span style="font-weight:bold;font-size:11px;display:block;width:100%;">Attachments</span>

                                                        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;">
                                                            <?php
                                                            while ($rattach = $rattachments->fetch_assoc()) {

                                                                $rfileUrl = $domainURL . $rattach["file_path"]; // adjust path
                                                                $rfileType = $rattach["file_type"];

                                                                if ($rfileType == 'video/mp4' || $rfileType == 'video/avi' || $rfileType == 'video/mov') {
                                                                    $ricon = $domainURL . "assets/images/video-icon.png";
                                                                } else if ($rfileType == 'application/pdf') {
                                                                    $ricon = $domainURL . "assets/images/pdf-icon.png";
                                                                } else {
                                                                    $ricon = $domainURL . "assets/images/image-icon.png";
                                                                }
                                                            ?>

                                                                <img
                                                                    src="<?= $ricon ?>"
                                                                    style="width:100px;height:100px;cursor:pointer;object-fit:contain;"
                                                                    onclick="openAttachmentPopup('<?= $rfileUrl ?>', '<?= $rfileType ?>')" />

                                                            <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php
                                                }


                                                ?>



                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php
                            } else if ($theReply['user_type'] == 'staff') {
                            ?>
                                <div class="row" style="margin-bottom:20px;">
                                    <div class="col-lg-12">
                                        <div class="col-12">
                                            <div style="display:block;left:0;margin-right: auto;max-width:800px;width:calc(90% - 20px);padding:10px;border: 1px solid #ccc; border-radius: 0px 10px 10px 10px;background: #f1f1f1;">
                                                <b><i>Staff/HQ</i></b>
                                                <p><?= nl2br(htmlspecialchars($theReply['message'])) ?></p>
                                                <span style="font-size:12px;color:#888;"><i><?= date('d M Y, h:i A', strtotime($theReply['created_at'])) ?></i></span>

                                                <?php
                                                $rattachments = replyTicketAttachments($theReply['id']);
                                                if ($rattachments->num_rows > 0) {
                                                ?>
                                                    <div style="display:block;">
                                                        <span style="font-weight:bold;font-size:11px;display:block;width:100%;">Attachments</span>

                                                        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;">
                                                            <?php
                                                            while ($rattach = $rattachments->fetch_assoc()) {

                                                                $rfileUrl = $domainURL . $rattach["file_path"]; // adjust path
                                                                $rfileType = $rattach["file_type"];

                                                                if ($rfileType == 'video/mp4' || $rfileType == 'video/avi' || $rfileType == 'video/mov') {
                                                                    $ricon = $domainURL . "assets/images/video-icon.png";
                                                                } else if ($rfileType == 'application/pdf') {
                                                                    $ricon = $domainURL . "assets/images/pdf-icon.png";
                                                                } else {
                                                                    $ricon = $domainURL . "assets/images/image-icon.png";
                                                                }
                                                            ?>

                                                                <img
                                                                    src="<?= $ricon ?>"
                                                                    style="width:100px;height:100px;cursor:pointer;object-fit:contain;"
                                                                    onclick="openAttachmentPopup('<?= $rfileUrl ?>', '<?= $rfileType ?>')" />

                                                            <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php
                                                }


                                                ?>



                                            </div>
                                        </div>

                                    </div>
                                </div>
                    <?php
                            }
                        }
                    }

                    ?>

                </div>

                <?php if (!empty($successTicket)): ?>
                    <div class="col-lg-12" style="margin-top:20px;">
                        <div class="alert alert-success" role="alert">
                            Your ticket has been created successfully. You can view your ticket <a href="<?= $ticketURL ?>">here</a>.
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row" style="">
                    <hr style="    display: block;
    width: 100%;">
                    <form action="" method="POST" enctype="multipart/form-data" style="max-width:100%;margin:auto;font-family:Arial;">

                        <h6 style="margin-bottom:20px;">Submit Reply</h6>



                        <label>Message</label>
                        <textarea name="description" rows="5" required
                            style="width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;"></textarea>



                        <label>Attachment (Image or Video)</label>
                        <input type="hidden" name="ticket_id" value="<?= $ticket_no ?>">
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

        </div>

        <?php
        if ($verifyTicket->num_rows < 1) {
            // Ticket found, display details
            $ticketDetails = $verifyTicket->fetch_assoc();
        ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ticket not found or has been closed.',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Replace with your target URL
                        window.location.href = '<?= $domainURL ?>customer/support-ticket';
                    }
                });
            </script>
        <?php
        }
        ?>


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