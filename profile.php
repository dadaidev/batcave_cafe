<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['client_id'])) {
    header("Location: customer_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Fetch client info
$stmt_user = $conn->prepare("SELECT * FROM client WHERE client_id = ?");
$stmt_user->bind_param("i", $client_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Fetch bookings
$stmt_booking = $conn->prepare("
    SELECT b.*, t.table_name, t.table_number
    FROM booking b
    LEFT JOIN table_seats t ON b.table_id = t.table_id
    WHERE b.client_id = ?
    ORDER BY b.booking_date DESC, b.booking_id DESC
");
$stmt_booking->bind_param("i", $client_id);
$stmt_booking->execute();
$bookings = $stmt_booking->get_result();

// Fetch client order history
$stmt_history = $conn->prepare("
    SELECT oi.*, m.menu_name, m.menu_image, m.menu_category, p.payment_date
    FROM order_items oi
    LEFT JOIN menu m ON oi.menu_id = m.menu_id
    LEFT JOIN payment p ON p.order_item_id = oi.order_item_id
    WHERE p.client_id = ?
    ORDER BY oi.order_item_id DESC
");
$stmt_history->bind_param("i", $client_id);
$stmt_history->execute();
$order_history = $stmt_history->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Bat Café</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f7f7f7; }
.section-title { margin-top: 40px; margin-bottom: 20px; font-weight: 600; }
.booking-row, .orders-row { background: #fff; padding: 15px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.booking-item, .order-item { border: 1px solid #dee2e6; padding: 10px; margin-bottom: 10px; border-radius: 5px; background: #f8f9fa; }
.order-item img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; }
.badge-status { font-size: 0.85em; }
</style>
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

    <h2 class="section-title">My Profile</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <p><strong>Full Name:</strong> <?= $user['client_fname'] . " " . $user['client_mname'] . " " . $user['client_lname']; ?></p>
            <p><strong>Username:</strong> <?= $user['client_username']; ?></p>
            <p><strong>Email:</strong> <?= $user['client_emailaddress']; ?></p>
            <p><strong>Contact Number:</strong> <?= $user['client_contactnumber']; ?></p>
        </div>
    </div>

    <h3 class="section-title">My Bookings</h3>
    <?php if ($bookings->num_rows > 0): ?>
        <?php while ($booking = $bookings->fetch_assoc()): ?>
            <div class="row booking-row">
                <div class="col-md-3"><strong>Booking #</strong><?= $booking['booking_id']; ?></div>
                <div class="col-md-2">
                    <strong>Status:</strong>
                    <span class="badge <?= $booking['booking_status']=='Pending'?'bg-warning':($booking['booking_status']=='Approved'?'bg-success':'bg-secondary') ?> badge-status">
                        <?= $booking['booking_status'] ?>
                    </span>
                </div>
                <div class="col-md-2"><strong>Date:</strong> <?= $booking['booking_date']; ?></div>
                <div class="col-md-2"><strong>Time:</strong> <?= $booking['booking_start_time'] ?> - <?= $booking['booking_end_time'] ?></div>
                <div class="col-md-1"><strong>Pax:</strong> <?= $booking['booking_pax']; ?></div>
                <div class="col-md-2"><strong>Table:</strong> <?= $booking['table_name'] . " (Table " . $booking['table_number'] . ")"; ?></div>

                <div class="col-12 mt-2">
                    <?php
                    $stmt_items = $conn->prepare("
                        SELECT oi.*, m.menu_name
                        FROM order_items oi
                        LEFT JOIN menu m ON oi.menu_id = m.menu_id
                        WHERE oi.booking_id = ?
                    ");
                    $stmt_items->bind_param("i", $booking['booking_id']);
                    $stmt_items->execute();
                    $items = $stmt_items->get_result();
                    ?>
                    <?php if ($items->num_rows > 0): ?>
                        <div class="row">
                            <?php while ($item = $items->fetch_assoc()): ?>
                                <div class="col-md-3 booking-item">
                                    <strong><?= $item['menu_name']; ?></strong><br>
                                    Qty: <?= $item['order_quantity']; ?><br>
                                    Price: ₱<?= number_format($item['order_amount'] / $item['order_quantity'], 2); ?><br>
                                    Total: ₱<?= number_format($item['order_amount'], 2); ?><br>
                                    <small>Special: <?= $item['special_request'] ?: 'None'; ?></small>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <small>No order items for this booking.</small>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>

    <h3 class="section-title">Previous Orders</h3>
    <?php if ($order_history->num_rows > 0): ?>
        <div class="row orders-row">
            <?php while ($row = $order_history->fetch_assoc()): ?>
                <div class="col-md-3 order-item">
                    <img src="<?= $row['menu_image'] ?>" alt="<?= htmlspecialchars($row['menu_name']) ?>">
                    <strong><?= $row['menu_name'] ?></strong><br>
                    <small class="text-muted"><?= $row['menu_category'] ?></small><br>
                    Qty: <?= $row['order_quantity'] ?><br>
                    Price: ₱<?= number_format($row['order_amount'] / $row['order_quantity'], 2) ?><br>
                    Total: ₱<?= number_format($row['order_amount'], 2) ?><br>
                    <small>Ordered on: <?= $row['payment_date'] ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No previous orders found.</p>
    <?php endif; ?>

</div>

</body>
</html>
