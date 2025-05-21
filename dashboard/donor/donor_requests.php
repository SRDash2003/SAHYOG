<?php
include 'session.php';
include '../../includes/database.php';
include '../../includes/notifications.php';
include '../../includes/email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['donor_logged_in']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../../login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch requests for this donor's items
$request_sql = "
    SELECT r.id AS request_id, d.item_name, r.status, u.name AS receiver_name, u.id AS receiver_id, d.id AS donation_id, r.requested_at
    FROM requests r
    INNER JOIN donations d ON r.donation_id = d.id
    INNER JOIN users u ON r.receiver_id = u.id
    WHERE d.donor_id = ? AND (r.status = 'approved' OR r.status = 'accepted')
    ORDER BY r.requested_at DESC
";

$stmt = $conn->prepare($request_sql);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$requests_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donor Requests - SAHYOG</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h1 { color: #333; }
        a { text-decoration: none; background: #eee; padding: 5px 10px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        form { margin-top: 10px; }
        button { padding: 5px 10px; margin-right: 5px; }
        textarea, input[type="date"] { width: 100%; padding: 5px; }
    </style>
</head>
<body>

<h1>📥 Pending Requests for Your Items</h1>
<a href="donor.php">← Back to Dashboard</a>

<?php if ($requests_result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Receiver</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($req = $requests_result->fetch_assoc()):
            $request_id = $req['request_id'];
            $item_name = $req['item_name'];
            $receiver_name = $req['receiver_name'];
            $receiver_id = $req['receiver_id'];

            // Check if pickup details already exist
            $pickup_check_sql = "SELECT id FROM pickup_details WHERE request_id = ?";
            $pickup_stmt = $conn->prepare($pickup_check_sql);
            $pickup_stmt->bind_param("i", $request_id);
            $pickup_stmt->execute();
            $pickup_result = $pickup_stmt->get_result();
            $pickup_exists = $pickup_result->num_rows > 0;
        ?>
            <tr>
                <td><?= htmlspecialchars($item_name) ?></td>
                <td><?= htmlspecialchars($receiver_name) ?></td>
                <td><?= ucfirst($req['status']) ?></td>
                <td><?= $req['requested_at'] ?></td>
                <td>
                    <?php if ($req['status'] === 'approved'): ?>
                        <form method="post">
                            <input type="hidden" name="request_id" value="<?= $request_id ?>">
                            <button type="submit" name="accept_request">✅ Accept</button>
                            <button type="submit" name="reject_request">❌ Reject</button>
                        </form>
                    <?php elseif ($req['status'] === 'accepted'): ?>
                        <?php if (!$pickup_exists): ?>
                            <form method="post">
                                <input type="hidden" name="pickup_request_id" value="<?= $request_id ?>">
                                <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                                <input type="hidden" name="receiver_name" value="<?= htmlspecialchars($receiver_name) ?>">
                                <input type="hidden" name="item_name" value="<?= htmlspecialchars($item_name) ?>">
                                <label><strong>🚚 Provide Pickup Date:</strong></label>
                                <input type="date" name="pickup_date" required>
                                <label><strong>Full Address:</strong></label>
                                <textarea name="pickup_address" rows="3" required></textarea>
                                <label><strong>Notes (optional):</strong></label>
                                <textarea name="pickup_notes" rows="2"></textarea>
                                <button type="submit" name="submit_pickup_details">📩 Submit</button>
                            </form>
                        <?php else: ?>
                            <em>✅ Pickup details submitted.</em>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No requests for your items yet.</p>
<?php endif; ?>

</body>
</html>

<?php
// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Accept / Reject Request
    if (isset($_POST['accept_request']) || isset($_POST['reject_request'])) {
        $request_id = intval($_POST['request_id']);
        $new_status = isset($_POST['accept_request']) ? 'accepted' : 'rejected';

        $update_sql = "UPDATE requests SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_status, $request_id);

        if ($stmt->execute()) {
            createNotification(4, "Donor has $new_status the request ID #$request_id.");
            header("Location: donor_requests.php");
            exit();
        } else {
            echo "<p style='color:red;'>Failed to update request status.</p>";
        }
    }

    // Submit Pickup Details
    if (isset($_POST['submit_pickup_details'])) {
        $request_id = intval($_POST['pickup_request_id']);
        $pickup_date = $_POST['pickup_date'];
        $pickup_address = trim($_POST['pickup_address']);
        $pickup_notes = trim($_POST['pickup_notes']);
        $receiver_id = intval($_POST['receiver_id']);
        $receiver_name = $_POST['receiver_name'];
        $item_name = $_POST['item_name'];

        // Fetch receiver email
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $receiver_id);
        $stmt->execute();
        $email_result = $stmt->get_result();
        $receiver_email = $email_result->fetch_assoc()['email'];

        // Insert pickup details
        $insert_sql = "INSERT INTO pickup_details (request_id, donor_id, pickup_date, pickup_address, notes)
                       VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iisss", $request_id, $donor_id, $pickup_date, $pickup_address, $pickup_notes);
        if ($stmt->execute()) {
            createNotification($receiver_id, "Pickup details for your item '$item_name' have been submitted.");
            createNotification(4, "Donor submitted pickup details for request #$request_id.");
            header("Location: donor_requests.php");
            exit();
        } else {
            echo "<p style='color:red;'>Failed to submit pickup details.</p>";
        }
    }
}
?>
