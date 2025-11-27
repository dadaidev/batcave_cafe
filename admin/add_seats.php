<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require 'admin_header.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = $_POST['table_number'];
    $name = $_POST['table_name'];
    $capacity = $_POST['capacity'];
    $available_seats = $_POST['available_seats'];
    $price = $_POST['table_price'];
    $status = $_POST['status'];
    $image = '';

    // Handle image upload
    if (!empty($_FILES['table_image']['name'])) {
        $target_dir = "../images/tables/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image = $target_dir . basename($_FILES['table_image']['name']);
        move_uploaded_file($_FILES['table_image']['tmp_name'], $image);
    }

    $stmt = $pdo->prepare("INSERT INTO table_seats (table_number, table_name, capacity, available_seats, table_price, status, table_image) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$number, $name, $capacity, $available_seats, $price, $status, $image]);
    header("Location: admin_seats.php");
    exit;
}
?>

<div class="container mt-4">
    <h2>Add New Table</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Table Name</label>
            <input type="text" name="table_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="table_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="Available">Available</option>
                <option value="Reserved">Reserved</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="table_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Add Table</button>
        <a href="admin_seats.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
