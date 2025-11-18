<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("includes/db.php");

$message = '';
$message_type = '';

// Redirect if already logged in
if (isset($_SESSION['client_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'customer') {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
        $message_type = 'error';
    } else {
        $stmt = $conn->prepare("SELECT client_id, client_username, client_password, client_role, client_emailaddress FROM client WHERE client_username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['client_password'])) {
                $_SESSION['client_id'] = $user['client_id'];
                $_SESSION['username'] = $user['client_username'];
                $_SESSION['role'] = $user['client_role'];
                $_SESSION['email'] = $user['client_emailaddress'];

                $_SESSION['last_activity'] = time();
                $_SESSION['expire_time'] = 600;

                header("Location: index.php");
                exit();
            } else {
                $message = "Invalid username or password.";
                $message_type = 'error';
            }
        } else {
            $message = "Invalid username or password.";
            $message_type = 'error';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café - Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include("includes/navbar.php"); ?>

    <div class="login-container main-container clogin">
        <!-- Form Column -->
        <div class="row">
            <div class="col-6">
                <img src="images/frontview.jpg" class="image-front m-5 p-5">
            </div>
            <div class="col-6">
                <div class="column form-card-customer">
                    <div class="header-login">
                        <img src="images/logo.png" alt="Bat Café Logo">
                        <h2 class="Intro-head">The Malvar Bat Cave Café</h2>
                    </div>

                    <h1 class="Intro-login">Log In to Your Account</h1>
                    <p class="short-desc">Welcome back!</p>

                    <form action="customer_login.php" method="POST" class="new-login-form">
                        <div class="new-input-group">
                            <input type="text" name="username" placeholder="Username" class="new-input-field" required>
                        </div>

                        <div class="new-input-group">
                            <input type="password" name="password" placeholder="Password" class="new-input-field"
                                required>
                        </div>

                        <button type="submit" class="new-login-btn">Log In</button>

                        <div class="new-separator">Don't have an account?</div>

                        <a href="customer_register.php" class="new-register-btn">Register</a>

                        <?php if (!empty($message)): ?>
                            <p class="<?= $message_type === 'error' ? 'error' : 'success' ?>"><?= $message ?></p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <?php include("includes/footer.php"); ?>
</body>

</html>