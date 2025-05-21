<?php
include 'session.php';
include '../../includes/database.php';
include 'adheader.php';

$user_id = $_SESSION['user_id'];
$notifications = getNotifications($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - SAHYOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4 mb-5">
            <h2 class="text-center mb-4">👨‍💼 Welcome, Admin!</h2>

            <div class="mb-4">
                <h4>📬 Manage Requests</h4>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="view_requests.php" class="text-decoration-none">🔍 Pending Donation Requests</a>
                    </li>
                </ul>
            </div>

            <div class="mb-4">
                <h4>⚙️ Admin Actions</h4>
                <div class="d-grid gap-2 col-6 mx-auto">
                    <a href="stats.php" class="btn btn-outline-primary">📊 View Donation Report</a>
                    <a href="view_users.php" class="btn btn-outline-primary">👥 Manage Users</a>
                    <a href="credentials.php" class="btn btn-outline-secondary">🆔 Generate Credentials</a>
                    <a href="pickups.php" class="btn btn-outline-success">🚚 Pickup Details</a>
                    <a href="../../reset_password.php" class="btn btn-outline-warning">🔒 Reset Password</a>
                    <a href="logout.php" class="btn btn-outline-danger">🚪 Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
