<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['table_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE table_seats SET status=? WHERE table_id=?");
    $stmt->execute([$status, $id]);
}

header("Location: admin_tables.php");
exit;
