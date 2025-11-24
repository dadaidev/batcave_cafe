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
$stmt_history->bind_param("i", $_SESSION['client_id']);
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
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container profile-container">
    <h2 class="mb-3">My Profile</h2>

    <!-- Client Info -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Client Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Full Name:</strong> <?= $user['client_fname'] . " " . $user['client_mname'] . " " . $user['client_lname']; ?></p>
            <p><strong>Username:</strong> <?= $user['client_username']; ?></p>
            <p><strong>Email:</strong> <?= $user['client_emailaddress']; ?></p>
            <p><strong>Contact Number:</strong> <?= $user['client_contactnumber']; ?></p>
        </div>
    </div>

    <!-- Booking History -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">My Bookings</h5>
        </div>
        <div class="card-body">
            <?php if ($bookings->num_rows > 0): ?>
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-card">
                        <h5>Booking #<?= $booking['booking_id']; ?></h5>
                        <p><strong>Status:</strong> 
                            <span class="badge 
                                <?= $booking['booking_status'] == 'Pending' ? 'bg-warning' : 
                                   ($booking['booking_status'] == 'Approved' ? 'bg-success' : 'bg-secondary'); ?>">
                                <?= $booking['booking_status']; ?>
                            </span>
                        </p>
                        <p><strong>Date:</strong> <?= $booking['booking_date']; ?></p>
                        <p><strong>Time:</strong> <?= $booking['booking_time']; ?></p>
                        <p><strong>Pax:</strong> <?= $booking['booking_pax']; ?></p>
                        <p><strong>Table:</strong> <?= $booking['table_name'] . " (Table " . $booking['table_number'] . ")"; ?></p>

                        <hr>

                        <!-- Order Items -->
                        <h6>Order Items</h6>
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
                            <table class="table table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Menu</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Special Request</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($item = $items->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $item['menu_name']; ?></td>
                                        <td><?= $item['quantity']; ?></td>
                                        <td>₱<?= number_format($item['price'], 2); ?></td>
                                        <td>₱<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td><?= $item['special_request'] ?: 'None'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">No order items for this booking.</p>
                        <?php endif; ?>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No bookings found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Previous Orders Grid -->
    <div class="container mt-5">
        <h3 class="mb-3">Your Previous Orders</h3>

        <?php if ($order_history->num_rows > 0): ?>
            <div class="orders-grid">
                <?php while ($row = $order_history->fetch_assoc()): ?>
                    <div class="order-card">
                        <img src="<?= $row['menu_image'] ?>" 
                             alt="<?= htmlspecialchars($row['menu_name']) ?>">

                        <h5><?= $row['menu_name'] ?></h5>
                        <p class="text-muted"><?= $row['menu_category'] ?></p>
                        <p>Qty: <strong><?= $row['quantity'] ?></strong></p>
                        <p>Price: ₱<?= number_format($row['price'],2) ?></p>
                        <p><strong>Total:</strong> ₱<?= number_format($row['price'] * $row['quantity'],2) ?></p>
                        <small>Ordered on: <?= $row['payment_date'] ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No orders yet.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
