<?php
require_once 'admin_authorization.php';
require_login();
$title='admin_client';
require 'admin_header.php';

// Fetch all menu
$stmt = $pdo->query("SELECT * FROM menu ORDER BY menu_id ASC");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bat Café - Menu List</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
<style>
.menu-container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 0 15px;
}
.menu-title {
    margin-bottom: 20px;
}
.table-menu {
    width: 100%;
    border-collapse: collapse;
}
.table-menu th, .table-menu td {
    text-align: center;
    vertical-align: middle;
    padding: 10px;
}
.table-menu img.menu-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}
.btn-md {
    margin-bottom: 15px;
}
@media (max-width: 768px) {
    .table-menu th, .table-menu td {
        font-size: 12px;
        padding: 5px;
    }
    .table-menu img.menu-image {
        width: 40px;
        height: 40px;
    }
}
</style>
</head>
<body>

<div class="menu-container">
    <h1 class="menu-title">TMBCC Menu Lists</h1>
    <a href="menu_add.php" class="btn btn-success btn-md">Add Menu</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-menu">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($menuItems as $item): ?>
                <tr>
                    <td><?= $item['menu_id'] ?></td>
                    <td><?= htmlspecialchars($item['menu_name']) ?></td>
                    <td>
                        <?php if($item['menu_image']): ?>
                        <img src="../<?= htmlspecialchars($item['menu_image']) ?>" class="menu-image">
                        <?php else: ?>
                        <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['menu_category']) ?></td>
                    <td>₱<?= number_format($item['menu_price'],2) ?></td>
                    <td><?= htmlspecialchars($item['menu_description']) ?></td>
                    <td>
                        <a href="menu_edit.php?id=<?= $item['menu_id'] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
                        <a href="menu_delete.php?id=<?= $item['menu_id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this menu item?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
