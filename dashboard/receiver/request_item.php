<?php
session_start();
require_once '../../includes/database.php';

if (!isset($_SESSION['receiver_logged_in']) || $_SESSION['role'] !== 'receiver') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_item'])) {
    $donation_id = $_POST['donation_id'];
    $receiver_id = $_POST['receiver_id'];

    // Prevent duplicate request
    $check = $conn->prepare("SELECT id FROM requests WHERE donation_id = ? AND receiver_id = ? AND status = 'pending'");
    $check->bind_param("ii", $donation_id, $receiver_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('You have already requested this item. Please wait for admin approval.'); window.location.href='receiver.php';</script>";
        exit;
    }

    // Insert new request
    $stmt = $conn->prepare("INSERT INTO requests (donation_id, receiver_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $donation_id, $receiver_id);

    if ($stmt->execute()) {
        echo "<script>alert('Request submitted successfully! Awaiting admin approval.'); window.location.href='receiver.php';</script>";
    } else {
        echo "<script>alert('Error submitting request. Please try again.'); window.location.href='receiver.php';</script>";
    }
} else {
    header("Location: receiver.php");
    exit();
}
