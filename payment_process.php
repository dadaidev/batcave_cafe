<?php
session_start();
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $client_id = $_SESSION['client_id'];  
    $booking_id = $_POST['booking_id'];  
    $amount = $_POST['payment_amount'];
    $method = $_POST['payment_method'];

    // Insert payment into db
    $sql = "INSERT INTO payment (
                payment_date, 
                payment_amount, 
                payment_method, 
                payment_status,
                client_id,
                booking_id,
                payment_type
            ) VALUES (
                NOW(),
                '$amount',
                '$method',
                'Completed',
                '$client_id',
                '$booking_id',
                'Booking'
            )";

    if ($conn->query($sql) === TRUE) {
        // Redirect to thank you page after payment
        header("Location: thankyou.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
