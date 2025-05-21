<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';


define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_USERNAME', 'yourmail@example.com');
define('EMAIL_PASSWORD', 'your_app_password');
define('EMAIL_PORT', 587);
define('EMAIL_FROM', 'yourmail@example.com');
define('EMAIL_FROM_NAME', 'SAHYOG');

function getMailerInstance() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = EMAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = EMAIL_PORT;
    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    return $mail;
}
