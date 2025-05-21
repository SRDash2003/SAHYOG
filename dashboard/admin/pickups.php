<?php
include '../../includes/database.php';
include 'session.php';

$page_title = "Pickup Details | SAHYOG";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin-top: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .back-link {
            font-size: 16px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        table th, table td {
            text-align: center;
            vertical-align: middle;
        }

        .table-custom {
            margin-top: 20px;
        }

        .table-custom th, .table-custom td {
            vertical-align: middle;
        }

        .table-custom thead {
            background-color: #f8f9fa;
        }

        .table-custom td {
            background-color: #ffffff;
        }

        .table-custom tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Pickup Details for Donated Items</h1>
        <a href="admin.php" class="back-link">⬅️ Back to Admin Dashboard</a>

        <?php
        $sql = "
            SELECT r.id AS request_id, d.item_name, r.status, p.pickup_date, p.pickup_address, p.notes, 
                   u.name AS donor_name, ur.name AS receiver_name
            FROM requests r
            INNER JOIN donations d ON r.donation_id = d.id
            INNER JOIN pickup_details p ON r.id = p.request_id
            INNER JOIN users u ON d.donor_id = u.id
            INNER JOIN users ur ON r.receiver_id = ur.id
            WHERE r.status = 'accepted'
            ORDER BY p.created_at DESC
        ";

        $result = $conn->query($sql);

        if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped table-custom">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Donor</th>
                        <th>Receiver</th>
                        <th>Pickup Date</th>
                        <th>Address</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                            <td><?php echo $row['pickup_date']; ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['pickup_address'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['notes'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">No pickup details submitted yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
