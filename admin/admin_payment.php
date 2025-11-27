<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

// Fetch payments with client, booking, and order info
$payments = $conn->query("
    SELECT p.*, c.client_fname, c.client_lname,
           b.booking_date, o.order_item_id, m.menu_name
    FROM payment p
    JOIN client c ON p.client_id = c.client_id
    LEFT JOIN booking b ON p.booking_id = b.booking_id
    LEFT JOIN order_items o ON p.order_item_id = o.order_item_id
    LEFT JOIN menu m ON o.menu_id = m.menu_id
    ORDER BY p.payment_id ASC
");
?>

<div class="container mt-5">
    <h2>Payments</h2>
    <a href="payment_add.php" class="btn btn-success mb-3">Add Payment</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Payment Type</th>
                <th>Booking / Order</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>Reference</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($p = $payments->fetch_assoc()): ?>
            <tr>
                <td><?= $p['payment_id'] ?></td>
                <td><?= $p['client_fname'].' '.$p['client_lname'] ?></td>
                <td><?= $p['payment_type'] ?></td>
                <td>
                    <?php 
                        if($p['payment_type']=='Booking') echo 'Booking #'.$p['booking_id'].' ('.$p['booking_date'].')';
                        else echo 'Order #'.$p['order_item_id'].' - '.$p['menu_name'];
                    ?>
                </td>
                <td>₱<?= number_format($p['payment_amount'],2) ?></td>
                <td><?= $p['payment_method'] ?></td>
                <td><?= $p['payment_status'] ?></td>
                <td><?= $p['payment_date'] ?></td>
                <td><?= $p['payment_reference'] ?></td>
                <td>
                    <a href="payment_edit.php?id=<?= $p['payment_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="payment_delete.php?id=<?= $p['payment_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this payment?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
