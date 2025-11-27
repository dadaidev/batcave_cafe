<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM table_seats WHERE table_id = ?");
    $stmt->execute([$id]);
}
header("Location: admin_tables.php");
exit;
