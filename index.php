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
    <!-- frontview section -->
    <div class="hero" id="frontview">
        <div class="slide3"></div>

        <div class="hero-text">
            <h1>Where Nights Come Alive</h1>
            <h1 class="hero-brand">Welcome to The Bat Cave Café.</h1>
            <p>A cozy hideout serving bold brews and night-inspired flavors.</p>
        </div>
        <div class="hero-buttons">
            <a href="menu.php" class="btn-bat">Explore Menu</a>
            <a href="booking.php" class="btn-bat">Book a Table</a>
        </div>
    </div>

    <!-- announcement -->
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

    <!-- best seellers -->
    <section class="best-sellers" id="best-sellers">
        <div class="best-sellers-container">
            <h2 class="section-title">Sip the Favorites!</h2>
            <p class="section-subtitle">
                Discover our most loved brews and treats that keep our customers coming back.
            </p>

            <div class="sellers-grid">
                <div class="seller-card">
                    <div class="seller-image">
                        <img src="images/best_1.webp" alt="Dark Roast Espresso">
                    </div>
                    <div class="seller-content">
                        <h3>Bat Brew</h3>
                        <p>Rich, bold, and smooth that is the perfect kickstart for any night owl.</p>
                        <span class="seller-price">P 120.00</span>
                        <div class="seller-buttons">
                            <a href="#" class="btn-view">View</a>
                            <a href="#" class="btn-buy">Buy Now</a>
                        </div>
                    </div>
                </div>

                <div class="seller-card">
                    <div class="seller-image">
                        <img src="images/best_2.webp" alt="Bat Cave Croissant">
                    </div>
                    <div class="seller-content">
                        <h3>Bat Cave Croissant</h3>
                        <p>Flaky, buttery croissant filled with dark chocolate — perfect for a midnight snack.</p>
                        <span class="seller-price">P 80.00</span>
                        <div class="seller-buttons">
                            <a href="#" class="btn-view">View </a>
                            <a href="#" class="btn-buy">Buy Now</a>
                        </div>
                    </div>
                </div>

                <div class="seller-card">
                    <div class="seller-image">
                        <img src="images/best_3.jpg" alt="Tuna Pasta">
                    </div>
                    <div class="seller-content">
                        <h3>Tuna Pasta</h3>
                        <p>Creamy pasta tossed with tender tuna flakes, fresh herbs, and a hint of garlic.</p>
                        <span class="seller-price">P 150.00</span>
                        <div class="seller-buttons">
                            <a href="#" class="btn-view">View</a>
                            <a href="#" class="btn-buy">Buy Now</a>
                        </div>
                    </div>
                </div>

                <div class="seller-card">
                    <div class="seller-image">
                        <img src="images/best_4.jpg" alt="Chocolate Frappe">
                    </div>
                    <div class="seller-content">
                        <h3>Chocolate Frappe</h3>
                        <p>A rich, chilled blend of chocolate, milk, and ice, topped with whipped cream.</p>
                        <span class="seller-price">P 130.00</span>
                        <div class="seller-buttons">
                            <a href="#" class="btn-view">View</a>
                            <a href="#" class="btn-buy">Buy Now</a>
                        </div>
                    </div>
                </div>
    </section>

    <!-- reviews -->
    <section class="customer-reviews" id="reviews">
    <div class="reviews-container">
        <h2 class="section-title">Sips & Opinions</h2>
        <p class="section-subtitle">
            See why coffee lovers and night owls choose The Bat Cave Café for their cozy nights.
        </p>

        <div class="reviews-grid">
            <div class="review-card">
                <div class="reviewer-photo">
                    <img src="images/review_1.jpg" alt="Jane Doe">
                </div>
                <div class="review-content">
                    <h3>Mingyu Batumbakal</h3>
                    <div class="review-rating">
                        ★★★★☆
                    </div>
                    <p>
                        “Super sarap ng kanilang soda drinks!! Nagustuhan din ng aking hubby na si Wonu! #Babalikbalikan!”
                    </p>
                </div>
            </div>

            <div class="review-card">
                <div class="reviewer-photo">
                    <img src="images/review_2.jpg" alt="Mark Smith">
                </div>
                <div class="review-content">
                    <h3>Dokyeom Manalo</h3>
                    <div class="review-rating">
                        ★★★★★
                    </div>
                    <p>
                        “Thank you so much sa mga staffs! Very affordable and quality lahat ng drinks.”
                    </p>
                </div>
            </div>

            <div class="review-card">
                <div class="reviewer-photo">
                    <img src="images/review_3.jpg" alt="Emily Johnson">
                </div>
                <div class="review-content">
                    <h3>Nayeon Dela Cruz</h3>
                    <div class="review-rating">
                        ★★★★☆
                    </div>
                    <p>
                        “Very worth it ang mga foods!”
                    </p>
                </div>
            </div>

            <div class="review-card">
                <div class="reviewer-photo">
                    <img src="images/review_4.jpg" alt="David Lee">
                </div>
                <div class="review-content">
                    <h3>Maloi Cole</h3>
                    <div class="review-rating">
                        ★★★★★
                    </div>
                    <p>
                        “Pet friendly!! Yes!!! Punta na kayo here!”
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


    <?php include("includes/footer.php"); ?>

    <!--etong mga script na ito ay para sa animations -->
    <script>
        const items = document.querySelectorAll('.menu-item'); //animation para magpop 

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => { //function ito
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        items.forEach(item => observer.observe(item));
    </script>

    <script>
        const announcementCards = document.querySelectorAll('.announcement-card'); //animation din ito para mag pop
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => { //function again
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        announcementCards.forEach(card => observer.observe(card));
    </script>

</body>

</html>