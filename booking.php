<?php
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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">

<style>
.equipment-box {
    border: 1px solid #555;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    display: flex;
    gap: 10px;
    background: #222;
}
.equipment-box img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}
.equipment-list {
    max-height: 250px;
    overflow-y: auto;
    margin-bottom: 10px;
    padding-right: 5px;
}
</style>
</head>
<body>
<?php include("includes/navbar.php"); ?>

<div class="booking-banner" id="menu">
    <div id="carouselExampleDark" class="carousel carousel-dark slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/menu_1.jpg" class="d-block w-100" alt="">
            </div>
            <div class="carousel-item">
                <img src="images/menu_2.jpg" class="d-block w-100" alt="">
            </div>
            <div class="carousel-item">
                <img src="images/menu_3.jpg" class="d-block w-100" alt="">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<div class="seating-container">
    <h3 class="title-available">Available Tables</h3>
    <div class="table-grid">
        <?php
        $tables = $conn->query("SELECT * FROM table_seats ORDER BY table_number ASC");
        if($tables->num_rows > 0){
            while($table = $tables->fetch_assoc()){
                $statusClass = $table['status'] === 'Available' ? 'available' : 'reserved';
                echo '
                <div class="table-card '.$statusClass.'">
                    <img src="'.$table['table_image'].'" alt="Table '.$table['table_number'].'">
                    <h3>'.$table['table_name'].'</h3>
                    <p>Pax: '.$table['capacity'].'</p>
                    <p>Price: ₱'.$table['table_price'].'/hour</p>
                    <p>Status: '.$table['status'].'</p>';
                if($table['status'] === 'Available'){
                    echo '<button class="book-btn" data-bs-toggle="modal" data-bs-target="#bookingModal" data-tableid="'.$table['table_id'].'" data-price="'.$table['table_price'].'">Book Now</button>';
                } else {
                    echo '<button class="book-btn" disabled>Not Available</button>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>No tables found.</p>';
        }
        ?>
    </div>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Book a Table</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form class="booking-form" action="payment_booking.php" method="POST">
                    <input type="hidden" name="client_id" value="1">
                    <input type="hidden" name="table_id" id="booking_table_id">
                    <input type="hidden" name="total_hours" id="total_hours">
                    <input type="hidden" name="total_price" id="total_price">

                    <label>Description:</label>
                    <input type="text" name="booking_description" required>

                    <label>Date:</label>
                    <input type="date" name="booking_date" min="<?php echo date('Y-m-d'); ?>" required>

                    <label>Start Time:</label>
                    <input type="time" name="booking_start_time" id="booking_start_time" required>

                    <label>End Time:</label>
                    <input type="time" name="booking_end_time" id="booking_end_time" required>

                    <label>Pax:</label>
                    <input type="number" name="booking_pax" min="1" required>

                    <h5 class="mt-3">Add Equipment</h5>
                    <div class="equipment-list">
                        <?php
                        $equipment = $conn->query("SELECT * FROM equipment ORDER BY equipment_name ASC");
                        if ($equipment->num_rows > 0) {
                            while ($eq = $equipment->fetch_assoc()) {
                                echo '
                                <label class="equipment-box">
                                    <input type="checkbox" class="equipment-checkbox" 
                                        name="equipment_ids[]" 
                                        value="'.$eq['equipment_id'].'" 
                                        data-price="'.$eq['equipment_price'].'">

                                    <img src="'.$eq['equipment_image'].'" alt="'.$eq['equipment_name'].'">

                                    <div>
                                        <strong>'.$eq['equipment_name'].'</strong><br>
                                        <small>₱'.$eq['equipment_price'].'</small>
                                    </div>
                                </label>';
                            }
                        } else {
                            echo "<p>No equipment available.</p>";
                        }
                        ?>
                    </div>

                    <p>Total Hours: <span id="display_hours">0</span></p>
                    <p>Total Price: ₱<span id="display_price">0</span></p>

                    <button type="submit" class="btn btn-submit">Submit Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="timeAlertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Invalid Booking Time</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Your selected time is invalid. Booking must be between 12:00 PM and 12:00 AM, with minimum 2 hours.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<script>
const bookingButtons = document.querySelectorAll('.book-btn');
const bookingTableInput = document.getElementById('booking_table_id');
const totalHoursInput = document.getElementById('total_hours');
const totalPriceInput = document.getElementById('total_price');
const displayHours = document.getElementById('display_hours');
const displayPrice = document.getElementById('display_price');

let baseTablePrice = 0;

bookingButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const tableId = btn.getAttribute('data-tableid');
        baseTablePrice = parseFloat(btn.getAttribute('data-price'));
        bookingTableInput.value = tableId;

        const startInput = document.getElementById('booking_start_time');
        const endInput = document.getElementById('booking_end_time');

        function calculatePrice() {
            const start = startInput.value;
            const end = endInput.value;
            if (!start || !end) return;

            const [startH, startM] = start.split(':').map(Number);
            const [endH, endM] = end.split(':').map(Number);
            const startMinutes = startH * 60 + startM;
            const endMinutes = endH * 60 + endM;

            const minMinutes = 12 * 60;
            const maxMinutes = 24 * 60;

            if (startMinutes < minMinutes || endMinutes > maxMinutes || endMinutes <= startMinutes) {
                const timeAlertModal = new bootstrap.Modal(document.getElementById('timeAlertModal'));
                timeAlertModal.show();
                return;
            }

            let hours = (endMinutes - startMinutes) / 60;

            let perHourPrice = baseTablePrice;
            if (hours < 2) perHourPrice += 25;

            let total = hours * perHourPrice;

            document.querySelectorAll(".equipment-checkbox:checked").forEach(eq => {
                total += parseFloat(eq.dataset.price);
            });

            totalHoursInput.value = hours.toFixed(2);
            totalPriceInput.value = total.toFixed(2);
            displayHours.textContent = hours.toFixed(2);
            displayPrice.textContent = total.toFixed(2);
        }

        document.querySelectorAll(".equipment-checkbox").forEach(eq => {
            eq.addEventListener("change", calculatePrice);
        });

        startInput.addEventListener('change', calculatePrice);
        endInput.addEventListener('change', calculatePrice);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
