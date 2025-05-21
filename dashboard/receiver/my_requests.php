<?php
include '../../includes/database.php';
include 'session.php';

if (!isset($_SESSION['receiver_logged_in']) || $_SESSION['role'] !== 'receiver') {
    header("Location: ../../login.php");
    exit();
}

$receiver_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests | SAHYOG</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #2c3e50;
        }

        .back-link {
            text-decoration: none;
            background: #3498db;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .back-link:hover {
            background: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        thead {
            background-color: #3498db;
            color: white;
        }

        th, td {
            padding: 14px 18px;
            text-align: left;
            font-size: 15px;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
            font-size: 14px;
        }

        .accepted {
            background-color: #27ae60;
        }

        .rejected {
            background-color: #e74c3c;
        }

        .no-requests {
            text-align: center;
            color: #888;
            font-size: 18px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>📝 My Requested Items</h1>
        <a class="back-link" href="receiver.php">⬅️ Back to Dashboard</a>
    </div>

    <?php
    $sql = "
        SELECT r.id AS request_id, d.item_name, r.status, p.pickup_date, p.pickup_address
        FROM requests r
        INNER JOIN donations d ON r.donation_id = d.id
        LEFT JOIN pickup_details p ON r.id = p.request_id
        WHERE r.receiver_id = ? AND r.status IN ('accepted', 'rejected')
        ORDER BY r.requested_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Status</th>
                    <th>Pickup Date</th>
                    <th>Pickup Address</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()):
                $statusClass = ($row['status'] === 'accepted') ? 'accepted' : 'rejected';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td><?php echo $row['pickup_date'] ?? 'N/A'; ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['pickup_address'] ?? 'N/A')); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-requests">No requests have been accepted or rejected yet.</div>
    <?php endif; ?>

</body>
</html>
