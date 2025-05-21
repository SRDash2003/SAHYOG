<?php
include 'session.php';
include '../../includes/database.php';
include_once '../../includes/notifications.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php");
    exit();
}

// Handle Approve/Reject actions
if (isset($_POST['action'], $_POST['request_id'])) {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE requests SET status = 'approved' WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->close();

            $donationQuery = $conn->prepare("SELECT donation_id FROM requests WHERE id = ?");
            $donationQuery->bind_param("i", $request_id);
            $donationQuery->execute();
            $donationQuery->bind_result($donation_id);
            $donationQuery->fetch();
            $donationQuery->close();

            $donorQuery = $conn->prepare("SELECT donor_id FROM donations WHERE id = ?");
            $donorQuery->bind_param("i", $donation_id);
            $donorQuery->execute();
            $donorQuery->bind_result($donor_id);
            $donorQuery->fetch();
            $donorQuery->close();

            $message = "You have a new request for your donation. Please review it in your dashboard.";
            createNotification($donor_id, $message);
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE requests SET status = 'rejected' WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch pending requests
$sql = "SELECT r.id as request_id, r.donation_id, r.requested_at, r.status, 
               d.item_name, d.item_description, d.location, d.item_photo,
               u.name AS receiver_name, u.email AS receiver_email 
        FROM requests r 
        JOIN donations d ON r.donation_id = d.id 
        JOIN users u ON r.receiver_id = u.id 
        WHERE r.status = 'pending' 
        ORDER BY r.requested_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - View Requests | SAHYOG</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2d89ef;
            margin-bottom: 20px;
        }
        a {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #555;
        }
        .request-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            background: #fafafa;
        }
        .request-card img {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 8px;
            border: 2px solid #ccc;
        }
        .request-card h3 {
            color: #333;
            margin-top: 0;
        }
        .request-card p {
            margin: 6px 0;
            color: #444;
        }
        .request-card form {
            margin-top: 15px;
        }
        .request-card button {
            padding: 8px 15px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .request-card button[value="approve"] {
            background-color: #28a745;
            color: #fff;
        }
        .request-card button[value="reject"] {
            background-color: #dc3545;
            color: #fff;
        }
        .no-requests {
            color: #666;
            font-size: 18px;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📬 Pending Donation Requests</h2>
        <a href="admin.php">⬅ Back to Dashboard</a>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="request-card">
                    <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['item_description']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Requested At:</strong> <?php echo htmlspecialchars($row['requested_at']); ?></p>
                    <p><strong>Receiver:</strong> <?php echo htmlspecialchars($row['receiver_name']); ?> (<?php echo htmlspecialchars($row['receiver_email']); ?>)</p>
                    
                    <?php if (!empty($row['item_photo'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($row['item_photo']); ?>" alt="Item Photo">
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                        <button type="submit" name="action" value="approve">Approve</button>
                        <button type="submit" name="action" value="reject">Reject</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-requests">No pending requests at the moment.</div>
        <?php endif; ?>
    </div>
</body>
</html>
