<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

if (!isset($_GET['id'])) die("No ID");

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM equipment WHERE equipment_id=?");
$stmt->execute([$id]);
$equipment = $stmt->fetch();
if (!$equipment) die("Equipment not found");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['equipment_name'];
    $category = $_POST['equipment_category'];
    $price    = $_POST['equipment_price'];
    $desc     = $_POST['equipment_description'];

    $imageName = $equipment['equipment_image'];
    if (!empty($_FILES['equipment_image']['name'])) {
        $imageName = "uploads/equipment_" . time() . "_" . basename($_FILES['equipment_image']['name']);
        move_uploaded_file($_FILES['equipment_image']['tmp_name'], "../" . $imageName);
    }

    $stmt = $pdo->prepare("UPDATE equipment SET equipment_name=?, equipment_category=?, equipment_price=?, equipment_description=?, equipment_image=? WHERE equipment_id=?");
    $stmt->execute([$name, $category, $price, $desc, $imageName, $id]);

    header("Location: admin_equipment.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Equipment</title></head>
<body>
<div class="container mt-5">
<h2>Edit Equipment</h2>
<p><?= $message ?></p>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="equipment_name" value="<?= htmlspecialchars($equipment['equipment_name']) ?>" required><br><br>
    <input type="text" name="equipment_category" value="<?= htmlspecialchars($equipment['equipment_category']) ?>" required><br><br>
    <input type="number" step="0.01" name="equipment_price" value="<?= $equipment['equipment_price'] ?>" required><br><br>
    <textarea name="equipment_description" required><?= htmlspecialchars($equipment['equipment_description']) ?></textarea><br><br>
    <?php if ($equipment['equipment_image']): ?>
        <img src="../<?= $equipment['equipment_image'] ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="equipment_image"><br><br>
    <button type="submit">Update Equipment</button>
</form>
</div>
</body>
</html>
