<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);
    $status     = $_POST['booking_status'];

    $stmt = $conn->prepare("UPDATE booking SET booking_status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $status, $booking_id);

    if ($stmt->execute()) {
        // Redirect back to booking list
        header("Location: booking_list.php");
        exit();
    } else {
        die("Error updating status: " . $conn->error);
    }
} else {
    die("Invalid request");
}
