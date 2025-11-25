<?php
require_once 'admin_authorization.php';
require_login();
require '../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id) die("Invalid ID");

$stmt = $pdo->prepare("DELETE FROM client WHERE client_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);

header("Location: admin_client.php?deleted=1");
exit;
?>
