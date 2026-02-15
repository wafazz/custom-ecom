<?php

namespace Ecom;

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('max_execution_time', '300');

require_once __DIR__ . '/../../config/mainConfig.php';

class supportController
{
    public function index()
    {
        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Main";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);
        $successTicket = $_SESSION['success_ticket'] ?? null;
        $errorTicket   = $_SESSION['error_ticket'] ?? null;
        $ticketURL     = $_SESSION['ticketURL'] ?? null;

        // Destroy messages after loading
        unset($_SESSION['success_ticket']);
        unset($_SESSION['error_ticket']);
        unset($_SESSION['ticketURL']);
        require_once __DIR__ . '/../../view/ecom/e-supportMain-keya88.php';
    }

    public function submittedTicket()
    {
        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Main";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);

        if (isset($_POST['newTicket'])) {

            $full_name   = mysqli_real_escape_string($conn, $_POST['full_name']);
            $email       = mysqli_real_escape_string($conn, $_POST['email']);
            $title       = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $priority    = mysqli_real_escape_string($conn, $_POST['priority']);
            $order_id    = mysqli_real_escape_string($conn, $_POST['order_id']) ?? '';

            $result = $conn->query("SELECT id FROM cs_customers WHERE email = '$email' LIMIT 1");

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $customer_id = $row['id'];
            } else {
                $conn->query("INSERT INTO cs_customers (name, email, created_at) VALUES ('$full_name', '$email', NOW())");
                $customer_id = $conn->insert_id;
            }

            $ticket_no = 'ST_' . date('YmdHis') . '_' . rand(100, 999);

            $sql = "INSERT INTO cs_tickets 
        (customer_name, customer_email, ticket_no, customer_id, title, description, status, order_id, assigned_to, created_at, updated_at, priority)
        VALUES 
        ('$full_name', '$email', '$ticket_no', $customer_id, '$title', '$description', 'new', '$order_id', 0, NOW(), NOW(), '$priority')";
            $conn->query($sql);

            $ticket_id = $conn->insert_id;

            $savedFiles = [];

            if (!empty($_FILES['attachments']['name'][0])) {
                $uploadDir = "assets/tickets/upload/" . date("Y") . "/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['attachments']['name'] as $key => $name) {
                    $tmp  = $_FILES['attachments']['tmp_name'][$key];
                    $type = mysqli_real_escape_string($conn, $_FILES['attachments']['type'][$key]);
                    $name = mysqli_real_escape_string($conn, $name);

                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $newName = uniqid() . "." . $ext;
                    $path = $uploadDir . $newName;

                    $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'webm'];
                    if (in_array($ext, $videoExtensions)) {
                        // Temp compressed file
                        $compressedFile = $uploadDir . "compressed_" . $newName;

                        // FFmpeg command to compress video (CRF 28, scale if needed)
                        $cmd = "ffmpeg -i " . escapeshellarg($tmp) . " -vcodec libx264 -crf 28 -preset fast -b:a 128k " . escapeshellarg($compressedFile);
                        exec($cmd, $output, $return_var);

                        if ($return_var === 0 && file_exists($compressedFile)) {
                            // Check if compressed video > 5MB, optionally reduce CRF or resolution
                            if (filesize($compressedFile) > 5 * 1024 * 1024) {
                                // Optionally: reduce CRF further or scale resolution
                                $cmd = "ffmpeg -i " . escapeshellarg($compressedFile) . " -vcodec libx264 -crf 32 -preset fast -b:a 128k " . escapeshellarg($path);
                                exec($cmd);
                                unlink($compressedFile);
                            } else {
                                rename($compressedFile, $path);
                            }
                        } else {
                            // If FFmpeg fails, fallback to original upload
                            move_uploaded_file($tmp, $path);
                        }
                    } else {
                        // Not a video, normal upload
                        move_uploaded_file($tmp, $path);
                    }

                    $conn->query("INSERT INTO cs_ticket_attachments 
                (ticket_id, filename, file_path, file_type, uploaded_by, created_at)
                VALUES 
                ($ticket_id, '$newName', '$path', '$type', 'customer', NOW())");

                    $savedFiles[] = [
                        "path" => $path,
                        "name" => $name
                    ];
                }

                $saveLogs = $conn->query("INSERT INTO cs_ticket_logs 
            (ticket_id, action, action_by, previous_value, new_value, created_at)
            VALUES 
            ($ticket_id, 'attachments_uploaded', 0, '', 'uploaded', NOW())");

                if ($saveLogs) {

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
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Your support ticket #' . $ticket_no . ' has been created';
                        $mail->Body    = '
                        <!doctype html>
                        <html lang="en">
                        <head>
                        <meta charset="utf-8">
                        <title>New Support Ticket</title>
                        <meta name="viewport" content="width=device-width,initial-scale=1">
                        <style>
                            /* CLIENT-SAFE RESET */
                            html,body{margin:0;padding:0;height:100%;}
                            img{border:0;display:block;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;}
                            a{color:inherit;text-decoration:none;}
                            table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;}
                            td{word-break:break-word;}

                            /* LAYOUT */
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

                            .message{background:#ffffff;border:1px solid #eef2f7;border-radius:6px;padding:14px;margin-bottom:18px;font-size:14px;color:#334155;line-height:1.5;white-space:pre-wrap;}

                            .btn-wrap{text-align:left;}
                            .btn{
                            display:inline-block;
                            padding:12px 18px;
                            background:#0b74ff;
                            color:#ffffff;
                            text-decoration:none;
                            border-radius:6px;
                            font-weight:600;
                            font-size:14px;
                            }

                            .footer{padding:18px 24px;background:#fbfcfd;border-top:1px solid #eef1f4;font-size:13px;color:#6b7280;}
                            .small{font-size:12px;color:#94a3b8;}

                            /* MOBILE */
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
                        <!-- Preheader text (shows in inbox preview) -->
                        <div class="preheader">New support ticket #' . $ticket_no . ' created — ' . $title . '</div>
                        
                        <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                            <td align="center">
                                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Header -->
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
                                            Support • New Ticket
                                        </td>
                                        </tr>
                                    </table>
                                    </td>
                                </tr>

                                <!-- Body -->
                                <tr>
                                    <td class="body">
                                    <h1 class="title">New support ticket created</h1>
                                    <p class="lead">A new ticket has been submitted. Below are the details — reply or click the button to view the full ticket.</p>

                                    <!-- Ticket summary -->
                                    <div class="ticket-card" role="article" aria-label="Ticket summary">
                                        <div class="row">
                                        <div class="col">
                                            <div class="meta-label">Ticket ID</div>
                                            <div class="meta-value">' . $ticket_no . '</div>
                                        </div>
                                        <div class="col">
                                            <div class="meta-label">Subject</div>
                                            <div class="meta-value">' . $title . '</div>
                                        </div>
                                        <div class="col">
                                            <div class="meta-label">Priority</div>
                                            <div class="meta-value">' . $priority . '</div>
                                        </div>
                                        </div>
                                        <div style="height:12px"></div>
                                        <div class="row">
                                        <div class="col">
                                            <div class="meta-label">Created by</div>
                                            <div class="meta-value">' . $full_name . '</div>
                                        </div>
                                        <div class="col">
                                            <div class="meta-label">Created at</div>
                                            <div class="meta-value">' . date('jS M Y h:i A') . '</div>
                                        </div>
                                        </div>
                                    </div>

                                    <!-- Message -->
                                    <div>
                                        <div class="meta-label" style="margin-bottom:8px;">Message</div>
                                        <div class="message">' . $description . '</div>
                                    </div>

                                    <!-- CTA -->
                                    <div class="btn-wrap" style="margin-bottom:18px;">
                                        <a href="' . $domainURL . 'customer/tiket-details?id=' . $ticket_no . '" target="_blank" class="btn" rel="noopener">View Ticket & Reply</a>
                                    </div>

                                    <!-- Additional notes -->
                                    <p class="small" style="margin:0 0 10px;">If you didn\'t expect this ticket, please review the requester details before replying.</p>
                                    </td>
                                </tr>

                                <!-- Footer -->
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

                        if (!empty($savedFiles)) {
                            foreach ($savedFiles as $f) {
                                $fullPath = __DIR__ . "/../../" . $f['path'];
                                if (file_exists($fullPath)) {
                                    $mail->addAttachment($fullPath, $f['name']);
                                }
                            }
                        }

                        $mail->send();
                        $_SESSION['success_ticket'] = "Email sent successfully";
                        $_SESSION["ticketURL"] = $domainURL . "customer/tiket-details?id=" . $ticket_no;
                        header("Location: " . $domainURL . "customer/support-ticket");
                        exit;
                    } catch (Exception $e) {
                        $_SESSION['error_ticket'] = "Email sent successfully";
                        header("Location: " . $domainURL . "customer/support-ticket");
                    }
                }
            }else {
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
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Your support ticket #' . $ticket_no . ' has been created';
                        $mail->Body    = '
                        <!doctype html>
                        <html lang="en">
                        <head>
                        <meta charset="utf-8">
                        <title>New Support Ticket</title>
                        <meta name="viewport" content="width=device-width,initial-scale=1">
                        <style>
                            /* CLIENT-SAFE RESET */
                            html,body{margin:0;padding:0;height:100%;}
                            img{border:0;display:block;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;}
                            a{color:inherit;text-decoration:none;}
                            table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;}
                            td{word-break:break-word;}

                            /* LAYOUT */
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

                            .message{background:#ffffff;border:1px solid #eef2f7;border-radius:6px;padding:14px;margin-bottom:18px;font-size:14px;color:#334155;line-height:1.5;white-space:pre-wrap;}

                            .btn-wrap{text-align:left;}
                            .btn{
                            display:inline-block;
                            padding:12px 18px;
                            background:#0b74ff;
                            color:#ffffff;
                            text-decoration:none;
                            border-radius:6px;
                            font-weight:600;
                            font-size:14px;
                            }

                            .footer{padding:18px 24px;background:#fbfcfd;border-top:1px solid #eef1f4;font-size:13px;color:#6b7280;}
                            .small{font-size:12px;color:#94a3b8;}

                            /* MOBILE */
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
                        <!-- Preheader text (shows in inbox preview) -->
                        <div class="preheader">New support ticket #' . $ticket_no . ' created — ' . $title . '</div>
                        
                        <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                            <td align="center">
                                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Header -->
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
                                            Support • New Ticket
                                        </td>
                                        </tr>
                                    </table>
                                    </td>
                                </tr>

                                <!-- Body -->
                                <tr>
                                    <td class="body">
                                    <h1 class="title">New support ticket created</h1>
                                    <p class="lead">A new ticket has been submitted. Below are the details — reply or click the button to view the full ticket.</p>

                                    <!-- Ticket summary -->
                                    <div class="ticket-card" role="article" aria-label="Ticket summary">
                                        <div class="row">
                                        <div class="col">
                                            <div class="meta-label">Ticket ID</div>
                                            <div class="meta-value">' . $ticket_no . '</div>
                                        </div>
                                        <div class="col">
                                            <div class="meta-label">Subject</div>
                                            <div class="meta-value">' . $title . '</div>
                                        </div>
                                        <div class="col">
                                            <div class="meta-label">Priority</div>
                                            <div class="meta-value">' . $priority . '</div>
                                        </div>
                                        </div>
                                        <div style="height:12px"></div>
                                        <div class="row">
                                        <div class="col">
                                            <div class="meta-label">Created by</div>
                                            <div class="meta-value">' . $full_name . '</div>
                                        </div>
                                        <div class="col">
                                            <div class="meta-label">Created at</div>
                                            <div class="meta-value">' . date('jS M Y h:i A') . '</div>
                                        </div>
                                        </div>
                                    </div>

                                    <!-- Message -->
                                    <div>
                                        <div class="meta-label" style="margin-bottom:8px;">Message</div>
                                        <div class="message">' . $description . '</div>
                                    </div>

                                    <!-- CTA -->
                                    <div class="btn-wrap" style="margin-bottom:18px;">
                                        <a href="' . $domainURL . 'customer/tiket-details?id=' . $ticket_no . '" target="_blank" class="btn" rel="noopener">View Ticket & Reply</a>
                                    </div>

                                    <!-- Additional notes -->
                                    <p class="small" style="margin:0 0 10px;">If you didn\'t expect this ticket, please review the requester details before replying.</p>
                                    </td>
                                </tr>

                                <!-- Footer -->
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

                        if (!empty($savedFiles)) {
                            foreach ($savedFiles as $f) {
                                $fullPath = __DIR__ . "/../../" . $f['path'];
                                if (file_exists($fullPath)) {
                                    $mail->addAttachment($fullPath, $f['name']);
                                }
                            }
                        }

                        $mail->send();
                        $_SESSION['success_ticket'] = "Email sent successfully";
                        $_SESSION["ticketURL"] = $domainURL . "customer/tiket-details?id=" . $ticket_no;
                        header("Location: " . $domainURL . "customer/support-ticket");
                        exit;
                    } catch (Exception $e) {
                        $_SESSION['error_ticket'] = "Email sent successfully";
                        header("Location: " . $domainURL . "customer/support-ticket");
                    }
            }
        }

        // if (isset($_POST['newTicket'])) {

        //     $full_name   = mysqli_real_escape_string($conn, $_POST['full_name']);
        //     $email       = mysqli_real_escape_string($conn, $_POST['email']);
        //     $title       = mysqli_real_escape_string($conn, $_POST['title']);
        //     $description = mysqli_real_escape_string($conn, $_POST['description']);
        //     $priority    = mysqli_real_escape_string($conn, $_POST['priority']);
        //     $order_id    = mysqli_real_escape_string($conn, $_POST['order_id']) ?? '';


        //     $result = $conn->query("SELECT id FROM cs_customers WHERE email = '$email' LIMIT 1");

        //     if ($result->num_rows > 0) {
        //         $row = $result->fetch_assoc();
        //         $customer_id = $row['id'];
        //     } else {
        //         $conn->query("INSERT INTO cs_customers (name, email, created_at) VALUES ('$full_name', '$email', NOW())");
        //         $customer_id = $conn->insert_id;
        //     }


        //     $ticket_no = 'ST_' . date('YmdHis') . '_' . rand(100, 999);

        //     $sql = "INSERT INTO cs_tickets 
        //         (customer_name, customer_email, ticket_no, customer_id, title, description, status, order_id, assigned_to, created_at, updated_at, priority)
        //     VALUES 
        //     ('$full_name', '$email', '$ticket_no', $customer_id, '$title', '$description', 'new', '$order_id', 0, NOW(), NOW(), '$priority')";


        //     $conn->query($sql);
        //     $ticket_id = $conn->insert_id;

        //     if (!empty($_FILES['attachments']['name'][0])) {
        //         $uploadDir = "assets/tickets/upload/" . date("Y") . "/";
        //         if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        //         foreach ($_FILES['attachments']['name'] as $key => $name) {
        //             $tmp  = $_FILES['attachments']['tmp_name'][$key];
        //             $type = mysqli_real_escape_string($conn, $_FILES['attachments']['type'][$key]);
        //             $name = mysqli_real_escape_string($conn, $name);

        //             $ext = pathinfo($name, PATHINFO_EXTENSION);
        //             $newName = uniqid() . "." . $ext;
        //             $path = $uploadDir . $newName;

        //             move_uploaded_file($tmp, $path);
        //             $path_db = mysqli_real_escape_string($conn, $path);

        //             $conn->query("INSERT INTO cs_ticket_attachments 
        //                 (ticket_id, filename, file_path, file_type, uploaded_by, created_at)
        //                 VALUES 
        //                 ($ticket_id, '$newName', '$path_db', '$type', 'customer', NOW())");
        //         }

        //         $saveLogs = $conn->query("INSERT INTO cs_ticket_logs 
        //             (ticket_id, action, action_by, previous_value, new_value, created_at)
        //             VALUES 
        //             ($ticket_id, 'attachments_uploaded', 0, '', 'uploaded', NOW())");

        //         if ($saveLogs) {

        //             $mail = new PHPMailer(true);

        //             try {
        //                 $mail->isSMTP();
        //                 $mail->Host       = 'smtp.gmail.com';
        //                 $mail->SMTPAuth   = true;
        //                 $mail->Username   = 'rozeyanahq.ticket@gmail.com';
        //                 $mail->Password   = 'mrae scfh kjfl cwkf'; // 16-digit app password
        //                 $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //                 $mail->Port       = 587;

        //                 $mail->setFrom('rozeyanahq.ticket@gmail.com', 'Rozeyana Support');
        //                 $mail->addAddress($email);

        //                 $mail->isHTML(true);
        //                 $mail->Subject = 'Your support ticket #' . $ticket_no . ' has been created';
        //                 $mail->Body    = '
        //                 <!doctype html>
        //                 <html lang="en">
        //                 <head>
        //                 <meta charset="utf-8">
        //                 <title>New Support Ticket</title>
        //                 <meta name="viewport" content="width=device-width,initial-scale=1">
        //                 <style>
        //                     /* CLIENT-SAFE RESET */
        //                     html,body{margin:0;padding:0;height:100%;}
        //                     img{border:0;display:block;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;}
        //                     a{color:inherit;text-decoration:none;}
        //                     table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;}
        //                     td{word-break:break-word;}

        //                     /* LAYOUT */
        //                     .email-wrapper{width:100%;background-color:#f4f6f8;padding:20px 12px;}
        //                     .email-content{max-width:900px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;font-family:Inter, "Helvetica Neue", Arial, sans-serif;color:#333333;}
        //                     .email-header{padding:20px;background:#ffffff;text-align:left;border-bottom:1px solid #eef1f4;}
        //                     .logo{height:48px;vertical-align:middle;}
        //                     .preheader{display:none!important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;color:transparent;}

        //                     .body{padding:24px;}
        //                     .title{font-size:20px;font-weight:600;margin:0 0 8px;color:#0f1724;}
        //                     .lead{font-size:14px;color:#475569;margin:0 0 18px;line-height:1.45;}

        //                     .ticket-card{background:#f8fafc;border:1px solid #e6eef6;border-radius:8px;padding:14px;margin-bottom:18px;}
        //                     .row{display:flex;flex-wrap:wrap;gap:12px;}
        //                     .col{flex:1;min-width:140px;padding:15px;}
        //                     .meta-label{font-size:12px;color:#6b7280;margin-bottom:6px;}
        //                     .meta-value{font-size:14px;color:#0f1724;font-weight:600;}

        //                     .message{background:#ffffff;border:1px solid #eef2f7;border-radius:6px;padding:14px;margin-bottom:18px;font-size:14px;color:#334155;line-height:1.5;white-space:pre-wrap;}

        //                     .btn-wrap{text-align:left;}
        //                     .btn{
        //                     display:inline-block;
        //                     padding:12px 18px;
        //                     background:#0b74ff;
        //                     color:#ffffff;
        //                     text-decoration:none;
        //                     border-radius:6px;
        //                     font-weight:600;
        //                     font-size:14px;
        //                     }

        //                     .footer{padding:18px 24px;background:#fbfcfd;border-top:1px solid #eef1f4;font-size:13px;color:#6b7280;}
        //                     .small{font-size:12px;color:#94a3b8;}

        //                     /* MOBILE */
        //                     @media only screen and (max-width:480px){
        //                     .email-content{border-radius:0;margin:0;}
        //                     .header-right{display:block;width:100%;text-align:right;margin-top:8px;}
        //                     .row{display:block;}
        //                     .col{min-width:100%;}
        //                     .btn{display:block;width:100%;text-align:center;}
        //                     }
        //                 </style>
        //                 </head>
        //                 <body>
        //                 <!-- Preheader text (shows in inbox preview) -->
        //                 <div class="preheader">New support ticket #' . $ticket_no . ' created — ' . $title . '</div>

        //                 <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        //                     <tr>
        //                     <td align="center">
        //                         <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        //                         <!-- Header -->
        //                         <tr>
        //                             <td class="email-header">
        //                             <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        //                                 <tr>
        //                                 <td align="left" style="vertical-align:middle;">
        //                                     <a href="https://rozeyana.com" target="_blank" aria-label="Rozeyana">
        //                                     <img src="https://rozeyana.com/assets/images/logo/2025_rozeyana_LOGO-ROZYANA-06-2.png"
        //                                         alt="Rozeyana" class="logo" style="max-height:48px;">
        //                                     </a>
        //                                 </td>
        //                                 <td align="right" class="header-right" style="vertical-align:middle;font-size:13px;color:#6b7280;">
        //                                     Support • New Ticket
        //                                 </td>
        //                                 </tr>
        //                             </table>
        //                             </td>
        //                         </tr>

        //                         <!-- Body -->
        //                         <tr>
        //                             <td class="body">
        //                             <h1 class="title">New support ticket created</h1>
        //                             <p class="lead">A new ticket has been submitted. Below are the details — reply or click the button to view the full ticket.</p>

        //                             <!-- Ticket summary -->
        //                             <div class="ticket-card" role="article" aria-label="Ticket summary">
        //                                 <div class="row">
        //                                 <div class="col">
        //                                     <div class="meta-label">Ticket ID</div>
        //                                     <div class="meta-value">' . $ticket_no . '</div>
        //                                 </div>
        //                                 <div class="col">
        //                                     <div class="meta-label">Subject</div>
        //                                     <div class="meta-value">' . $title . '</div>
        //                                 </div>
        //                                 <div class="col">
        //                                     <div class="meta-label">Priority</div>
        //                                     <div class="meta-value">' . $priority . '</div>
        //                                 </div>
        //                                 </div>
        //                                 <div style="height:12px"></div>
        //                                 <div class="row">
        //                                 <div class="col">
        //                                     <div class="meta-label">Created by</div>
        //                                     <div class="meta-value">' . $full_name . '</div>
        //                                 </div>
        //                                 <div class="col">
        //                                     <div class="meta-label">Created at</div>
        //                                     <div class="meta-value">' . date('jS M Y h:i A') . '</div>
        //                                 </div>
        //                                 </div>
        //                             </div>

        //                             <!-- Message -->
        //                             <div>
        //                                 <div class="meta-label" style="margin-bottom:8px;">Message</div>
        //                                 <div class="message">' . $description . '</div>
        //                             </div>

        //                             <!-- CTA -->
        //                             <div class="btn-wrap" style="margin-bottom:18px;">
        //                                 <a href="' . $domainURL . 'customer/tiket-details?id=' . $ticket_no . '" target="_blank" class="btn" rel="noopener">View Ticket & Reply</a>
        //                             </div>

        //                             <!-- Additional notes -->
        //                             <p class="small" style="margin:0 0 10px;">If you didn\'t expect this ticket, please review the requester details before replying.</p>
        //                             </td>
        //                         </tr>

        //                         <!-- Footer -->
        //                         <tr>
        //                             <td class="footer">
        //                             <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        //                                 <tr>
        //                                 <td style="vertical-align:middle;">
        //                                     <strong>Rozeyana Support</strong><br>
        //                                     <span class="small">You are receiving this email because you are subscribed to support notifications.</span>
        //                                 </td>
        //                                 <td align="right" style="vertical-align:middle;">
        //                                     <span class="small">Need help? <a href="' . $domainURL . 'contact" style="color:#0b74ff;">Contact us</a></span>
        //                                 </td>
        //                                 </tr>
        //                             </table>
        //                             </td>
        //                         </tr>

        //                         </table>
        //                     </td>
        //                     </tr>
        //                 </table>
        //                 </body>
        //                 </html>
        //                 ';

        //                 if (!empty($_FILES['attachments']['name'][0])) {

        //                     foreach ($_FILES['attachments']['name'] as $key => $name) {

        //                         $tmpPath = $_FILES['attachments']['tmp_name'][$key];

        //                         // Skip if file missing
        //                         if (!is_uploaded_file($tmpPath)) continue;

        //                         // Use original filename
        //                         $originalName = basename($name);

        //                         // Attach file
        //                         $mail->addAttachment($tmpPath, $originalName);
        //                     }
        //                 }

        //                 $mail->send();
        //                 echo "Email sent successfully";
        //             } catch (Exception $e) {
        //                 echo "Error: {$mail->ErrorInfo}";
        //             }
        //         }
        //     }
        // }
    }

    public function ticketDetails()
    {
        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Ticket Details";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);

        $successTicket = $_SESSION['success_ticket'] ?? null;
        $errorTicket   = $_SESSION['error_ticket'] ?? null;
        $ticketURL     = $_SESSION['ticketURL'] ?? null;

        // Destroy messages after loading
        unset($_SESSION['success_ticket']);
        unset($_SESSION['error_ticket']);
        unset($_SESSION['ticketURL']);

        // Get ticket ID from query parameter
        $ticket_no = $_GET['id'] ?? '';

        $verifyTicket = $conn->query("SELECT * FROM cs_tickets WHERE ticket_no = '$ticket_no' AND status!= 'closed' LIMIT 1");

        // Fetch ticket details
        // echo "Ticket Details for Ticket No: " . htmlspecialchars(string: $ticket_no);
        require_once __DIR__ . '/../../view/ecom/e-supportTicketMain-keya88.php';
    }

    public function tiketDetails()
    {
        $domainURL = getMainUrl();
        // Get ticket ID from query parameter
        $ticket_no = $_GET['id'] ?? '';

        // Fetch ticket details
        header("Location: " . $domainURL . "customer/ticket-details?id=" . urlencode($ticket_no));
    }

    public function submittedReplyTicket()
    {
        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Main";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);

        if (isset($_POST['newTicket'])) {


            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $ticket_nos = mysqli_real_escape_string($conn, $_POST['ticket_id']);


            $varTicket = $conn->query("SELECT * FROM cs_tickets WHERE ticket_no = '$ticket_nos' LIMIT 1");
            $varTicketData = $varTicket->fetch_assoc();
            $theID = $varTicketData['id'];

            $sql = "INSERT INTO `cs_ticket_replies`(`id`, `ticket_id`, `user_type`, `user_id`, `message`, `created_at`) VALUES (NULL,'$theID','customer','0','$description',NOW())";
            $conn->query($sql);

            $ticket_id = $conn->insert_id;

            $savedFiles = [];

            if (!empty($_FILES['attachments']['name'][0])) {
                $uploadDir = "assets/tickets/upload/" . date("Y") . "/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['attachments']['name'] as $key => $name) {
                    $tmp  = $_FILES['attachments']['tmp_name'][$key];
                    $type = mysqli_real_escape_string($conn, $_FILES['attachments']['type'][$key]);
                    $name = mysqli_real_escape_string($conn, $name);

                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $newName = uniqid() . "." . $ext;
                    $path = $uploadDir . $newName;

                    $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'webm'];
                    if (in_array($ext, $videoExtensions)) {
                        // Temp compressed file
                        $compressedFile = $uploadDir . "compressed_" . $newName;

                        // FFmpeg command to compress video (CRF 28, scale if needed)
                        $cmd = "ffmpeg -i " . escapeshellarg($tmp) . " -vcodec libx264 -crf 28 -preset fast -b:a 128k " . escapeshellarg($compressedFile);
                        exec($cmd, $output, $return_var);

                        if ($return_var === 0 && file_exists($compressedFile)) {
                            // Check if compressed video > 5MB, optionally reduce CRF or resolution
                            if (filesize($compressedFile) > 5 * 1024 * 1024) {
                                // Optionally: reduce CRF further or scale resolution
                                $cmd = "ffmpeg -i " . escapeshellarg($compressedFile) . " -vcodec libx264 -crf 32 -preset fast -b:a 128k " . escapeshellarg($path);
                                exec($cmd);
                                unlink($compressedFile);
                            } else {
                                rename($compressedFile, $path);
                            }
                        } else {
                            // If FFmpeg fails, fallback to original upload
                            move_uploaded_file($tmp, $path);
                        }
                    } else {
                        // Not a video, normal upload
                        move_uploaded_file($tmp, $path);
                    }

                    $conn->query("INSERT INTO cs_reply_attachments 
                (reply_id, filename, file_path, file_type, created_at)
                VALUES 
                ($ticket_id, '$newName', '$path', '$type', NOW())");

                    $savedFiles[] = [
                        "path" => $path,
                        "name" => $name
                    ];
                }

                $saveLogs = $conn->query("INSERT INTO cs_ticket_logs 
            (ticket_id, action, action_by, previous_value, new_value, created_at)
            VALUES 
            ($theID, 'attachments_uploaded', 0, '', 'uploaded', NOW())");

                if ($saveLogs) {

                    $_SESSION['success_ticket'] = "Reply sent successfully";
                    $_SESSION["ticketURL"] = $domainURL . "customer/tiket-details?id=" . $ticket_nos;
                    header("Location: " . $domainURL . "customer/ticket-details?id=" . $ticket_nos);
                    exit;
                } else {
                    $_SESSION['error_ticket'] = "Failed to reply ticket.";
                    header("Location: " . $domainURL . "customer/ticket-details?id=" . $ticket_nos);
                    exit;
                }
            }else {
                $_SESSION['error_ticket'] = "No attachments found.";
                header("Location: " . $domainURL . "customer/ticket-details?id=" . $ticket_nos);
                exit;
            }
        }
    }
}
