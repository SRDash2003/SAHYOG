<?php
session_start();
if (!isset($_SESSION['receiver_logged_in']) || $_SESSION['role'] !== 'receiver') {
    header("Location: ../../login.php");
    exit();
}
?>
