<?php
include 'session.php';
include '../../includes/database.php';
include '../../includes/email_config.php';
include 'donheader.php';

if (!isset($_SESSION['donor_logged_in']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../../login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch notifications
$notifications = getNotifications($donor_id);

// Get total donations count
$donation_count_sql = "SELECT COUNT(*) AS total FROM donations WHERE donor_id = ?";
$stmt = $conn->prepare($donation_count_sql);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$donationStats = $result->fetch_assoc();
$total_donations = $donationStats['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Dashboard - SAHYOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary">Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        <a href="../../reset_password.php" class="btn btn-outline-primary mt-2">🔒 Reset Password</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">📊 Your Donation Stats</h4>
                    <p class="card-text fs-5">Total Donations Made: <strong><?php echo $total_donations; ?></strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <a href="donor_requests.php" class="btn btn-success mt-md-4">📥 View Pending Requests</a>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            📦 Your Donation History
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Posted On</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query = "SELECT * FROM donations WHERE donor_id = ? ORDER BY created_at DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $donor_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $i = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$i}</td>
                                    <td>".htmlspecialchars($row['item_name'])."</td>
                                    <td>".htmlspecialchars($row['item_description'])."</td>
                                    <td><span class='badge bg-info text-dark'>".ucfirst(htmlspecialchars($row['status']))."</span></td>
                                    <td>".htmlspecialchars($row['created_at'])."</td>
                                  </tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>You haven't donated anything yet.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center mb-5">
        <h4>➕ Want to Donate More?</h4>
        <a href="post_item.php" class="btn btn-secondary">Post New Item</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            📧 Contact Us
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="query" class="form-label">Your Message to Admin:</label>
                    <textarea name="query" id="query" rows="4" class="form-control" required></textarea>
                </div>
                <button type="submit" name="send_query" class="btn btn-primary">Send</button>
            </form>

            <?php
            if (isset($_SESSION['contact_status'])) {
                echo $_SESSION['contact_status'];
                unset($_SESSION['contact_status']);
            }
            if (isset($_SESSION['request_success'])) {
                echo $_SESSION['request_success'];
                unset($_SESSION['request_success']);
            }

            if (isset($_POST['send_query'])) {
                $message = htmlspecialchars($_POST['query']);
                $email = $_SESSION['email'];
                $name = $_SESSION['name'];
                $user_id = $_SESSION['user_id'];
                $role = $_SESSION['role'];

                require '../../vendor/autoload.php';
                $mail = getMailerInstance();
                $mail->addAddress(EMAIL_FROM, 'SAHYOG Admin');
                $mail->Subject = "Query from $role - $name (UID: $user_id)";
                $mail->Body = "Query from $role\n"
                            . "-------------------------\n"
                            . "User ID: $user_id\n"
                            . "Name: $name\n"
                            . "Email: $email\n\n"
                            . "Message:\n$message";

                try {
                    $mail->send();
                    $_SESSION['contact_status'] = "<div class='alert alert-success mt-3'>✅ Your query has been sent to the admin.</div>";
                } catch (Exception $e) {
                    $_SESSION['contact_status'] = "<div class='alert alert-danger mt-3'>❌ Failed to send query: {$mail->ErrorInfo}</div>";
                }

                header("Location: donor.php");
                exit();
            }
            ?>
        </div>
    </div>

    <div class="text-end">
        <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
