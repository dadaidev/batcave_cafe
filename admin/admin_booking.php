<?php
    require_once __DIR__ . '/admin_config.php';
    require_once __DIR__ . '/admin_authorization.php';
    require 'admin_header.php';
    require_login();

    // Fetch all bookings
    $sql = "
        SELECT b.*,
            c.client_fname, c.client_lname,
            t.table_name
        FROM booking b
        LEFT JOIN client c ON b.client_id = c.client_id
        LEFT JOIN table_seats t ON b.table_id = t.table_id
        ORDER BY b.booking_id ASC
    ";

    $bookings = $pdo->query($sql)->fetchAll();
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


<div class="booking-container">
    <h1 class="booking-title">TMBCC Bookings</h1>
    <a href="booking_add.php" class="btn btn-success btn-md">Add Bookings</a>

    <table class="table-booking">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
                <th>Pax</th>
                <th>Table</th>
                <th>Status</th>
                <th width="150">Actions</th>
            </tr>
        </thead>

<tbody>
    <?php foreach ($bookings as $b): ?>
    <tr>
        <td><?php echo $b['booking_id']?></td>

        <td><?php echo htmlspecialchars($b['client_fname'] . ' ' . $b['client_lname'])?></td>

        <td><?php echo htmlspecialchars($b['booking_description'])?></td>

        <td><?php echo htmlspecialchars($b['booking_date'])?></td>

        <td><?php echo htmlspecialchars($b['booking_time'])?></td>

        <td><?php echo intval($b['booking_pax'])?></td>

        <td><?php echo htmlspecialchars($b['table_name'])?></td>

        <td>
            <form action="booking_status_update.php" method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']?>">
                <select name="booking_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="Pending"   <?php echo $b['booking_status'] == 'Pending' ? 'selected' : ''?>>Pending</option>
                    <option value="Confirmed" <?php echo $b['booking_status'] == 'Confirmed' ? 'selected' : ''?>>Confirmed</option>
                    <option value="Cancelled" <?php echo $b['booking_status'] == 'Cancelled' ? 'selected' : ''?>>Cancelled</option>
                    <option value="Completed" <?php echo $b['booking_status'] == 'Completed' ? 'selected' : ''?>>Completed</option>
                </select>
            </form>
        </td>

        <td>
            <a href="booking_edit.php?id=<?php echo $b['booking_id']?>" class="btn btn-sm btn-primary">Edit</a>

            <a href="booking_delete.php?id=<?php echo $b['booking_id']?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this booking?')">
                Delete
            </a>
        </td>

    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
</div>
</body>
</html>