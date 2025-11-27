<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");

if (!isset($_GET['id'])) {
    die("Booking ID missing.");
}

$id = intval($_GET['id']);

// Delete equipment first (foreign key requirement)
$conn->query("DELETE FROM booking_equipment WHERE booking_id = $id");

// Delete booking
if ($conn->query("DELETE FROM booking WHERE booking_id = $id")) {
    header("Location: booking_list.php?msg=deleted");
    exit;
} else {
    echo "Error deleting booking: " . $conn->error;
}
?>
