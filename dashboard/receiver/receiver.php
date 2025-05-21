<?php
include 'session.php';
include '../../includes/database.php';
include '../../includes/email_config.php';
include 'recheader.php';

if (!isset($_SESSION['receiver_logged_in']) || $_SESSION['role'] !== 'receiver') {
    header("Location: ../../login.php");
    exit();
}

$receiver_id = $_SESSION['user_id'];
$receiver_name = $_SESSION['name'];
$receiver_email = $_SESSION['email'];

// Handle item request (NO EMAIL SENT)
if (isset($_POST['request_item'])) {
    $item_id = $_POST['item_id'];

    $check = $conn->prepare("SELECT id FROM requests WHERE donation_id = ? AND receiver_id = ?");
    $check->bind_param("ii", $item_id, $receiver_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO requests (donation_id, receiver_id, status, requested_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->bind_param("ii", $item_id, $receiver_id);
        $_SESSION['request_success'] = $stmt->execute()
            ? "✅ Request submitted. Admin will review it shortly."
            : "❌ Failed to send request. Please try again later.";
        $stmt->close();
    } else {
        $_SESSION['request_success'] = "⚠️ You have already requested this item.";
    }
    $check->close();
    header("Location: receiver.php");
    exit();
}

// Handle contact/query submission (EMAIL SENT)
if (isset($_POST['contact_us'])) {
    $query_message = $_POST['query_message'];
    require '../../vendor/autoload.php';

    $mail = getMailerInstance();
    $mail->addAddress(EMAIL_FROM, 'SAHYOG Admin');
    $mail->Subject = "New Query from Receiver";
    $mail->Body = "📬 New Query from Receiver\n\n"
                . "Name: $receiver_name\n"
                . "ID: $receiver_id\n"
                . "Email: $receiver_email\n\n"
                . "Message:\n$query_message";

    try {
        $mail->send();
        $_SESSION['contact_status'] = "<div class='alert alert-success'>✅ Query sent to admin.</div>";
    } catch (Exception $e) {
        $_SESSION['contact_status'] = "<div class='alert alert-danger'>❌ Failed to send: {$mail->ErrorInfo}</div>";
    }
    header("Location: receiver.php");
    exit();
}

// Fetch available donations
$sql = "SELECT * FROM donations WHERE status = 'available' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receiver Dashboard - SAHYOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .feed-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .feed-image {
            max-height: 200px;
            object-fit: cover;
            width: 65%;
            border-radius: 5px;
        }
        .btn-request {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-4">

    <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($receiver_name); ?> 👋</h2>

    <div class="mb-3">
        <a href="../../reset_password.php" class="btn btn-primary">🔒 Reset Password</a>
        <a href="my_requests.php" class="btn btn-secondary">📋 My Requests</a>
    </div>

    <!-- Alert Messages -->
    <?php
    if (isset($_SESSION['request_success'])) {
        echo "<div class='alert alert-info'>" . $_SESSION['request_success'] . "</div>";
        unset($_SESSION['request_success']);
    }
    if (isset($_SESSION['contact_status'])) {
        echo $_SESSION['contact_status'];
        unset($_SESSION['contact_status']);
    }
    ?>

    <!-- Donation Feed -->
    <h3 class="mb-3">📦 Available Donations</h3>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="feed-card">
                <h4 class="mb-2"><?php echo htmlspecialchars($row['item_name']); ?></h4>
                <?php if (!empty($row['item_photo'])): ?>
                    <img src="../../uploads/<?php echo $row['item_photo']; ?>" class="feed-image mb-3" alt="Item Image">
                <?php endif; ?>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['item_description']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Claim Duration:</strong> <?php echo htmlspecialchars($row['claim_duration']); ?></p>
                <p><small class="text-muted">🕒 Posted on: <?php echo $row['created_at']; ?></small></p>

                <form method="post">
                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="request_item" class="btn btn-request">Request this Item</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning">No donation items available right now. Please check back later!</div>
    <?php endif; ?>

    <!-- Contact Form -->
    <h3 class="mt-5">💬 Contact Us</h3>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <textarea class="form-control" name="query_message" rows="4" placeholder="Enter your query or message..." required></textarea>
        </div>
        <button type="submit" name="contact_us" class="btn btn-dark">Send Query</button><br><br>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
