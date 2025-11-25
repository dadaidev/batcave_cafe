<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require 'admin_header.php';
require_login();

// Fetch all seats
$stmt = $pdo->query("SELECT * FROM table_seats ORDER BY table_number ASC");
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
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


<div class="seats-container">
    <h1 class="seats-title">TMBCC Tables</h1>
    <a href="seats_add.php" class="btn btn-success btn-md">Add seats</a>

    <table class="table-booking">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Table #</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Image</th>
                <th>Price</th>
                <th width="180">Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($seats as $row): ?>
            <tr>
                <td><?= $row['table_id'] ?></td>
                <td><?= htmlspecialchars($row['table_number']) ?></td>
                <td><?= htmlspecialchars($row['capacity']) ?></td>

                <td>
                    <form action="seats_status_update.php" method="POST">
                        <input type="hidden" name="table_id" value="<?= $row['table_id'] ?>">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="Available" <?= $row['status']=="Available"?"selected":"" ?>>Available</option>
                            <option value="Reserved" <?= $row['status']=="Reserved"?"selected":"" ?>>Reserved</option>
                        </select>
                    </form>
                </td>

                <td>
                    <?php if ($row['table_image']): ?>
                        <img src="../<?= $row['table_image'] ?>" width="80" style="border-radius:6px;">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </td>

                <td>₱<?= number_format($row['table_price'],2) ?></td>

                <td>
                    <a href="admin_table_edit.php?id=<?= $row['table_id'] ?>" class="btn btn-warning btn-sm">Edit</a>

                    <a href="admin_table_delete.php?id=<?= $row['table_id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this table?')">
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
