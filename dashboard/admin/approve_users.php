<?php
include 'session.php';
include '../../includes/database.php';
//include '../includes/header.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch pending users
$sql = "SELECT * FROM users WHERE status = 'pending'";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    echo "<p>Error in SQL query: " . mysqli_error($conn) . "</p>";
    exit();  // Stop the script execution if the query failed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Users - Admin Panel | SAHYOG</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            padding: 25px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a.view-link {
            color: #17a2b8;
            text-decoration: none;
            font-weight: bold;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 2px;
        }

        .approve {
            background-color: #28a745;
            color: white;
        }

        .reject {
            background-color: #dc3545;
            color: white;
        }

        .action-btn:hover {
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
                text-align: left;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                top: 12px;
                padding-left: 15px;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php" class="back-link">← Back to Dashboard</a>
        <h2>Pending User Approvals</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>ID Proof</th>
                    <th>Org Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td data-label="Name"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td data-label="Phone"><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td data-label="Role"><?php echo ucfirst($row['role']); ?></td>
                        <td data-label="ID Proof">
                            <a class="view-link" href="viewimage.php?id=<?php echo $row['id'] ?>" target="_blank">View ID</a>
                        </td>
                        <td data-label="Org Document">
                            <a class="view-link" href="viewimage.php?id=<?php echo $row['id'] ?>" target="_blank">View Doc</a>
                        </td>
                        <td data-label="Actions">
                            <a href="approve_users.php?action=approve&id=<?php echo $row['id']; ?>">
                                <button class="action-btn approve">Approve</button>
                            </a>
                            <a href="approve_users.php?action=reject&id=<?php echo $row['id']; ?>">
                                <button class="action-btn reject">Reject</button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Handle Approve and Reject actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $update = "UPDATE users SET status='approved' WHERE id='$userId'";
    } elseif ($action === 'reject') {
        $update = "UPDATE users SET status='rejected' WHERE id='$userId'";
    }

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('User status updated successfully!'); window.location='approve_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user status: " . mysqli_error($conn) . "');</script>";
    }
}
?>
