<?php
  require_once 'admin_authorization.php';
  require_login();
  $title='admin_client';
  require 'admin_header.php';

  // pagination simple
  $page = max(1, (int)($_GET['page'] ?? 1));
  $perPage = 20;
  $offset = ($page-1)*$perPage;

  $stmt = $pdo->prepare("SELECT * FROM client ORDER BY client_id ASC LIMIT :offset, :limit");
  $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
  $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
  $stmt->execute();
  $clients = $stmt->fetchAll();
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


<div class="page-container">
    <h1 class="page-title">TMBCC Clients</h1>
    <a href="client_add.php" class="btn btn-success btn-md">Add Client</a>

    <table class="table-client">
        <thead>
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
            <?php foreach($clients as $c): ?>
                <tr>
                    <td><?= $c['client_id'] ?></td>
                    <td><?= htmlspecialchars($c['client_username']) ?></td>
                    <td><?= htmlspecialchars($c['client_password']) ?></td>
                    <td><?= htmlspecialchars($c['client_fname'].' '.$c['client_lname']) ?></td>
                    <td><?= htmlspecialchars($c['client_emailaddress']) ?></td>
                    <td><?= htmlspecialchars($c['client_contactnumber']) ?></td>
                    <td><?= htmlspecialchars($c['client_role']) ?></td>
                    <td>
                        <a class="btn btn-primary btn-sm" href="client_edit.php?id=<?= $c['client_id'] ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="client_delete.php?id=<?= $c['client_id'] ?>" onclick="return confirm('Delete this client?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>