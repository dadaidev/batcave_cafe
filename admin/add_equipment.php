<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
require 'admin_header.php';
require '../includes/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = $_POST['equipment_name'];
    $category = $_POST['equipment_category'];
    $price    = $_POST['equipment_price'];
    $desc     = $_POST['equipment_description'];

    $imageName = "";

    if (!empty($_FILES['equipment_image']['name'])) {
        $uploadDir = "../images/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName  = "equipment_" . time() . "_" . basename($_FILES['equipment_image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['equipment_image']['tmp_name'], $targetPath)) {
            $message = "Failed to upload image.";
        }
    }

    if (!$message) {
        $stmt = $pdo->prepare("INSERT INTO equipment (equipment_name, equipment_category, equipment_price, equipment_description, equipment_image)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $desc, $imageName]);
        $message = "Equipment added successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Equipment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 600px;">
    <h2>Add Equipment</h2>

    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Equipment Name</label>
            <input type="text" name="equipment_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="equipment_category" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="equipment_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="equipment_description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="equipment_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Add Equipment</button>
        <a href="admin_equipment.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
