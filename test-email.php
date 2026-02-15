<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = '127.0.0.1';
    $mail->Port = 25;
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;

    $mail->setFrom('noreply@rozeyana.com', 'Rozeyana');
    $mail->addAddress('fakrul2897@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body = '<h2>Hello</h2><p>This email is sent via Postfix on VPS</p>';
    $mail->AltBody = 'This email is sent via Postfix on VPS';

    $mail->send();
    echo 'Email sent successfully';

} catch (Exception $e) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}