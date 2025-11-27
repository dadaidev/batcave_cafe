<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
require 'admin_header.php';
require '../includes/db.php';

if (!isset($_GET['id'])) die("No table ID.");

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM table_seats WHERE table_id = ?");
$stmt->execute([$id]);
$table = $stmt->fetch();

if (!$table) die("Table not found.");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = $_POST['table_number'];
    $capacity = $_POST['capacity'];
    $price = $_POST['table_price'];
    $status = $_POST['status'];

    $imageName = $table['table_image'];
    if (!empty($_FILES['table_image']['name'])) {
        $uploadDir = "../images/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $imageName = "table_" . time() . "_" . basename($_FILES['table_image']['name']);
        $targetPath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['table_image']['tmp_name'], $targetPath);
    }

    $stmt = $pdo->prepare("UPDATE table_seats SET table_number=?, capacity=?, table_price=?, status=?, table_image=? WHERE table_id=?");
    $stmt->execute([$number, $capacity, $price, $status, $imageName, $id]);
    header("Location: admin_seats.php");
    exit;
}
?>

<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 600px;">
    <h2>Edit Table</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Table #</label>
        <input type="text" name="table_number" class="form-control mb-3" value="<?= $table['table_number'] ?>" required>
        <label>Capacity</label>
        <input type="number" name="capacity" class="form-control mb-3" value="<?= $table['capacity'] ?>" required>
        <label>Price</label>
        <input type="number" step="0.01" name="table_price" class="form-control mb-3" value="<?= $table['table_price'] ?>" required>
        <label>Status</label>
        <select name="status" class="form-select mb-3" required>
            <option value="Available" <?= $table['status']=="Available"?"selected":"" ?>>Available</option>
            <option value="Reserved" <?= $table['status']=="Reserved"?"selected":"" ?>>Reserved</option>
        </select>
        <label>Image</label>
        <input type="file" name="table_image" class="form-control mb-3">
        <?php if($table['table_image']): ?>
            <img src="../<?= $table['table_image'] ?>" width="100">
        <?php endif; ?>
        <button class="btn btn-primary">Update Table</button>
        <a href="admin_seats.php" class="btn btn-secondary">Back</a>
    </form>
</div>
