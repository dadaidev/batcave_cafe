<?php
require_once 'admin_authorization.php';
require_login();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM client WHERE client_id=?");
    $stmt->execute([$id]);
}

header('Location: admin_client.php');
exit;
