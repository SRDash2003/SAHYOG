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
        $_SESSION['registration_success'] = "Registration successful. Admin will review shortly.";
        header("Location: register.php");
        exit();
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(245, 239, 91);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color:rgb(217, 145, 11);
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }
        input[type="text"], input[type="email"], input[type="file"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            padding: 12px 20px;
            background-color:rgb(174, 155, 8);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #006400;
        }
        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .register {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><strong>Register on SAHYOG</strong></h2>

        <?php
        if (isset($_SESSION['registration_success'])) {
            echo '<div class="success-message">' . $_SESSION['registration_success'] . '</div>';
            unset($_SESSION['registration_success']); // Clear message after showing it
        }
        ?>

        <form action="register.php" method="POST" enctype="multipart/form-data" class="register">
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

        <p><a href="login.php">Already registered? Login here</a></p>
    </div>

    <script>
        // Show extra document field for receivers only
        document.querySelector("select[name='role']").addEventListener("change", function() {
            document.getElementById("receiver-doc").style.display = this.value === "receiver" ? "block" : "none";
        });
    </script>
</body>
</html>
