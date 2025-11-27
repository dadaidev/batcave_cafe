<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

$message = "";

// Fetch clients, bookings, orders
$clients = $conn->query("SELECT client_id, client_fname, client_lname FROM client ORDER BY client_fname");
$bookings = $conn->query("SELECT booking_id, booking_date, booking_amount FROM booking ORDER BY booking_date");
$orders = $conn->query("
    SELECT o.order_item_id, o.order_amount, m.menu_name
    FROM order_items o
    JOIN menu m ON o.menu_id = m.menu_id
    ORDER BY o.order_item_id
");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $client_id = $_POST['client_id'];
    $payment_type = $_POST['payment_type'];
    $booking_id = $_POST['booking_id'] ?: null;
    $order_item_id = $_POST['order_item_id'] ?: null;
    $payment_amount = $_POST['payment_amount'];
    $payment_method = $_POST['payment_method'];
    $payment_reference = $_POST['payment_reference'];
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("
        INSERT INTO payment
        (client_id, booking_id, order_item_id, payment_type, payment_amount, payment_method, payment_reference, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiisdsss", $client_id, $booking_id, $order_item_id, $payment_type, $payment_amount, $payment_method, $payment_reference, $payment_status);

    if($stmt->execute()){
        $message = "Payment added successfully!";
    } else {
        $message = "Error: ".$conn->error;
    }
}
?>

<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 700px;">
    <h2>Add Payment</h2>
    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Client</label>
        <select name="client_id" class="form-control mb-3" required>
            <option value="">Select client</option>
            <?php while($c = $clients->fetch_assoc()): ?>
                <option value="<?= $c['client_id'] ?>"><?= $c['client_fname'].' '.$c['client_lname'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Payment Type</label>
        <select name="payment_type" class="form-control mb-3" required onchange="toggleFields(this.value)">
            <option value="Booking">Booking</option>
            <option value="Order">Order</option>
        </select>

        <div id="booking_field">
            <label>Booking</label>
            <select name="booking_id" class="form-control mb-3">
                <option value="">Select booking</option>
                <?php while($b = $bookings->fetch_assoc()): ?>
                    <option value="<?= $b['booking_id'] ?>">Booking #<?= $b['booking_id'] ?> (₱<?= number_format($b['booking_amount'],2) ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>

        <div id="order_field" style="display:none;">
            <label>Order</label>
            <select name="order_item_id" class="form-control mb-3">
                <option value="">Select order</option>
                <?php while($o = $orders->fetch_assoc()): ?>
                    <option value="<?= $o['order_item_id'] ?>">Order #<?= $o['order_item_id'] ?> - <?= $o['menu_name'] ?> (₱<?= number_format($o['order_amount'],2) ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>

        <label>Payment Amount</label>
        <input type="number" name="payment_amount" step="0.01" class="form-control mb-3" required>

        <label>Payment Method</label>
        <select name="payment_method" class="form-control mb-3" required>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="GCash">GCash</option>
            <option value="Online">Online</option>
        </select>

        <label>Payment Reference</label>
        <input type="text" name="payment_reference" class="form-control mb-3">

        <label>Status</label>
        <select name="payment_status" class="form-control mb-3" required>
            <option value="Pending">Pending</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <button type="submit" class="btn btn-primary">Add Payment</button>
        <a href="admin_payment.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<script>
function toggleFields(type){
    if(type==='Booking'){
        document.getElementById('booking_field').style.display='block';
        document.getElementById('order_field').style.display='none';
    } else {
        document.getElementById('booking_field').style.display='none';
        document.getElementById('order_field').style.display='block';
    }
}
</script>
