<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require 'admin_header.php';
require_login();

// Fetch the orders ito
$sql = "SELECT oi.*, m.menu_name, m.menu_category, b.booking_date, b.booking_start_time, c.client_fname, c.client_lname
        FROM order_items oi
        LEFT JOIN menu m ON oi.menu_id = m.menu_id
        LEFT JOIN booking b ON oi.booking_id = b.booking_id
        LEFT JOIN client c ON b.client_id = c.client_id
        ORDER BY oi.order_item_id ASC";

$orders = $pdo->query($sql)->fetchAll();
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">
</head>
<body>

<div class="order-container">
    <h1 class="order-title">TMBCC Orders</h1>
    <a href="order_add.php" class="btn btn-success btn-md">Add Orders</a>

    <table class="table-order">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Booking (ID / Date)</th>
          <th>Client</th>
          <th>Menu Item</th>
          <th>Category</th>
          <th>Qty</th>
          <th>Price (each)</th>
          <th>Line Total</th>
          <th>Temperature</th>
          <th>Special Request</th>
          <th width="150">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $grandTotal = 0; foreach($orders as $o): 
          $lineTotal = floatval($o['price']) * intval($o['quantity']);
          $grandTotal += $lineTotal;
        ?>
        <tr>
          <td><?= $o['order_item_id'] ?></td>
          <td>
            <?php if($o['booking_id']): ?>
              <?= $o['booking_id'] ?> / <?= htmlspecialchars($o['booking_date']) ?> <?= htmlspecialchars($o['booking_start_time']) ?>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($o['client_fname'].' '.$o['client_lname']) ?></td>
          <td><?= htmlspecialchars($o['menu_name']) ?></td>
          <td><?= htmlspecialchars($o['menu_category']) ?></td>
          <td><?= intval($o['quantity']) ?></td>
          <td>₱<?= number_format($o['price'],2) ?></td>
          <td>₱<?= number_format($lineTotal,2) ?></td>
          <td><?= htmlspecialchars($o['temperature']) ?></td>
          <td><?= htmlspecialchars($o['special_request']) ?></td>
          <td>
            <a href="order_edit.php?id=<?= $o['order_item_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="order_delete.php?id=<?= $o['order_item_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order item?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="7" class="text-end">Grand Total:</th>
          <th>₱<?= number_format($grandTotal,2) ?></th>
          <th colspan="3"></th>
        </tr>
      </tfoot>
    </table>
  </div>
</body>
</html>
