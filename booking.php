<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['client_id'])) {
    header("Location: customer_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Fetch tables
$tables = $conn->query("SELECT * FROM table_seats ORDER BY table_number ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Tables</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Available Tables</h3>
    <div class="row">
        <?php while($table = $tables->fetch_assoc()): ?>
        <div class="col-md-4 mb-3">
            <div class="card bg-dark text-white">
                <img src="<?= $table['table_image'] ?>" class="card-img-top" alt="<?= $table['table_name'] ?>">
                <div class="card-body">
                    <h5><?= $table['table_name'] ?></h5>
                    <p>Pax: <?= $table['capacity'] ?> | Price: ₱<?= $table['table_price'] ?>/hour</p>
                    <?php if($table['status'] === 'Available'): ?>
                        <form action="booking_order.php" method="POST">
                            <input type="hidden" name="table_id" value="<?= $table['table_id'] ?>">
                            <input type="hidden" name="booking_date" value="<?= date('Y-m-d') ?>">
                            <input type="hidden" name="booking_start_time" value="12:00">
                            <input type="hidden" name="booking_end_time" value="14:00">
                            <input type="hidden" name="booking_pax" value="<?= $table['capacity'] ?>">
                            <input type="hidden" name="booking_description" value="Default booking">
                            <button type="submit" class="btn btn-primary w-100">Book Now</button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>Not Available</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
