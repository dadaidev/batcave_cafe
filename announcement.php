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
    <section class="announcements" id="announcement">
        <div class="announcements-container">
            <h2 class="section-title">From the Cave:Announcements </h2>
            <p class="section-subtitle">
                Stay updated with the latest happenings, seasonal brews, and special offers at The Bat Cave Café.
            </p>

            <div class="announcement-grid">
                <div class="announcement-card">
                    <div class="announcement-image">
                        <img src="images/announcement_1.jpg" alt="Midnight Jazz Night">
                    </div>
                    <div class="announcement-content">
                        <h3>Midnight Jazz Night </h3>
                        <p>Join us every Friday for live jazz performances and signature night brews. Experience the
                            rhythm of the night with smooth melodies and aromatic blends.</p>
                        <span class="announcement-date">Every Friday | 8:00 PM - 12:00 AM</span>
                    </div>
                </div>

                <div class="announcement-card">
                    <div class="announcement-image">
                        <img src="images/announcement_2.jpg" alt="Halloween Coffee Festival">
                    </div>
                    <div class="announcement-content">
                        <h3>Halloween Coffee Festival </h3>
                        <p>Celebrate spooky season with our limited-edition Ghoul's Potion and Dark Choco Witch Brew.
                            Costumes are encouraged — free treat for the best outfit!</p>
                        <span class="announcement-date">October 28–31, 2025</span>
                    </div>
                </div>

                <div class="announcement-card">
                    <div class="announcement-image">
                        <img src="images/announcement_3.jpg" alt="New Menu Launch">
                    </div>
                    <div class="announcement-content">
                        <h3>New Menu Launch </h3>
                        <p>We’re brewing something special! Get ready for our new selection of moonlit desserts and
                            hand-crafted cold brews — coming this November.</p>
                        <span class="announcement-date">Launching November 20, 2025</span>
                    </div>
    </section>
    <?php include("includes/footer.php"); ?>
</body>

</html>