
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAHYOG - Smart Donation Platform</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="/sahyog/assets/style.css" />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/sahyog/assets/logo.png">
</head>
<body>


<!-- Main Content -->
<div class="container my-5 text-center">
  <h1 class="display-4">Welcome to <span class="text-success">SAHYOG</span></h1>
  <p class="lead">Your gateway to giving and receiving help!</p>

  <?php if (isset($_SESSION['user_id'])): ?>
    <p class="mt-3">You are logged in as <strong><?= htmlspecialchars($_SESSION['name']); ?></strong>.</p>
    <a href="dashboard/<?= $_SESSION['role']; ?>.php" class="btn btn-success mt-2">Go to Dashboard</a>
  <?php else: ?>
    <a href="login.php" class="btn btn-primary m-2">Login</a>
    <a href="register.php" class="btn btn-outline-primary m-2">Register</a>
  <?php endif; ?>
</div>

<!-- Optional: Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
