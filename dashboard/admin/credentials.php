<?php
include 'session.php';
include '../../includes/database.php';
include '../../includes/notifications.php';
require '../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Generate a random password
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $password = generatePassword();
    $hashed_password = md5($password);

    $subject = "SAHYOG - Your Login Credentials";
    $message = "Hello,\n\nYour registration has been approved!\nHere are your login credentials:\n\nUser ID: $user_id\nPassword: $password\n\nPlease change your password after logging in.\n\nRegards,\nSAHYOG Team";
    
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
        $mail->Subject = $subject;
        $mail->Body = $message;
    
        $mail->send();
    
        // ✅ Email sent successfully — now update DB
        $update_query = "UPDATE users SET password='$hashed_password' WHERE id=$user_id";
        if (mysqli_query($conn, $update_query)) {
            createNotification($user_id, "Your credentials have been sent to your email.");
            echo "<p class='alert alert-success'>Credentials generated and sent to: $email</p>";
        } else {
            echo "<p class='alert alert-danger'>Email sent, but failed to update password in database.</p>";
        }
    
    } catch (Exception $e) {
        echo "<p class='alert alert-danger'>Failed to send email. Error: {$mail->ErrorInfo}</p>";
    }
    
}

// Fetch approved users who don’t have passwords yet
$query = "SELECT * FROM users WHERE status = 'approved' AND password IS NULL";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Credentials - SAHYOG</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .container {
            max-width: 1000px;
            margin-top: 30px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table th, table td {
            text-align: center;
            vertical-align: middle;
        }

        .btn-custom {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-generate {
            background-color: #28a745;
            color: white;
        }

        .btn-generate:hover {
            background-color: #218838;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Generate Login Credentials for Approved Users</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="email" value="<?= $row['email'] ?>">
                                    <button type="submit" class="btn btn-custom btn-generate">Generate & Send</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No approved users awaiting credentials.</p>
        <?php endif; ?>
        <a href="admin.php" class="back-link">← Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
