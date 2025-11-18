<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form data
    $booking_description = $_POST['booking_description'];
    $booking_time = $_POST['booking_time'];
    $booking_date = $_POST['booking_date'];
    $booking_pax = $_POST['booking_pax'];
    $booking_tablenum = $_POST['booking_tablenum'];
    $client_id = $_POST['client_id'];  // from session or form

    // Default status
    $booking_status = "Pending";

    // Insert Query
    $sql = "INSERT INTO booking 
            (booking_description, booking_time, booking_date, booking_pax, booking_tablenum, booking_status, client_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssissi",
        $booking_description,
        $booking_time,
        $booking_date,
        $booking_pax,
        $booking_tablenum,
        $booking_status,
        $client_id
    );

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
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

    <!-- BANNER -->
    <div class="booking-banner" id="menu">
        <div id="carouselExampleDark" class="carousel carousel-dark slide">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="10000">
            <img src="images/menu_1.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Brew & Celebrate</h5>
                <p>Host your next celebration here—birthdays, meetings, or special moments made better with great café ambiance </p>
            </div>
            </div>
            <div class="carousel-item" data-bs-interval="2000">
            <img src="images/menu_2.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Gather & Indulge!</h5>
                <p>Perfect for solo visitors—enjoy a quiet corner, a warm cup, and a moment just for yourself.</p>
            </div>
            </div>
            <div class="carousel-item">
            <img src="images/menu_3.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Get a reservation for table!</h5>
                <p>A welcoming spot for friends and family to enjoy delicious food, crafted drinks, and good company.</p>
            </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
            <h3 class="title-available text-align-center">Available table</h3>
            </div>
        </div>
        <div class="row p-5 m-4">
            <div class="col-3">
                <img src="#" class="img-table">
                <h3 class="title-tbl"> Solo Signature </h3>
                <h5 class="title-seats"> Seats: </h5>
                <h5 class="title-price"> Price: ₱ </h5>
                <button class="book-now">Book Now</button>
            </div>
            <div class="col-3">
                <img src="#" class="img-table">
                <h3 class="title-tbl"> Solo Signature </h3>
                <h5 class="title-seats"> Seats:  </h5>
                <h5 class="title-price"> Price: ₱ </h5>
                <button class="book-now">Book Now</button>
            </div>
            <div class="col-3">
                <img src="#" class="img-table">
                <h3 class="title-tbl"> Solo Signature </h3>
                <h5 class="title-seats"> Seats:  </h5>
                <h5 class="title-price"> Price: ₱ </h5>
                <button class="book-now">Book Now</button>
            </div>
            <div class="col-3">
                <img src="#" class="img-table">
                <h3 class="title-tbl"> Solo Signature </h3>
                <h5 class="title-seats"> Seats: </h5>
                <h5 class="title-price"> Price: ₱ </h5>
                <button class="book-now">Book Now</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book a Table</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="booking-form" action="booking_process.php" method="POST">
                        <input type="hidden" name="client_id" value="1">
                        <input type="hidden" name="booking_category" id="booking_category">

                        <label>Description:</label>
                        <input type="text" name="booking_description" required>

                        <label>Date:</label>
                        <input type="date" name="booking_date" required>

                        <label>Time:</label>
                        <input type="time" name="booking_time" required>

                        <label>Pax:</label>
                        <input type="number" name="booking_pax" min="1" required>

                        <label>Table #:</label>
                        <input type="number" name="booking_tablenum" min="1" required>

                        <button type="submit" class="btn btn-submit">Submit Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

    <script>
        const bookingButtons = document.querySelectorAll('.book-btn');
        const bookingCategoryInput = document.getElementById('booking_category');

        bookingButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const category = btn.getAttribute('data-category');
                bookingCategoryInput.value = category;
            });
        });
    </script>

</body>
</html>