<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

// Fetch orders with client, booking (optional), and menu info
$orders = $conn->query("
    SELECT o.*, c.client_fname, c.client_lname, 
           b.booking_date, m.menu_name
    FROM order_items o
    JOIN client c ON o.client_id = c.client_id
    LEFT JOIN booking b ON o.booking_id = b.booking_id
    JOIN menu m ON o.menu_id = m.menu_id
    ORDER BY o.order_item_id ASC
");
?>

<div class="container mt-5">
    <h2>Orders</h2>
    <a href="order_add.php" class="btn btn-success mb-3">Add Order</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Booking Date</th>
                <th>Menu</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Order Date</th>
                <th>Special Request</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($o = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= $o['order_item_id'] ?></td>
                <td><?= $o['client_fname'].' '.$o['client_lname'] ?></td>
                <td><?= $o['booking_date'] ?? '-' ?></td>
                <td><?= $o['menu_name'] ?></td>
                <td><?= $o['order_quantity'] ?></td>
                <td>₱<?= number_format($o['order_amount'],2) ?></td>
                <td><?= $o['order_date'] ?></td>
                <td><?= $o['special_request'] ?></td>
                <td>
                    <a href="order_edit.php?id=<?= $o['order_item_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="order_delete.php?id=<?= $o['order_item_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
