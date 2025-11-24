<?php
session_start();
include("includes/db.php");
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

    <div class="container">
        <div class="column card">
            <div id="carouselExampleFade" class="carousel slide carousel-fade">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/slideshow1.jpg" alt="">
                    </div>
                    <div class="carousel-item">
                        <img src="images/slideshow2.jpg" alt="">
                    </div>
                    <div class="carousel-item">
                        <img src="images/slideshow3.jpg" alt="">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade"
                    data-bs-slide="prev">
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade"
                    data-bs-slide="next">
                </button>
            </div>
        </div>

        <div class="column card form-card">
            <h1 class="role">Are you a..</h1>
            <div class="role-btn">
                <a href="admin/admin_login.php"><button class="btn-admin" type="button">Admin</button></a>
                <a href="customer_login.php"><button class="btn-customer" type="button">Customer</button></a>
            </div>
            <p class="intro">Welcome to Batcave Cafe</p>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>
</body>

</html>