<?php
require_once 'admin_authorization.php';
require_login();

$title = 'Dashboard';
require 'admin_header.php';

// count queries
$counts = [];
$counts['clients'] = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();
$counts['tables']  = $pdo->query("SELECT COUNT(*) FROM table_seats")->fetchColumn();
$counts['order_items'] = $pdo->query("SELECT COUNT(*) FROM order_items")->fetchColumn();
$counts['bookings']= $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
$counts['payments']= $pdo->query("SELECT COUNT(*) FROM payment")->fetchColumn();

// map keys to dashboard links
$cardLinks = [
    'clients'  => 'admin_client.php',
    'tables'   => 'admin_tables.php',
    'order_items'  => 'admin_order.php',
    'bookings' => 'admin_bookings.php',
    'payments' => 'admin_payments.php'
];
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="admin/style.css">
</head>
<body>
    <div class="admin-dashboard">

        <h1 class="dashboard-title">TMBCC Admin Dashboard</h1>

        <div class="dashboard-row">
        <?php foreach($counts as $k=>$v): 
            $link = $cardLinks[$k] ?? '#';
            $label = ucwords(str_replace('_', ' ', $k));
        ?>
            <a href="<?= $link ?>" class="dashboard-card-link">
                <div class="dashboard-card">
                    <div class="dashboard-card-body">
                        <div class="card-label"><?= htmlspecialchars($label) ?></div>
                        <div class="card-value"><?= intval($v) ?></div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

