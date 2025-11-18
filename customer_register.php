<?php
session_start();
include("includes/db.php");

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $email = trim($_POST["email"]);
    $contact = trim($_POST["contact"]);
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $mname = trim($_POST["mname"]);

    if (empty($username) || empty($password) || empty($email) || empty($contact) || empty($fname) || empty($lname)) {
        $message = "Please fill out all required fields.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } elseif (!preg_match("/^[0-9]{11}$/", $contact)) {
        $message = "Contact number must be 11 digits.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("SELECT client_id FROM client WHERE client_username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username already exists.";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("SELECT client_id FROM client WHERE client_emailaddress = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "Email is already registered.";
                $message_type = "error";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO client (client_username, client_password, client_emailaddress, client_contactnumber, client_fname, client_lname, client_mname, client_role) VALUES (?, ?, ?, ?, ?, ?, ?, 'customer')");
                $stmt->bind_param("sssssss", $username, $hashed_password, $email, $contact, $fname, $lname, $mname);

                if ($stmt->execute()) {
                    $message = "Your account has been created successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error: " . $stmt->error;
                    $message_type = "error";
                }
            }
        }
        $stmt->close();
    }
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café - Register</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include("includes/navbar.php"); ?>


    <div class="container form-container"></div>
    <div class="row">
        <div class="col-12">
            <form action="customer_register.php" method="POST">
                <h3 class="register text-center">Register Account</h3>
                <input type="text" name="username" placeholder="Username">
                <input type="text" name="password" placeholder="Password">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="contact" placeholder="Contact Number (11 digits)" required>
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
                <input type="text" name="mname" placeholder="Middle Name">
                <button type="submit">Register</button>
                <?php if (!empty($message)): ?>
                    <p class="<?= $message_type ?>"><?= $message ?></p>
                <?php endif; ?>

                <p class="p-form">Already have an account? <a href="customer_login.php">Login here</a></p>

        </div>
    </div>

    </div>
    </div>

    <?php include("includes/footer.php"); ?>
</body>

</html>