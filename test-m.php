<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';
require 'email-order.php'; // Contains the getEmailTemplate() function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = [
    'CustomerName' => 'fakrul',
    'OrderID' => '1',
    'OrderLink' => $domainURL."order-details/1",
];

$emailHTML = getEmailTemplate($data);

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '889d41001@smtp-brevo.com';      // Replace with your Brevo login
    $mail->Password   = 'xsmtpsib-XXXXXXXXXXXXXXXXXXXX';        // Brevo SMTP key
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('orders-noreply@rozeyana.com', 'Rozeyana.com');
    $mail->addAddress('fakrul2897@gmail.com', 'fakrul');

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Order Confirmation - Rozeyana';
    $mail->Body    = $emailHTML;
    $mail->AltBody = 'Thank you for your order #1. Visit '.$domainURL.'order-details/1 for details.';

    $mail->send();
    echo "✅ Email sent successfully!";
} catch (Exception $e) {
    echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}