<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");

$id = $_GET['id'] ?? null;
if(!$id) die("Order ID missing.");

// Delete order
$conn->query("DELETE FROM order_items WHERE order_item_id=$id");

header("Location: admin_order.php");
exit;
