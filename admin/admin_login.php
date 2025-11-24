<?php
require_once 'admin_authorization.php'; //checker ito

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Default admin 
    if ($username === 'admin' && $password === 'password123') {
        $_SESSION['admin_id'] = 0;
        $_SESSION['admin_name'] = 'Bat Café Admin';
        $_SESSION['admin_role'] = 'admin';
        header('Location: admin_dashboard.php');
        exit;
    }

    // Check database for admin credentials
    require_once 'db.php'; // PDO connection sa config

    $stmt = $pdo->prepare("SELECT client_id, client_password, client_role, client_fname, client_lname 
                           FROM client 
                           WHERE client_username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user['client_role'] === 'admin' && password_verify($password, $user['client_password'])) {
        $_SESSION['admin_id'] = $user['client_id'];
        $_SESSION['admin_name'] = $user['client_fname'] . ' ' . $user['client_lname'];
        $_SESSION['admin_role'] = $user['client_role'];
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $errors[] = 'Invalid credentials or not an admin.';
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-card-body text-center">
            <img src="images/logo.jpg" alt="Bat Café Logo" class="login-logo">
            <h4 class="login-title">Admin Login</h4>

            <?php if (!empty($errors)): ?>
                <div class="alert-danger">
                    <?= implode('<br>', $errors) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input name="username" class="form-input" placeholder="Username" required>
                <input name="password" type="password" class="form-input" placeholder="Password" required>
                <button type="submit" class="btn-login">Login</button>
            </form>

            <p class="text-muted">Use your admin credentials</p>
        </div>
    </div>
</div>

</body>
</html>
