<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../../includes/database.php';
include '../../includes/notifications.php';

$user_id = $_SESSION['user_id'] ?? null;
$notifications = $user_id ? getNotifications($user_id) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SAHYOG</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('../assets/watermark-bg.png') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar-brand img {
  height: 80px;         /* Increase logo height */
  object-fit: contain;  /* Keep aspect ratio */
  margin-top: -15px;    /* Pull it upward */
  margin-bottom: -15px; /* Pull it downward */
}
    .dropdown-menu.scrollable-notifications {
      max-height: 300px;
      overflow-y: auto;
      width: 350px;
    }
    .dropdown-item.small {
      font-size: 13px;
      white-space: normal;
      word-wrap: break-word;
    }
    .logo{
      height: 100px;
      
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
  <a class="navbar-brand" href="#"><img src="../../assets/logo.png" alt="SAHYOG Logo" class="logo"></a>
  <div class="ms-auto d-flex align-items-center">
    <?php if ($user_id): ?>
    <!-- 🔔 Notification dropdown -->
    <div class="dropdown me-3">
      <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
        🔔 Notifications
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow scrollable-notifications">
        <?php if (empty($notifications)): ?>
          <li><span class="dropdown-item text-muted">No new notifications</span></li>
        <?php else: ?>
          <?php foreach ($notifications as $note): ?>
            <li><span class="dropdown-item small"><?php echo htmlspecialchars($note['message']); ?></span></li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
    <?php endif; ?>
    <a href="../../index.php" class="btn btn-primary mb-2">Home</a>
  </div>
</nav>
