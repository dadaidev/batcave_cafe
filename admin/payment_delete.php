<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");

$id = $_GET['id'] ?? null;
if(!$id) die("Payment ID missing.");

// Delete payment
$conn->query("DELETE FROM payment WHERE payment_id=$id");

header("Location: admin_payment.php");
exit;
