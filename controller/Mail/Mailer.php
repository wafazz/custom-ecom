<?php

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host = '127.0.0.1';
        $this->mail->Port = 25;
        $this->mail->SMTPAuth = false;
        $this->mail->SMTPAutoTLS = false;

        $this->mail->setFrom('noreply@rozeyana.com', 'Rozeyana');
    }

    public function send(string $to, string $subject, string $html, string $altText = ''): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);

            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $html;
            $this->mail->AltBody = $altText ?: strip_tags($html);

            return $this->mail->send();
        } catch (Exception $e) {
            error_log('Mail error: ' . $this->mail->ErrorInfo);
            return false;
        }
    }
}