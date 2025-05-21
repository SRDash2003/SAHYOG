<?php
session_start();

// SESSION TIMEOUT: Set to 30 minutes (1800 seconds)
$timeout = 3600;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../../login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>
