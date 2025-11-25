<?php
    require_once 'admin_authorization.php';
    require_login();
    require 'admin_header.php';
    require '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch();

if (!$menu) {
    die("Menu not found.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['menu_name'];
    $category = $_POST['menu_category'];
    $price = $_POST['menu_price'];
    $desc = $_POST['menu_description'];

    $imageName = $menu['menu_image'];

    if (!empty($_FILES['menu_image']['name'])) {

        if (!empty($menu['menu_image']) && file_exists("../images/" . $menu['menu_image'])) {
            unlink("../" . $menu['menu_image']);
        }

        $imageName = time() . "_" . basename($_FILES['menu_image']['name']);
        $targetPath = "../images/" . $imageName;

        move_uploaded_file($_FILES['menu_image']['tmp_name'], $targetPath);
    }

    $stmt = $pdo->prepare("UPDATE menu 
                           SET menu_name=?, menu_category=?, menu_price=?, menu_description=?, menu_image=? 
                           WHERE menu_id=?");

    $stmt->execute([$name, $category, $price, $desc, $imageName, $id]);

    $message = "Menu updated!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Menu</title>
</head>
<body>

<div class="admin-form">
<h2>Edit Menu</h2>
<p><?= $message ?></p>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="menu_name" value=" <?= $menu['menu_name'] ?>" required class="form-control mb-2"><br>
    <input type="text" name="menu_category" value="<?= $menu['menu_category'] ?>" required class="form-control mb-2"><br>
    <input type="number" step="0.01" name="menu_price" value="<?= $menu['menu_price'] ?>" required class="form-control mb-2"><br>
    <textarea required class="form-control mb-2 name="menu_description"><?= $menu['menu_description'] ?></textarea><br>

    <p>Current Image:</p>
    <img src="../<?= $menu['menu_image'] ?>" width="120"><br><br>

    <input type="file" name="menu_image"><br><br>

    <button type="submit">Update</button>
</form>
</div>

</body>
</html>
