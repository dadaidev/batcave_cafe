<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

$id = $_GET['id'] ?? null;
if(!$id) die("Order ID missing.");

$message = "";

// Fetch order
$order = $conn->query("SELECT * FROM order_items WHERE order_item_id=$id")->fetch_assoc();
if(!$order) die("Order not found.");

// Fetch clients, bookings, menus
$clients = $conn->query("SELECT client_id, client_fname, client_lname FROM client ORDER BY client_fname");
$bookings = $conn->query("SELECT booking_id, booking_date FROM booking ORDER BY booking_date");
$menus = $conn->query("SELECT menu_id, menu_name, menu_price FROM menu ORDER BY menu_name");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $client_id = $_POST['client_id'];
    $booking_id = $_POST['booking_id'] ?: null;
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['order_quantity'];
    $special_request = $_POST['special_request'];

    $menu_price = $conn->query("SELECT menu_price FROM menu WHERE menu_id=$menu_id")->fetch_assoc()['menu_price'];
    $total_amount = $menu_price * $quantity;

    $stmt = $conn->prepare("
        UPDATE order_items SET 
            client_id=?, booking_id=?, menu_id=?, order_quantity=?, order_amount=?, special_request=? 
        WHERE order_item_id=?
    ");
    $stmt->bind_param("iiidisi", $client_id, $booking_id, $menu_id, $quantity, $total_amount, $special_request, $id);

    if($stmt->execute()){
        $message = "Order updated successfully!";
        $order = $conn->query("SELECT * FROM order_items WHERE order_item_id=$id")->fetch_assoc();
    } else {
        $message = "Error: ".$conn->error;
    }
}
?>

<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 700px;">
    <h2>Edit Order #<?= $order['order_item_id'] ?></h2>
    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Client</label>
        <select name="client_id" class="form-control mb-3" required>
            <?php while($c = $clients->fetch_assoc()): ?>
                <option value="<?= $c['client_id'] ?>" <?= $c['client_id']==$order['client_id']?'selected':'' ?>>
                    <?= $c['client_fname'].' '.$c['client_lname'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Booking (Optional)</label>
        <select name="booking_id" class="form-control mb-3">
            <option value="">No booking</option>
            <?php while($b = $bookings->fetch_assoc()): ?>
                <option value="<?= $b['booking_id'] ?>" <?= $b['booking_id']==$order['booking_id']?'selected':'' ?>>
                    Booking #<?= $b['booking_id'] ?> (<?= $b['booking_date'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Menu</label>
        <select name="menu_id" class="form-control mb-3" required>
            <?php while($m = $menus->fetch_assoc()): ?>
                <option value="<?= $m['menu_id'] ?>" <?= $m['menu_id']==$order['menu_id']?'selected':'' ?>>
                    <?= $m['menu_name'] ?> (₱<?= number_format($m['menu_price'],2) ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Quantity</label>
        <input type="number" name="order_quantity" class="form-control mb-3" min="1" value="<?= $order['order_quantity'] ?>" required>

        <label>Special Request</label>
        <textarea name="special_request" class="form-control mb-3"><?= $order['special_request'] ?></textarea>

        <button type="submit" class="btn btn-primary">Update Order</button>
        <a href="admin_order.php" class="btn btn-secondary">Back</a>
    </form>
</div>
