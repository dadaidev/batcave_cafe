<?php
require_once 'admin_authorization.php';
require_login();
require 'admin_header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_client.php');
    exit;
}

// Fetch client data
$stmt = $pdo->prepare("SELECT * FROM client WHERE client_id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['client_username'];
    $email = $_POST['client_emailaddress'];
    $contact = $_POST['client_contactnumber'];
    $fname = $_POST['client_fname'];
    $lname = $_POST['client_lname'];
    $mname = $_POST['client_mname'] ?? '';
    $role = $_POST['client_role'] ?? 'customer';

    $password = $_POST['client_password'] ?? '';
    if ($password) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE client SET client_username=?, client_password=?, client_emailaddress=?, client_contactnumber=?, client_fname=?, client_lname=?, client_mname=?, client_role=? WHERE client_id=?");
        $stmt->execute([$username, $password, $email, $contact, $fname, $lname, $mname, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE client SET client_username=?, client_emailaddress=?, client_contactnumber=?, client_fname=?, client_lname=?, client_mname=?, client_role=? WHERE client_id=?");
        $stmt->execute([$username, $email, $contact, $fname, $lname, $mname, $role, $id]);
    }

    header('Location: admin_client.php');
    exit;
}
?>

<div class="container mt-5">
    <h1>Edit Client</h1>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="client_username" value="<?= htmlspecialchars($client['client_username']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="client_password" class="form-control">
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="client_fname" value="<?= htmlspecialchars($client['client_fname']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Middle Name</label>
            <input type="text" name="client_mname" value="<?= htmlspecialchars($client['client_mname']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="client_lname" value="<?= htmlspecialchars($client['client_lname']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="client_emailaddress" value="<?= htmlspecialchars($client['client_emailaddress']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="client_contactnumber" value="<?= htmlspecialchars($client['client_contactnumber']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="client_role" class="form-select">
                <option value="customer" <?= $client['client_role']=='customer'?'selected':'' ?>>Customer</option>
                <option value="admin" <?= $client['client_role']=='admin'?'selected':'' ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Client</button>
        <a href="admin_client.php" class="btn btn-secondary">Back</a>
    </form>
</div>
