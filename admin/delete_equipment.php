<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

if (!isset($_GET['id'])) die("No ID");

$id = intval($_GET['id']);

// Delete equipment
$stmt = $pdo->prepare("DELETE FROM equipment WHERE equipment_id=?");
$stmt->execute([$id]);

header("Location: admin_equipment.php");
exit;
