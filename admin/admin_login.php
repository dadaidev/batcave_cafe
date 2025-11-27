<?php
    session_start();
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === 'admin' && $password === 'password123') {
            $_SESSION['admin_id']   = 0;
            $_SESSION['admin_name'] = 'Bat Café Admin';
            $_SESSION['admin_role'] = 'admin';
            header('Location: admin_dashboard.php');
            exit;
        }

        $stmt = $pdo->prepare("SELECT client_id, client_password, client_role, client_fname, client_lname
                           FROM client
                           WHERE client_username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['client_role'] === 'admin' && password_verify($password, $user['client_password'])) {
            $_SESSION['admin_id']   = $user['client_id'];
            $_SESSION['admin_name'] = $user['client_fname'] . ' ' . $user['client_lname'];
            $_SESSION['admin_role'] = $user['client_role'];
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid credentials or not an admin.';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bat Café Admin Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<style>
    .login-container {
  margin: 50px auto;
  padding: 50px 20px;
  display: flex;
  flex-wrap: wrap;
  align-items: stretch;
  justify-content: center;
  gap: 50px;
}

.form-card-customer {
  text-align: center;
  padding: 30px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  margin: 0px auto;
}

.header-login {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  gap: 10px;
}

.header-login img {
  width: 80px;
  height: auto;
}

.Intro-head {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
  color: #2b0d0c;
}

.Intro-login {
  font-size: 35px;
  color: #2b0d0c;
  margin: 30px 0 10px 0;
  text-align: center;
}

.new-login-form {
  margin-top: 30px;
  text-align: center;
}

.new-input-group {
  position: relative;
  margin-bottom: 20px;
}

.new-input-group input {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.btn-admin {
  padding: 15px 30px;
  font-size: 20px;
  border: none;
  cursor: pointer;
  background-color: #2b0d0c;
  color: #D3DAD9;
  transition: transform 0.2s ease;
}

.btn-admin:hover {
  background-color: #715A5A;
  color: #1a0606;
}

.alert {
  padding: 10px;
  color: #842029;
  background-color: #f8d7da;
  border-radius: 6px;
  margin-bottom: 15px;
}

.short-desc {
  font-size: 16px;
  color: #5a4c47;
  margin-top: 15px;
}

</style>
<body>

<div class="login-container">
    <div class="form-card-customer">
        <div class="header-login">
            <img src="../images/logo.png" alt="Bat Café Logo">
            <h3 class="Intro-head">Admin Panel</h3>
        </div>
        <h2 class="Intro-login">Welcome Back Admin!</h2>

        <?php if (! empty($errors)): ?>
            <div class="alert alert-danger">
                <?php echo implode('<br>', $errors)?>
            </div>
        <?php endif; ?>

        <form method="post" class="new-login-form">
            <div class="new-input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="new-input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-admin">Login</button>
        </form>

        <p class="short-desc">Use your admin credentials</p>
    </div>
</div>

</body>
</html>
