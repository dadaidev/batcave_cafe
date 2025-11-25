<?php
    require_once 'admin_authorization.php';
    require_login();
    require 'admin_header.php';
    require '../includes/db.php';

    $message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name     = $_POST['menu_name'];
        $category = $_POST['menu_category'];
        $price    = $_POST['menu_price'];
        $desc     = $_POST['menu_description'];

        $imageName = "";
        if (! empty($_FILES['menu_image']['name'])) {
            $imageName  = time() . "_" . basename($_FILES['menu_image']['name']);
            $targetPath = "../" . $imageName;

            move_uploaded_file($_FILES['menu_image']['tmp_name'], $targetPath);
        }

        $stmt = $pdo->prepare("INSERT INTO menu (menu_name, menu_category, menu_price, menu_description, menu_image)
                           VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $desc, $imageName]);

        $message = "Menu added successfully!";
    }
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Menu</title>
</head>
<body>

<div class="admin-form">

<h2>Add Menu</h2>
<p><?php echo $message?></p>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="menu_name" placeholder="Name" required class="form-control mb-2"><br>
    <input type="text" name="menu_category" placeholder="Category" required class="form-control mb-2"><br>
    <input type="number" step="0.01" name="menu_price" placeholder="Price" required class="form-control mb-2"><br>
    <textarea name="menu_description" placeholder="Description" required class="form-control mb-2"></textarea><br>

    <input type="file" name="menu_image" required><br><br>

    <button type="submit">Add</button>
</form>
</diV>

</body>
</html>
