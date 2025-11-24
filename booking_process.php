<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['client_id'])) {
    header("Location: customer_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client_id = $_SESSION['client_id'];
    $table_id = intval($_POST['table_id']);
    $description = $_POST['booking_description'];
    $date = $_POST['booking_date'];
    $time = $_POST['booking_time'];
    $pax = intval($_POST['booking_pax']);

    $stmt = $conn->prepare("
        INSERT INTO booking 
        (booking_description, booking_date, booking_time, booking_pax, table_id, client_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssiii", $description, $date, $time, $pax, $table_id, $client_id);
    $stmt->execute();

    $booking_id = $stmt->insert_id;

    header("Location: payment_booking.php?booking_id=" . $booking_id);
    exit();
} else {
    header("Location: booking.php");
    exit();
}
?>
