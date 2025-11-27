<?php
require_once 'admin_authorization.php';
require_login();
require 'admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['client_username'];
    $password = password_hash($_POST['client_password'], PASSWORD_DEFAULT); // hash password
    $email = $_POST['client_emailaddress'];
    $contact = $_POST['client_contactnumber'];
    $fname = $_POST['client_fname'];
    $lname = $_POST['client_lname'];
    $mname = $_POST['client_mname'] ?? '';
    $role = $_POST['client_role'] ?? 'customer';

    $stmt = $pdo->prepare("INSERT INTO client 
        (client_username, client_password, client_emailaddress, client_contactnumber, client_fname, client_lname, client_mname, client_role) 
        VALUES (:username, :password, :email, :contact, :fname, :lname, :mname, :role)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password,
        ':email' => $email,
        ':contact' => $contact,
        ':fname' => $fname,
        ':lname' => $lname,
        ':mname' => $mname,
        ':role' => $role
    ]);

    header('Location: admin_client.php');
    exit;
}
?>

<div class="container mt-5">
    <h1>Add New Client</h1>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="client_username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="client_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="client_fname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Middle Name</label>
            <input type="text" name="client_mname" class="form-control">
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="client_lname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="client_emailaddress" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="client_contactnumber" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="client_role" class="form-select">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Client</button>
        <a href="admin_client.php" class="btn btn-secondary">Back</a>
    </form>
</div>
