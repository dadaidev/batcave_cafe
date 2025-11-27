<?php
require_once 'admin_authorization.php';
require_login();
$title = 'admin_client';
require 'admin_header.php';

// Pagination setup
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Fetch clients with limit
$stmt = $pdo->prepare("SELECT * FROM client ORDER BY client_id ASC LIMIT :offset, :limit");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total clients for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) FROM client");
$totalClients = (int)$totalStmt->fetchColumn();
$totalPages = ceil($totalClients / $perPage);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bat Café - Users</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">TMBCC Users</h1>
    <a href="client_add.php" class="btn btn-success mb-3">Add User</a>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $c): ?>
                <tr>
                    <td><?= $c['client_id'] ?></td>
                    <td><?= htmlspecialchars($c['client_username']) ?></td>
                    <td><?= htmlspecialchars($c['client_password']) ?></td>
                    <td><?= htmlspecialchars($c['client_fname'] . ' ' . $c['client_lname']) ?></td>
                    <td><?= htmlspecialchars($c['client_emailaddress']) ?></td>
                    <td><?= htmlspecialchars($c['client_contactnumber']) ?></td>
                    <td><?= htmlspecialchars($c['client_role']) ?></td>
                    <td>
                        <a href="client_edit.php?id=<?= $c['client_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="client_delete.php?id=<?= $c['client_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this client?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
