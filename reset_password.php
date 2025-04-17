<?php
session_start();
include 'includes/database.php';
require 'vendor/autoload.php';
require_once __DIR__ . '/includes/email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$step = 'email_input';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !isset($_POST['otp'])) {
        $email = $_POST['email'];
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $_SESSION['reset_email'] = $email;
            $otp = rand(100000, 999999);
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['otp_expires'] = time() + 59;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = EMAIL_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = EMAIL_USERNAME;
                $mail->Password = EMAIL_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = EMAIL_PORT;

                $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
                $mail->addAddress($email);
                $mail->Subject = 'Your OTP for SAHYOG Password Reset';
                $mail->Body = "Your OTP is: $otp\n\nThis OTP will expire in 59 seconds.";

                $mail->send();
                $message = "OTP sent to your email. OTP will expire in 59 secs.";
                $step = 'otp_input';
            } catch (Exception $e) {
                $message = "Failed to send OTP. Please try again.";
            }
        } else {
            $message = "No account found with that email.";
        }

    } elseif (isset($_POST['otp']) && isset($_SESSION['reset_otp'])) {
        $otp_input = $_POST['otp'];
        if (time() > $_SESSION['otp_expires']) {
            $message = "OTP expired. Try again.";
            session_unset();
        } elseif ($otp_input == $_SESSION['reset_otp']) {
            $message = "OTP verified.";
            $step = 'new_password';
        } else {
            $message = "Incorrect OTP.";
            $step = 'otp_input';
        }

    } elseif (isset($_POST['new_password']) && isset($_SESSION['reset_email'])) {
        $new_pass = md5($_POST['new_password']);
        $email = $_SESSION['reset_email'];
        mysqli_query($conn, "UPDATE users SET password = '$new_pass' WHERE email = '$email'");
        $message = "Password reset successful. You can now <a href='login.php'>login</a>.";
        session_unset();
        $step = 'done';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - SAHYOG</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <p><?= $message ?></p>

    <?php if ($step === 'email_input'): ?>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <button type="submit">Send OTP</button>
        </form>
    <?php elseif ($step === 'otp_input'): ?>
        <form method="POST">
            <label>Enter OTP:</label>
            <input type="number" name="otp" required>
            <button type="submit">Verify OTP</button>
        </form>
    <?php elseif ($step === 'new_password'): ?>
        <form method="POST">
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
