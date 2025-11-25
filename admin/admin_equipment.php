<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require 'admin_header.php';
require_login();

// Fetch equipment list
$sql = "SELECT * FROM equipment ORDER BY equipment_id ASC";
$items = $pdo->query($sql)->fetchAll();
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


<div class="equipment-container">
    <h1 class="equipment-title">TMBCC Equipment Lists</h1>
    <a href="equipment_add.php" class="btn btn-success btn-md">Add equipment</a>

    <table class="table-equipment">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Image</th>
                <th width="150">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($items as $i): ?>
            <tr>
                <td><?= $i['equipment_id'] ?></td>
                <td><?= htmlspecialchars($i['equipment_name']) ?></td>
                <td><?= htmlspecialchars($i['equipment_description']) ?></td>
                <td><?= htmlspecialchars($i['equipment_category']) ?></td>
                <td>₱<?= number_format($i['equipment_price'], 2) ?></td>
                <td>
                    <?php if($i['equipment_image']): ?>
                        <img src="../<?= $i['equipment_image'] ?>" width="60">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>

                <td>
                    <a href="equipment_edit.php?id=<?= $i['equipment_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="equipment_delete.php?id=<?= $i['equipment_id'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this item?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
