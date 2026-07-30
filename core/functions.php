<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/plugins/phpmailer/src/Exception.php';
require '../assets/plugins/phpmailer/src/PHPMailer.php';
require '../assets/plugins/phpmailer/src/SMTP.php';

include_once "../config/config.php";

function sendMail($name, $email, $message)
{
    $to = SUPPORT_EMAIL;
    $headers = "From:" . SUPPORT_EMAIL;
    $subject = "Contact Form Submission";
    $mailMessage = 'Name: ' . $name . '<br>' .
        'Email: ' . $email . '<br>' .
        'Message: ' . $message;
    return mail($to, $subject, $mailMessage, $headers);
}

function sendSMTPMail($name, $email, $message)
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPSecure = SMTP_SECURITY;
    $mail->Port = SMTP_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->isHTML(true);

    // Set sender and recipient
    $mail->setFrom(SMTP_USERNAME, SITE_NAME);
    $mail->addAddress(SMTP_TO_EMAIL, SITE_NAME);

    // Set email subject and body
    $mail->Subject = 'Contact Form Submission';
    $mail->Body = 'Name: ' . $name . '<br>' .
        'Email: ' . $email . '<br>' .
        'Message: ' . $message;

    // Send email
    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
