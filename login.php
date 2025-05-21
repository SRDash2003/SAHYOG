<?php
include 'includes/session.php';
include 'includes/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
        
        // Hash the input password using MD5
        $hashed_password = md5($password);

        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // Compare hashed input with stored hashed password
            if ($hashed_password === $row['password']) {
                if ($row['status'] === 'approved') {
                    // Set session variables
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['admin_logged_in']=1;
                    $_SESSION['donor_logged_in']=1;
                    $_SESSION['receiver_logged_in']=1;
                    $_SESSION['email'] = $row['email'];

                    // Role-based redirection
                    if ($row['role'] === 'admin') {
                        header("Location: dashboard/admin/admin.php");
                    } elseif ($row['role'] === 'donor') {
                        header("Location: dashboard/donor/donor.php");
                    } elseif ($row['role'] === 'receiver') {
                        header("Location: dashboard/receiver/receiver.php");
                    } else {
                        $error = "Invalid role. Please contact support.";
                    }
                    exit();
                } else {
                    $error = "Your account is not approved yet. Please wait for admin approval.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - SAHYOG</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/style.css" />
  <style>
    body {
      background-color:rgb(146, 190, 235);
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 400px;
      margin-top: 100px;
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #0056b3;
    }
    form {
      margin-top: 20px;
    }
    label {
      font-weight: bold;
      color: #333;
    }
    input[type="email"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    button[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color:rgb(78, 140, 215);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    button[type="submit"]:hover {
      background-color: #45a049;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 20px;
    }
    .forgot-password {
      display: block;
      text-align: center;
      margin-top: 10px;
    }
    .forgot-password a {
      color: #007bff;
      text-decoration: none;
    }
    .forgot-password a:hover {
      text-decoration: underline;
    }
    .register-link {
      text-align: center;
      margin-top: 20px;
    }
    .register-link a {
      color: #007bff;
      text-decoration: none;
    }
    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2><strong>Login to SAHYOG</strong></h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
      <label for="email">Email:</label>
      <input type="email" name="email" required />
      <label for="password">Password:</label>
      <input type="password" name="password" required />
      <button type="submit" name="login">Login</button>
    </form>
    <p class="forgot-password"><a href="reset_password.php">Forgot Password?</a></p>
    <p class="register-link">Don't have an account? <a href="register.php">Register here</a>.</p>
  </div>
</body>
</html>
