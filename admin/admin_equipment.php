<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require 'admin_header.php';
require_login();

// Fetch equipment list
$sql = "SELECT * FROM equipment ORDER BY equipment_id ASC";
$items = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café - Equipment Admin</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">TMBCC Equipment Lists</h1>
    <a href="add_equipment.php" class="btn btn-success mb-3">Add Equipment</a>

    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $i): ?>
            <tr class="text-center">
                <td><?= $i['equipment_id'] ?></td>
                <td><?= htmlspecialchars($i['equipment_name']) ?></td>
                <td><?= htmlspecialchars($i['equipment_description']) ?></td>
                <td><?= htmlspecialchars($i['equipment_category']) ?></td>
                <td>₱<?= number_format($i['equipment_price'], 2) ?></td>
                <td>
                    <?php if(!empty($i['equipment_image'])): ?>
                        <img src="../<?= htmlspecialchars($i['equipment_image']) ?>" width="60" style="border-radius:4px;">
                    <?php else: ?>
                        <span class="text-muted">No Image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_equipment.php?id=<?= $i['equipment_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="delete_equipment.php?id=<?= $i['equipment_id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this item?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
