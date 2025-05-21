<?php
include 'session.php';
include '../../includes/database.php';

if (!isset($_SESSION['donor_logged_in']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../../login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$upload_dir = '../../uploads/';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $claim_duration = $_POST['claim_duration'];
    $location = $_POST['location'];
    $category = $_POST['category'];

    $status = 'available';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    $item_photo = "";
    if (isset($_FILES['item_photo']) && $_FILES['item_photo']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES['item_photo']['tmp_name'];
        $photo_name = uniqid("item_") . '_' . basename($_FILES['item_photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (move_uploaded_file($photo_tmp, $photo_path)) {
            $item_photo = $photo_name;
        } else {
            $message = "<div class='alert alert-danger'>❌ Failed to upload image.</div>";
        }
    }

    if ($item_photo) {
        $sql = "INSERT INTO donations (donor_id, item_name, item_description, item_photo, claim_duration, location, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $donor_id, $item_name, $description, $item_photo, $claim_duration, $location, $status, $created_at, $updated_at);

        if ($stmt->execute()) {
            $_SESSION['donation_msg'] = "<div class='alert alert-success'>✅ Item posted successfully!</div>";
        } else {
            $_SESSION['donation_msg'] = "<div class='alert alert-danger'>❌ Database error: " . $stmt->error . "</div>";
        }
    }

    header("Location: post_item.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post New Item - SAHYOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .form-container {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container form-container">
    <h2>📝 Post a New Donation Item</h2>
    <a class="back-link" href="donor.php">&larr; Back to Dashboard</a>

    <?php
    if (isset($_SESSION['donation_msg'])) {
        echo $_SESSION['donation_msg'];
        unset($_SESSION['donation_msg']);
    }
    echo $message;
    ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="clothes">Clothes</option>
                <option value="food">Food</option>
                <option value="books">Books</option>
                <option value="electronics">Electronics</option>
                <option value="toys">Toys</option>
                <option value="others">Others</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Claim Duration (e.g., 3 days)</label>
            <input type="text" name="claim_duration" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Item Image</label>
            <input type="file" name="item_photo" class="form-control" accept="image/*" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success">Post Item</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
