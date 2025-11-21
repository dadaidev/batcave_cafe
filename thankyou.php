<?php
if (!isset($_GET['payment_id'])) {
    die("Invalid access.");
}
$payment_id = intval($_GET['payment_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="thankyou-box">
    <div class="check">✔</div>
    <h2>Payment Successful</h2>
    <p>Your payment has been processed.</p>
    <p><strong>Payment ID:</strong> <?php echo $payment_id; ?></p>

    <a href="menu.php" class="btn btn-main">Back to Menu</a>
</div>
    <?php include("includes/footer.php"); ?>


</body>
</html>
