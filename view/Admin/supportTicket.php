<?php
include "01-header.php";
include "01-menu.php";
?>

<!-- End Navbar -->
<div class="container-fluid py-4">
    <div class="row">

    </div>
    <div class="row px-3 pb-3">

        <style>
            .lt-ticket {
                height: 80vh;
            }

            @media (max-width: 767px) {
                .lt-ticket {
                    height: 20vh !important;
                }

                .ticket-item.active {
                    background-color: #0d6efd;
                    color: white;
                }
            }
        </style>

        <!-- LEFT: OPEN TICKET LIST -->
        <div class="col-lg-4 col-md-5 col-12 mb-3">
            <div class="card lt-ticket">
                <div class="card-header pb-2">
                    <h6 class="mb-0">Open Tickets</h6>
                </div>
                <div class="card-body p-0" style="overflow-y: auto; max-height: 74vh;">

                    <?php
                    $tickets = $result ?? [];
                    ?>

                    <!-- Desktop list: visible on md+ -->
                    <div class="d-none d-md-block">
                        <?php if (empty($tickets)) : ?>
                            <div class="text-center text-muted py-3">No open tickets.</div>
                        <?php else : ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($tickets as $ticket) : ?>
                                    <li class="list-group-item ticket-item" style="cursor:pointer;"
                                        onclick="loadTicketDetail(<?= htmlspecialchars($ticket['id']) ?>)" data-ticket-id="<?= htmlspecialchars($ticket['id']) ?>">
                                        <div>
                                            <strong>#<?= htmlspecialchars($ticket['ticket_no']) ?></strong>
                                            <br>
                                            <i><?= htmlspecialchars($ticket['title']) ?></i>
                                            <br>
                                            <small>Name: <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong></small>
                                            <br>
                                            <small>Email: <strong><?= htmlspecialchars($ticket['customer_email']) ?></strong></small>
                                            <?php if (!empty($ticket['order_id'])) : ?>
                                                <br>
                                                <small>Order: <strong><?= htmlspecialchars($ticket['order_id']) ?></strong></small>
                                            <?php else : ?>
                                                <br>
                                                <small>Order: <strong>N/A</strong></small>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">Created on: <?= htmlspecialchars($ticket['created_at']) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile dropdown: visible on sm screens -->
                    <div class="d-block d-md-none mb-3">
                        <?php if (empty($tickets)) : ?>
                            <div class="text-center text-muted py-2">No open tickets.</div>
                        <?php else : ?>
                            <select class="form-select" id="ticketDropdown" onchange="loadTicketDetail(this.value)">
                                <option value="">Select a ticket...</option>
                                <?php foreach ($tickets as $ticket) : ?>
                                    <option value="<?= htmlspecialchars($ticket['id']) ?>">
                                        #<?= htmlspecialchars($ticket['ticket_no']) ?> - <?= htmlspecialchars($ticket['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT: TICKET DETAIL + CHAT + REPLY FORM -->
        <div class="col-lg-8 col-md-7 col-12 mb-3">
            <div class="card" style="height: 80vh;">
                <div class="card-header pb-2">
                    <h6 class="mb-0">Ticket Details</h6>
                    <button class="btn btn-danger closeButton" id="closeTicketBtn" style="display:none;">Close this Ticket</button>
                </div>

                <div class="card-body pt-2" style="overflow-y: auto; max-height: 60vh;" id="ticketConversation">
                    <!-- Default placeholder -->
                    <div class="text-center text-muted mt-5">
                        <i>Select a ticket from Open Tickets list</i>
                    </div>
                </div>

                <div class="card-footer">
                    <form id="replyForm" enctype="multipart/form-data">
                        <div class="mb-2">
                            <textarea class="form-control"
                                id="replyMessage"
                                placeholder="Type your reply…"
                                rows="2"
                                required></textarea>
                        </div>

                        <input type="hidden" id="currentTicketId" value="">

                        <!-- Attachments -->
                        <div class="mb-2">
                            <input type="file" id="replyAttachments" class="form-control" multiple>
                            <small class="text-muted">You can select multiple files.</small>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary w-100">
                            Send Reply
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Fixed: Use consistent variable name (removed the 's')
        let currentTicketId = null;

        // Close ticket button handler
        document.getElementById("closeTicketBtn").addEventListener("click", function() {
            const hiddenFields = document.getElementById("currentTicketId");

            if (!hiddenFields.value) {
                alert("Please select a ticket first.");
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "Do you really want to close this ticket?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Close",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= $domainURL ?>ticket/close-ticket?ticket_id=" + hiddenFields.value;
                }
            });
        });

        function loadTicketDetail(ticketId) {
            const url = "<?= $domainURL ?>support/tickets-details?id=" + ticketId;

            // Fixed: Use consistent variable name
            currentTicketId = ticketId;

            // Update hidden field
            const hiddenField = document.getElementById("currentTicketId");
            if (hiddenField) hiddenField.value = ticketId;

            document.querySelector('.closeButton').style.display = 'block';

            // Toggle active class in desktop list
            document.querySelectorAll(".ticket-item").forEach(item => {
                if (item.dataset.ticketId == ticketId) {
                    item.classList.add("active");
                } else {
                    item.classList.remove("active");
                }
            });

            // Set selected option in mobile dropdown
            const dropdown = document.getElementById("ticketDropdown");
            if (dropdown) dropdown.value = ticketId;

            document.getElementById('ticketConversation').innerHTML = `
                <div class="text-center text-muted mt-5">
                    Loading ticket...
                </div>
            `;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div><strong>Ticket #${data.ticket.id}</strong></div>
                        <div><b>${data.ticket.subject}</b></div>
                        <hr>
                    `;

                    data.messages.forEach(msg => {
                        // Build attachment icons
                        let attachHTML = "";
                        if (msg.attachments && msg.attachments.length > 0) {
                            attachHTML += `<div class="d-flex gap-2 mt-2">`;

                            msg.attachments.forEach(a => {
                                let icon = "";
                                const ext = a.file_type.toLowerCase();

                                if (["video/mp4", "video/mov", "video/avi"].includes(ext)) {
                                    icon = "<?= $domainURL ?>assets/images/video-icon.png";
                                } else if (["application/pdf"].includes(ext)) {
                                    icon = "<?= $domainURL ?>assets/images/pdf-icon.png";
                                } else if (["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp"].includes(ext)) {
                                    icon = "<?= $domainURL ?>assets/images/image-icon.png";
                                } else {
                                    icon = "<?= $domainURL ?>assets/images/image-icon.png";
                                }

                                attachHTML += `
                                    <a href="../${a.file_path}" target="_blank">
                                        <img src="${icon}" width="40" height="40" style="object-fit:contain;">
                                    </a>
                                `;
                            });

                            attachHTML += `</div>`;
                        }

                        html += `
                            <div class="mb-3 p-2 ${msg.sender === 'staff' ? 'bg-secondary text-white' : 'bg-light'} rounded" style="${msg.sender === 'staff' ? 'border-top-left-radius: 0 !important;display:block;width:90%;margin-right:auto;' : 'border-top-right-radius: 0 !important;display:block;width:90%;margin-left:auto;'}">
                                <strong>${msg.sender}:</strong><br>
                                ${msg.message}
                                <br>
                                <small class="text-white"><i>${msg.created_at}</i></small>
                                ${attachHTML}
                            </div>
                        `;
                    });

                    document.getElementById('ticketConversation').innerHTML = html;

                    // Auto scroll to bottom
                    const conv = document.getElementById('ticketConversation');
                    conv.scrollTop = conv.scrollHeight;
                })
                .catch(err => {
                    document.getElementById('ticketConversation').innerHTML = `
                        <div class="text-danger mt-5 text-center">
                            Failed to load ticket.
                        </div>
                    `;
                    console.error(err);
                });
        }

        function appendNewMessage(reply) {
            let conv = document.getElementById("ticketConversation");

            // Build attachment HTML
            let attachHTML = "";
            if (reply.attachments && reply.attachments.length > 0) {
                attachHTML += `<div class="d-flex gap-2 mt-2">`;

                reply.attachments.forEach(a => {
                    let icon = "";
                    let ext = a.file_type.toLowerCase();

                    if (ext.includes("mp4") || ext.includes("mov") || ext.includes("avi")) {
                        icon = "<?= $domainURL ?>assets/images/video-icon.png";
                    } else if (ext.includes("pdf")) {
                        icon = "<?= $domainURL ?>assets/images/pdf-icon.png";
                    } else if (ext.includes("png") || ext.includes("jpg") ||
                        ext.includes("jpeg") || ext.includes("gif") || ext.includes("webp")) {
                        icon = "<?= $domainURL ?>assets/images/image-icon.png";
                    } else {
                        icon = "<?= $domainURL ?>assets/images/image-icon.png";
                    }

                    attachHTML += `
                        <a href="../${a.file_path}" target="_blank">
                            <img src="${icon}" width="40" height="40" style="object-fit:contain;">
                        </a>
                    `;
                });

                attachHTML += `</div>`;
            }

            // Build message bubble
            let bubble = document.createElement("div");
            bubble.className = `mb-3 p-2 bg-secondary text-white rounded`;
            bubble.style.setProperty("border-top-left-radius", "0px", "important");
            bubble.style.setProperty("display", "block");
            bubble.style.setProperty("width", "90%");
            bubble.style.setProperty("margin-right", "auto");

            bubble.innerHTML = `
                <strong>staff:</strong><br>
                ${reply.message}<br>
                <small class="text-white"><i>${reply.created_at}</i></small>
                ${attachHTML}
            `;

            // Append to chat
            conv.appendChild(bubble);

            // Scroll to bottom
            conv.scrollTop = conv.scrollHeight;

            // Clear form
            document.getElementById("replyMessage").value = "";
            document.getElementById("replyAttachments").value = "";
        }

        // Fixed: Properly handle form submission with event parameter
        function submitReply(event) {
            event.preventDefault();
            
            let message = document.getElementById("replyMessage").value.trim();
            let files = document.getElementById("replyAttachments").files;
            let ticketId = document.getElementById("currentTicketId").value;
            let btn = document.querySelector("#replyForm button[type='submit']");

            // Validation: Check if ticket is selected
            if (!ticketId) {
                alert("Please select a ticket first.");
                return;
            }

            // Validation: Check if message is not empty
            if (!message) {
                alert("Message cannot be empty.");
                return;
            }

            // Disable button to prevent double submission
            btn.disabled = true;
            btn.innerHTML = "Sending...";

            let formData = new FormData();
            formData.append("ticket_id", ticketId);
            formData.append("message", message);

            // Append all files
            for (let i = 0; i < files.length; i++) {
                formData.append("attachments[]", files[i]);
            }

            fetch("<?php echo $domainURL; ?>support/tickets-reply", {
                    method: "POST",
                    body: formData
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        appendNewMessage(data.reply);
                        alert("Reply sent successfully!");
                    } else {
                        alert("Failed to send reply: " + (data.message || "Unknown error"));
                    }
                })
                .catch(err => {
                    console.error("Error sending reply:", err);
                    alert("An error occurred while sending your reply. Please try again.");
                })
                .finally(() => {
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = "Send Reply";
                });
        }

        // Fixed: Attach event listener properly to form
        document.getElementById("replyForm").addEventListener("submit", submitReply);
    </script>

    <?php
    include "01-footer.php";
    ?>