<?php
session_start();
if (!isset($_SESSION['donor_logged_in']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../../login.php");
    exit();
}
?>
