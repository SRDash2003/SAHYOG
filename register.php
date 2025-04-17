<?php
include 'includes/session.php';
include 'includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $gov_id_type = mysqli_real_escape_string($conn, $_POST['gov_id_type']);
    $status = 'pending'; // For admin verification

    // File Upload Handling
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create uploads folder if not exists
    }

    // Upload Govt. ID Proof
    $gov_id_file = $uploadDir . basename($_FILES['govt_id']['name']);
    if (move_uploaded_file($_FILES['govt_id']['tmp_name'], $gov_id_file)) {
        $gov_id_file = mysqli_real_escape_string($conn, $gov_id_file);
    } else {
        die("Error uploading Government ID Proof.");
    }

    // Upload Organization Proof (if receiver)
    $org_doc = NULL;
    if ($role === "receiver" && !empty($_FILES['org_proof']['name'])) {
        $org_doc = $uploadDir . basename($_FILES['org_proof']['name']);
        if (move_uploaded_file($_FILES['org_proof']['tmp_name'], $org_doc)) {
            $org_doc = mysqli_real_escape_string($conn, $org_doc);
        } else {
            die("Error uploading Organization Proof.");
        }
    }

    // Insert into Database (No password yet, status pending)
    $sql = "INSERT INTO users (name, email, phone, role, gov_id_type, gov_id_file, org_doc, status)
            VALUES ('$name', '$email', '$phone', '$role', '$gov_id_type', '$gov_id_file', " . ($org_doc ? "'$org_doc'" : "NULL") . ", '$status')";

    if (mysqli_query($conn, $sql)) {
        echo "Registration request submitted! Please wait for admin approval. You will receive your login credentials via email once approved.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - SAHYOG</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
    <div class="container">
        <h2>Register on SAHYOG</h2>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <label for="name">Full Name:</label>
            <input type="text" name="name" required />

            <label for="email">Email:</label>
            <input type="email" name="email" required />

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" required />

            <label for="role">Register as:</label>
            <select name="role" required>
                <option value="donor">Donor</option>
                <option value="receiver">Receiver (NGO, etc.)</option>
            </select>

            <label for="gov_id_type">Government ID Type:</label>
            <select name="gov_id_type" required>
                <option value="Aadhaar">Aadhaar</option>
                <option value="PAN">PAN</option>
                <option value="Other">Other</option>
            </select>

            <label for="govt_id">Upload Govt. ID Proof:</label>
            <input type="file" name="govt_id" accept="image/*,.pdf" required />

            <div id="receiver-doc" style="display: none;">
                <label for="org_proof">Upload Organization Proof (Only for Receivers):</label>
                <input type="file" name="org_proof" accept="image/*,.pdf" />
            </div>

            <button type="submit" name="register">Register</button>
        </form>
    </div>

    <script>
        // Show extra document field for receivers only
        document.querySelector("select[name='role']").addEventListener("change", function() {
            document.getElementById("receiver-doc").style.display = this.value === "receiver" ? "block" : "none";
        });
    </script>
</body>
</html>
