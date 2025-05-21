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
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND status ='approved'");
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
                $message = "OTP sent to your email. OTP will expire in 59 seconds.";
                $step = 'otp_input';
            } catch (Exception $e) {
                $message = "Failed to send OTP. Please try again.";
            }
        } else {
            $message = "No account found with that email OR account not approved by ADMIN yet.";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }
        .container {
            max-width: 420px;
            margin: 60px auto;
            padding: 25px 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        input[type="email"],
        input[type="number"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        button {
            background: #2d89ef;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:disabled {
            background: #aaa;
            cursor: not-allowed;
        }
        p {
            font-size: 15px;
            margin: 10px 0;
        }
        #countdown {
            margin-top: 10px;
            font-weight: bold;
            color: red;
        }
    </style>

    <?php if ($step === 'otp_input'): ?>
    <script>
        let timer = 59;
        const countdown = () => {
            const el = document.getElementById('countdown');
            if (timer > 0) {
                el.textContent = `OTP expires in ${timer} seconds`;
                timer--;
            } else {
                el.textContent = "OTP expired. Please reload the page and try again.";
                document.querySelector("#otp-form button").disabled = true;
            }
        };
        window.onload = () => {
            countdown();
            setInterval(countdown, 1000);
        };
    </script>
    <?php endif; ?>
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
        <form method="POST" id="otp-form">
            <label>Enter OTP:</label>
            <input type="number" name="otp" required>
            <button type="submit">Verify OTP</button>
        </form>
        <p id="countdown"></p>

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
