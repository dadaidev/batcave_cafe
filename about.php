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

    <?php include("includes/navbar.php") ?>

    <div class="about-pic">
        <img src="">
    </div>

    <div class="about-container">
        <div class="row">
            <div class="col-6">
                <div class="about-cafe">
                    <h1 class="about">About</h1>
                    <p class="about-sentence">Our cafe.....</p>
                </div>
            </div>
            <div class="col-6">
                <div class="loc-cafe">
                    <h1 class="loc">Location</h1>
                    <p class="pogi">JAFJFID</p>
                    <img src="">
                </div>
            </div>
        </div>

        <div class="mv-container">
            <div class="row">
                <div class="col-6">
                    <h1 class="mission">Mission</h1>
                    <p class="m-text">TEXTTT</p>
                </div>
                <div class="col-6">
                    <h1 class="vission">Vission</h1>
                    <p class="v-text">TEXTTT</p>
                </div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
</body>

</html>