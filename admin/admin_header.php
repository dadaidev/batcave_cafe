<?php
require_once 'admin_authorization.php';
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">
</head>
<body>

    <div class="admin-header">
    <div class="logo">
        <img src="../images/logo.png">
        <h2>The Malvar Bat Cave Café</h2>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_client.php">Clients</a></li>
            <li><a href="admin_menu.php">Menu</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </div>

</body>
</html> 
