<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome to SAHYOG</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container">
    <h1>Welcome to SAHYOG</h1>
    <p>Your gateway to giving and receiving help!</p>

    <?php if (isset($_SESSION['user_id'])): ?>
      <p>You are logged in as <strong><?= htmlspecialchars($_SESSION['name']); ?></strong>.</p>
      <a href="dashboard/<?php echo $_SESSION['role']; ?>.php">Go to Dashboard</a>
      <!-- <a href="logout.php">Logout</a> -->
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</body>
</html>
