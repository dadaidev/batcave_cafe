<?php
require_once 'admin_config.php';
require_once 'admin_authorization.php';
    require 'admin_header.php';

require_login();

if (!isset($_GET['id'])) {
    die("Menu ID not specified.");
}

$menu_id = intval($_GET['id']);

// Fetch the menu item
$stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    die("Menu item not found.");
}

// Initialize variables safely
$menu_name = $menu['menu_name'] ?? '';
$menu_description = $menu['menu_description'] ?? '';
$menu_category = $menu['menu_category'] ?? '';
$menu_price = $menu['menu_price'] ?? '';
$menu_image = $menu['menu_image'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_name = $_POST['menu_name'] ?? '';
    $menu_description = $_POST['menu_description'] ?? '';
    $menu_category = $_POST['menu_category'] ?? '';
    $menu_price = $_POST['menu_price'] ?? 0;

    // Handle image upload
    if (!empty($_FILES['menu_image']['name'])) {
        $target_dir = "../uploads/";
        $filename = basename($_FILES["menu_image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["menu_image"]["tmp_name"], $target_file)) {
            $menu_image = "uploads/" . $filename;
        }
    }

    // Update database
    $update = $pdo->prepare("UPDATE menu SET menu_name=?, menu_description=?, menu_category=?, menu_price=?, menu_image=? WHERE menu_id=?");
    $update->execute([$menu_name, $menu_description, $menu_category, $menu_price, $menu_image, $menu_id]);

    header("Location: admin_menu.php");
    exit;
}
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Bat Café</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Edit Menu Item</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Menu Name</label>
            <input type="text" name="menu_name" class="form-control" value="<?= htmlspecialchars($menu_name) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="menu_description" class="form-control"><?= htmlspecialchars($menu_description) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="menu_category" class="form-control" value="<?= htmlspecialchars($menu_category) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="menu_price" class="form-control" value="<?= htmlspecialchars($menu_price) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Current Image</label><br>
            <?php if ($menu_image): ?>
                <img src="../<?= htmlspecialchars($menu_image) ?>" width="100" style="border-radius:6px;"><br><br>
            <?php else: ?>
                <span>No image uploaded</span><br><br>
            <?php endif; ?>
            <label class="form-label">Change Image</label>
            <input type="file" name="menu_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success btn-md">Update</button>
        <a href="admin_menu.php" class="btn btn-secondary btn-md">Back to Menu List</a>
    </form>
</div>

</body>
</html>
