<?php
session_start();
include("includes/db.php");

// Fetch menu items from DB
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);

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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">   
</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="order-container">
        <div class="row">
            <div class="col-6">
            <img src="<?php echo $menu['menu_image']; ?>">
            </div>
            <div class="col-6">
            <h3 class="menu-name"><?php echo $menu['menu_name']; ?></h3>
            <p class="menu-description"><?php echo $menu['menu_description']; ?></p>
            <p class="menu-price">₱<?php echo number_format($menu['menu_price'], 2); ?></p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>
</html>