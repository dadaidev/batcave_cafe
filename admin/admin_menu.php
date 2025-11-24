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


<div class="menu-container">
    <h1 class="menu-title">TMBCC Menu Lists</h1>
    <a href="menu_add.php" class="btn btn-success btn-md">Add Menu</a>

    <table class="table-menu">
        <thead>
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
                <td><?= $item['menu_name'] ?></td>
                <td><img src="../images/<?= htmlspecialchars($item['menu_image']) ?>" class="menu-image"></td>
                <td><?= $item['menu_category'] ?></td>
                <td>₱<?= number_format($item['menu_price'],2) ?></td>
                <td><?= $item['menu_description'] ?></td>
                <td>
                    <a href="menu_edit.php?id=<?= $item['menu_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="menu_delete.php?id=<?= $item['menu_id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this menu item?')">Delete</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</div>

</body>
</html>