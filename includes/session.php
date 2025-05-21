<?php 
session_start();

// Prevent session fixation attacks
session_regenerate_id(true);

// Allow access to register and login without authentication
$currentPage = basename($_SERVER['PHP_SELF']);
if (in_array($currentPage, ['register.php', 'login.php', 'index.php'])) {
    return; 
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Implement session expiry (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset(); // Clear session variables
    session_destroy(); // Destroy session
    header("Location: login.php?session_expired=true");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity timestamp

// Role-Based Redirects (Prevent access to wrong pages)
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin' && $currentPage !== 'admin.php' && strpos($_SERVER['PHP_SELF'], '/admin/') === false) {
        header("Location: dashboard/admin/admin.php");
        exit();
    } elseif ($_SESSION['role'] === 'donor' && $currentPage !== 'donor.php') {
        header("Location: dashboard/donor/donor.php");
        exit();
    } elseif ($_SESSION['role'] === 'receiver' && $currentPage !== 'receiver.php') {
        header("Location: dashboard/receiver/receiver.php");
        exit();
    }
}
?>
