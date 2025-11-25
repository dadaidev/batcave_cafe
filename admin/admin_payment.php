<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

// Delete payment
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM payment WHERE payment_id = $id");
    header("Location: admin_payment_list.php");
    exit();
}
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


<div class="payment-container">
    <h1 class="payment-title">TMBCC Payments</h1>
    <a href="payment_add.php" class="btn btn-success btn-md">Add payment</a>

    <table class="table-booking">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Status</th>
            <th>Type</th>
            <th>Date</th>
            <th width="160">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = $conn->query("
            SELECT p.*, c.client_fname AS name
            FROM payment p
            LEFT JOIN client c ON p.client_id = c.client_id
            ORDER BY payment_id ASC
        ");

        while ($row = $query->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['payment_id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td>₱<?= number_format($row['payment_amount'], 2) ?></td>
            <td><?= $row['payment_method'] ?></td>
            <td><?= $row['payment_status'] ?></td>
            <td><?= $row['payment_type'] ?></td>
            <td><?= $row['payment_date'] ?></td>
            <td>
                <a href="admin_payment_edit.php?id=<?= $row['payment_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="admin_payment_list.php?delete=<?= $row['payment_id'] ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete payment?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
