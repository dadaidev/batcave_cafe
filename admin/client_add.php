<?php
require_once 'admin_authorization.php';
require_login();
require 'admin_header.php';
require '../includes/db.php';  
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO client (client_username, client_password, client_fname, client_lname, client_emailaddress, client_contactnumber, client_role)
        VALUES (:u, :p, :fn, :ln, :e, :c, :r)
    ");

    $stmt->execute([
        ':u' => $_POST['username'],
        ':p' => $_POST['password'],  
        ':fn' => $_POST['fname'],
        ':ln' => $_POST['lname'],
        ':e' => $_POST['email'],
        ':c' => $_POST['contact'],
        ':r' => $_POST['role'],
    ]);

    header("Location: admin_client.php?added=1");
    exit;
}
?>



<div class="admin-form">
    <h2>Add Client</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required class="form-control mb-2">
        <input type="text" name="password" placeholder="Password" required class="form-control mb-2">
        <input type="text" name="fname" placeholder="First Name" required class="form-control mb-2">
        <input type="text" name="lname" placeholder="Last Name" required class="form-control mb-2">
        <input type="email" name="email" placeholder="Email" required class="form-control mb-2">
        <input type="text" name="contact" placeholder="Contact" required class="form-control mb-2">

        <select name="role" class="form-control mb-2" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button class="btn btn-success">Add Client</button>
    </form>
</div>