<?php
require_once 'admin_authorization.php';
require_login();
require '../includes/db.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT menu_image FROM menu WHERE menu_id = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch();

if ($menu) {
    if (!empty($menu['menu_image']) && file_exists("../" . $menu['menu_image'])) {
        unlink("../" . $menu['menu_image']);  
    }
}

$stmt = $pdo->prepare("DELETE FROM menu WHERE menu_id = ?");
$stmt->execute([$id]);

header("Location: menu.php?deleted=1");
exit;
?>
