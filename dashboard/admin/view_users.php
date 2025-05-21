<?php
include 'session.php';
include '../../includes/database.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch users based on filter or search
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "SELECT * FROM users WHERE 1";
if ($status_filter) {
    $query .= " AND status = '$status_filter'";
}
if ($search_query) {
    $query .= " AND (name LIKE '%$search_query%' OR email LIKE '%$search_query%' OR phone LIKE '%$search_query%')";
}
$query .= " ORDER BY registered_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users - SAHYOG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
        }
        .container {
            padding: 40px;
        }
        table {
            margin-top: 20px;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .form-inline .form-control,
        .form-inline .form-select {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-primary">👥 View Registered Users</h2>

    <!-- Filter & Search Form -->
    <form class="row gy-2 gx-3 align-items-center mb-4" method="GET" action="view_users.php">
        <div class="col-auto">
            <label class="form-label">Filter by Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>

        <div class="col-auto">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Enter name, email, or phone" value="<?= htmlspecialchars($search_query); ?>">
        </div>

        <div class="col-auto mt-4">
            <button type="submit" class="btn btn-primary">Apply</button>
        </div>
    </form>

    <!-- User Table -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['phone']); ?></td>
                            <td><?= htmlspecialchars($row['role']); ?></td>
                            <td><span class="badge bg-<?= $row['status'] === 'approved' ? 'success' : ($row['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                <?= htmlspecialchars($row['status']); ?>
                            </span></td>
                            <td><?= htmlspecialchars($row['registered_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No users found matching your criteria.</div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="approve_users.php" class="btn btn-outline-success me-2">✅ Approve/Reject Users</a>
        <a href="admin.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
