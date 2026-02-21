<?php

namespace SupportTicket;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/SupportTicket.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ticketController
{
    private $conn;
    private $ticketModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->ticketModel = new \SupportTicket($this->conn);
    }

    private function checkAccess()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];
        $firstSegments1 = $segmentss[1] ?? '';
        $domainURL = getMainUrl();
        if (roleVerify($firstSegments . "/" . $firstSegments1, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            exit;
        }
    }

    public function mainTickets()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $country = allSaleCountry();

        $pageName = "Support Tickets ";

        $result = $this->ticketModel->getOpenTickets();

        $successTicket = $_SESSION['success_ticket'] ?? null;
        unset($_SESSION['success_ticket']);

        require_once __DIR__ . '/../../view/Admin/supportTicket.php';
    }

    public function replyTickets()
    {
        header("Content-Type: application/json");
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $country = allSaleCountry();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];

        $ticketId = $_GET['id'];

        $ticket = $this->ticketModel->getTicketById($ticketId);

        $ticketAttachments = $this->ticketModel->getTicketAttachments($ticketId);

        $messages = [];

        $messages[] = [
            "sender"      => "customer",
            "message"     => $ticket["description"],
            "created_at"  => $ticket["created_at"],
            "attachments" => $ticketAttachments
        ];

        $replies = $this->ticketModel->getReplies($ticketId);

        foreach ($replies as $reply) {
            $replyId = $reply['id'];
            $replyAttachments = $this->ticketModel->getReplyAttachments($replyId);

            $messages[] = [
                "sender"      => $reply["user_type"],
                "message"     => $reply["message"],
                "created_at"  => $reply["created_at"],
                "attachments" => $replyAttachments
            ];
        }

        echo json_encode([
            "ticket" => [
                "id"          => $ticket["id"],
                "subject"     => $ticket["subject"],
                "status"      => $ticket["status"],
                "priority"    => $ticket["priority"],
                "created_at"  => $ticket["created_at"],
                "customer_name"  => $ticket["customer_name"],
                "customer_email" => $ticket["customer_email"]
            ],
            "messages" => $messages
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function repliesTickets()
    {
        header('Content-Type: application/json');

        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            $country = 1;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Main";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);

        if (!isset($_POST['ticket_id'])) {
            echo json_encode(["success" => false, "message" => "No ticket_id found"]);
            exit;
        }

        $ticket_no = mysqli_real_escape_string($conn, $_POST['ticket_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);

        $ticket = $this->ticketModel->getTicketById($ticket_no);
        if (!$ticket) {
            echo json_encode(["success" => false, "message" => "Ticket not found"]);
            exit;
        }

        $ticket_id = $ticket['id'];

        $reply_id = $this->ticketModel->addReply([
            'ticket_id' => $ticket_id,
            'user_type' => 'staff',
            'user_id' => 0,
            'message' => $message,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        if (!$reply_id) {
            echo json_encode(["success" => false, "message" => "Failed to save reply"]);
            exit;
        }

        $replyAttachments = [];

        if (!empty($_FILES['attachments']['name'][0])) {

            $uploadDir = "assets/tickets/upload/" . date("Y") . "/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['attachments']['name'] as $i => $name) {

                $tmp  = $_FILES['attachments']['tmp_name'][$i];
                $mimeType = $_FILES['attachments']['type'][$i];
                $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                $newName = uniqid() . "." . $ext;
                $savePath = $uploadDir . $newName;

                $videoExt = ["mp4", "mov", "avi", "mkv", "flv", "wmv", "webm"];

                if (in_array($ext, $videoExt)) {
                    $compressed = $uploadDir . "cmp_" . $newName;
                    $cmd = "ffmpeg -i " . escapeshellarg($tmp) . " -vcodec libx264 -crf 28 -preset fast -b:a 128k " . escapeshellarg($compressed);
                    exec($cmd, $o, $r);

                    if ($r === 0 && file_exists($compressed)) {
                        rename($compressed, $savePath);
                    } else {
                        move_uploaded_file($tmp, $savePath);
                    }
                } else {
                    move_uploaded_file($tmp, $savePath);
                }

                $this->ticketModel->addReplyAttachment([
                    'reply_id' => $reply_id,
                    'filename' => $newName,
                    'file_path' => $savePath,
                    'file_type' => $mimeType,
                    'created_at' => date("Y-m-d H:i:s")
                ]);

                $replyAttachments[] = [
                    "filename"  => $newName,
                    "file_path" => $savePath,
                    "file_type" => $mimeType
                ];
            }
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rozeyanahq.ticket@gmail.com';
            $mail->Password   = 'mrae scfh kjfl cwkf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('rozeyanahq.ticket@gmail.com', 'Rozeyana Support');
            $mail->addAddress($ticket["customer_email"]);

            $mail->isHTML(true);
            $mail->Subject = 'Reply from support for ticket #' . $ticket['ticket_no'];
            $mail->Body    = '
            <!doctype html>
            <html lang="en">
            <head>
            <meta charset="utf-8">
            <title>Support Team has replied to your Support Ticket.</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <style>
                html,body{margin:0;padding:0;height:100%;}
                img{border:0;display:block;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;}
                a{color:inherit;text-decoration:none;}
                table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;}
                td{word-break:break-word;}
                .email-wrapper{width:100%;background-color:#f4f6f8;padding:20px 12px;}
                .email-content{max-width:900px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;font-family:Inter, "Helvetica Neue", Arial, sans-serif;color:#333333;}
                .email-header{padding:20px;background:#ffffff;text-align:left;border-bottom:1px solid #eef1f4;}
                .logo{height:48px;vertical-align:middle;}
                .preheader{display:none!important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;color:transparent;}
                .body{padding:24px;}
                .title{font-size:20px;font-weight:600;margin:0 0 8px;color:#0f1724;}
                .lead{font-size:14px;color:#475569;margin:0 0 18px;line-height:1.45;}
                .ticket-card{background:#f8fafc;border:1px solid #e6eef6;border-radius:8px;padding:14px;margin-bottom:18px;}
                .row{display:flex;flex-wrap:wrap;gap:12px;}
                .col{flex:1;min-width:140px;padding:15px;}
                .meta-label{font-size:12px;color:#6b7280;margin-bottom:6px;}
                .meta-value{font-size:14px;color:#0f1724;font-weight:600;}
                .btn-wrap{text-align:left;}
                .btn{display:inline-block;padding:12px 18px;background:#0b74ff;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;font-size:14px;}
                .footer{padding:18px 24px;background:#fbfcfd;border-top:1px solid #eef1f4;font-size:13px;color:#6b7280;}
                .small{font-size:12px;color:#94a3b8;}
                @media only screen and (max-width:480px){
                .email-content{border-radius:0;margin:0;}
                .header-right{display:block;width:100%;text-align:right;margin-top:8px;}
                .row{display:block;}
                .col{min-width:100%;}
                .btn{display:block;width:100%;text-align:center;}
                }
            </style>
            </head>
            <body>
            <div class="preheader">New reply from support for ticket #' . $ticket['ticket_no'] . '.</div>
            <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td align="center">
                    <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="email-header">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                            <td align="left" style="vertical-align:middle;">
                                <a href="https://rozeyana.com" target="_blank" aria-label="Rozeyana">
                                <img src="https://rozeyana.com/assets/images/logo/2025_rozeyana_LOGO-ROZYANA-06-2.png"
                                    alt="Rozeyana" class="logo" style="max-height:48px;">
                                </a>
                            </td>
                            <td align="right" class="header-right" style="vertical-align:middle;font-size:13px;color:#6b7280;">
                                Support - Reply from HQ/Staff
                            </td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="body">
                        <h1 class="title">New Reply to Your Support Ticket</h1>
                        <p class="lead">Team HQ (HQ/Staff) has replied to your support ticket.</p>
                        <div class="ticket-card" role="article" aria-label="Ticket summary">
                            <div class="row">
                            <div class="col">
                                <div class="meta-label">Ticket ID</div>
                                <div class="meta-value">' .  $ticket['ticket_no'] . '</div>
                            </div>
                            </div>
                        </div>
                        <div class="btn-wrap" style="margin-bottom:18px;">
                            <a href="' . $domainURL . 'customer/tiket-details?id=' .  $ticket['ticket_no'] . '" target="_blank" class="btn" rel="noopener">View Ticket & Reply</a>
                        </div>
                        <p class="small" style="margin:0 0 10px;">If you didn\'t expect this ticket, please review the requester details before replying.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                            <td style="vertical-align:middle;">
                                <strong>Rozeyana Support</strong><br>
                                <span class="small">You are receiving this email because you are subscribed to support notifications.</span>
                            </td>
                            <td align="right" style="vertical-align:middle;">
                                <span class="small">Need help? <a href="' . $domainURL . 'contact" style="color:#0b74ff;">Contact us</a></span>
                            </td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                    </table>
                </td>
                </tr>
            </table>
            </body>
            </html>
        ';

            if (!empty($replyAttachments)) {
                foreach ($replyAttachments as $f) {
                    $fullPath = __DIR__ . "/../../" . $f['file_path'];
                    if (file_exists($fullPath)) {
                        $mail->addAttachment($fullPath, $f['filename']);
                    }
                }
            }

            $mail->send();

            echo json_encode([
                "success" => true,
                "reply" => [
                    "sender"      => "staff",
                    "senderName"  => "Staff",
                    "message"     => stripslashes($message),
                    "created_at"  => date("Y-m-d H:i:s"),
                    "attachments" => $replyAttachments
                ]
            ]);
        } catch (Exception $e) {
            error_log("Email send failed: " . $e->getMessage());

            echo json_encode([
                "success" => true,
                "reply" => [
                    "sender"      => "staff",
                    "senderName"  => "Staff",
                    "message"     => stripslashes($message),
                    "created_at"  => date("Y-m-d H:i:s"),
                    "attachments" => $replyAttachments
                ],
                "emailWarning" => "Reply saved but email notification failed"
            ]);
        }

        exit;
    }

    public function closeTicket()
    {
        $domainURL = getMainUrl();

        if (!isset($_GET['ticket_id'])) {
            echo json_encode(["success" => false, "message" => "No ticket_id found"]);
            exit;
        }

        $ticketID = intval($_GET['ticket_id']);
        $this->ticketModel->closeTicket($ticketID);

        $ref = $_SERVER['HTTP_REFERER'] ?? '';

        if (str_contains($ref, 'customer/ticket-details')) {
            $_SESSION['success_ticket'] = "Successfully closed the ticket.";
            header("Location: " . $domainURL . "customer/support-ticket");
            exit;
        } else if (str_contains($ref, '/support/tickets')) {
            $_SESSION['success_ticket'] = "Successfully closed the ticket.";
            header("Location: " . $domainURL . "support/tickets");
            exit;
        }
    }
}
