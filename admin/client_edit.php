<?php
require_once 'admin_authorization.php';
require_login();
require 'admin_header.php';
require '../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id) die("Invalid ID");

$stmt = $pdo->prepare("SELECT * FROM client WHERE client_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$client = $stmt->fetch();

if (!$client) die("Client not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        UPDATE client SET
            client_username = :u,
            client_password = :p,
            client_fname = :fn,
            client_lname = :ln,
            client_emailaddress = :e,
            client_contactnumber = :c,
            client_role = :r
        WHERE client_id = :id
    ");

    $stmt->execute([
        ':u' => $_POST['username'],
        ':p' => $_POST['password'],
        ':fn' => $_POST['fname'],
        ':ln' => $_POST['lname'],
        ':e' => $_POST['email'],
        ':c' => $_POST['contact'],
        ':r' => $_POST['role'],
        ':id' => $id
    ]);

    header("Location: admin_client.php?updated=1");
    exit;
}
?>

<div class="admin-form">
<h2>Edit Client</h2>
<form method="POST">
    <input type="text" name="username" value="<?= htmlspecialchars($client['client_username']) ?>" class="form-control mb-2" required>
    <input type="text" name="password" value="<?= htmlspecialchars($client['client_password']) ?>" class="form-control mb-2" required>
    <input type="text" name="fname" value="<?= htmlspecialchars($client['client_fname']) ?>" class="form-control mb-2" required>
    <input type="text" name="lname" value="<?= htmlspecialchars($client['client_lname']) ?>" class="form-control mb-2" required>
    <input type="email" name="email" value="<?= htmlspecialchars($client['client_emailaddress']) ?>" class="form-control mb-2" required>
    <input type="text" name="contact" value="<?= htmlspecialchars($client['client_contactnumber']) ?>" class="form-control mb-2" required>

    <select name="role" class="form-control mb-2">
        <option value="user" <?= $client['client_role'] === 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $client['client_role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button class="btn btn-primary">Save Changes</button>
</form>
</div>