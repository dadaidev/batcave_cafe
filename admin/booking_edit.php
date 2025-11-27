<?php
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include("../includes/db.php");
require 'admin_header.php';

$message = "";
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if(!$id){
    die("Booking ID missing.");
}

// Fetch booking
$booking = $conn->query("SELECT * FROM booking WHERE booking_id=$id")->fetch_assoc();
if(!$booking){
    die("Booking not found.");
}

// Fetch clients
$clients = $conn->query("SELECT client_id, client_fname, client_lname FROM client ORDER BY client_fname");

// Fetch tables
$tables = $conn->query("SELECT * FROM table_seats ORDER BY table_name");

// Fetch all equipment
$equipment = $conn->query("SELECT * FROM equipment ORDER BY equipment_name ASC");

// Fetch booked equipment
$booked_equipment = [];
$res = $conn->query("SELECT equipment_id FROM booking_equipment WHERE booking_id=$id");
while($row = $res->fetch_assoc()) $booked_equipment[] = (int)$row['equipment_id'];

// Pre-calc formatted hours for display (from stored decimal)
$initial_hours = (float)$booking['booking_hours'];
$init_hrs = floor($initial_hours);
$init_mins = round(($initial_hours - $init_hrs) * 60);
if ($init_mins === 60) { $init_hrs += 1; $init_mins = 0; }
$initial_formatted_hours = sprintf("%02d:%02d", $init_hrs, $init_mins);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $client      = (int)$_POST['client_id'];
    $table       = (int)$_POST['table_id'];
    $date        = $conn->real_escape_string($_POST['booking_date']);
    $startTime   = $conn->real_escape_string($_POST['booking_start_time']);
    $endTime     = $conn->real_escape_string($_POST['booking_end_time']);
    $pax         = (int)$_POST['booking_pax'];
    $desc        = $conn->real_escape_string($_POST['booking_description']);
    $equipment_ids = isset($_POST['equipment_ids']) ? array_map('intval', $_POST['equipment_ids']) : [];
    $status      = $conn->real_escape_string($_POST['booking_status']);

    // Check availability
    $check_avail_row = $conn->query("
        SELECT t.capacity, (t.capacity - IFNULL(SUM(b.booking_pax),0)) AS available_seats
        FROM table_seats t
        LEFT JOIN booking b 
            ON t.table_id = b.table_id 
            AND b.booking_date = '$date' 
            AND b.booking_id != $id
            AND ((b.booking_start_time < '$endTime' AND b.booking_end_time > '$startTime')) 
            AND b.booking_status IN ('Pending','Confirmed')
        WHERE t.table_id = $table
        GROUP BY t.table_id
    ")->fetch_assoc();

    if (!$check_avail_row) {
        $cap_row = $conn->query("SELECT capacity FROM table_seats WHERE table_id = $table")->fetch_assoc();
        $check_avail_row = [
            'capacity' => (int)$cap_row['capacity'],
            'available_seats' => (int)$cap_row['capacity']
        ];
    } else {
        $check_avail_row['available_seats'] = (int)$check_avail_row['available_seats'];
    }

    if($pax > $check_avail_row['available_seats']){
        $message = "Cannot update. Only ".$check_avail_row['available_seats']." seats are available for this table/time.";
    } else {
        // Hours (decimal)
        $start_sec = strtotime($startTime);
        $end_sec   = strtotime($endTime);
        if($end_sec <= $start_sec){
            $message = "End time must be after start time.";
        } else {
            $total_hours = round(($end_sec - $start_sec)/3600, 2);

            // Convert to HH:MM Format for display
            $hrs = floor($total_hours);
            $mins = round(($total_hours - $hrs) * 60);
            if ($mins === 60) { $hrs += 1; $mins = 0; }
            $formatted_hours = sprintf("%02d:%02d", $hrs, $mins);

            // Base price
            $table_price = (float)$conn->query("SELECT table_price FROM table_seats WHERE table_id=$table")->fetch_assoc()['table_price'];
            if($total_hours < 2){
                $booking_amount = $total_hours * ($table_price + 25);
            } else {
                $booking_amount = $total_hours * $table_price;
            }

            // Equipment price
            if(count($equipment_ids) > 0){
                $ids = implode(',', $equipment_ids);
                $equip_total_row = $conn->query("SELECT SUM(equipment_price) as total FROM equipment WHERE equipment_id IN ($ids)")->fetch_assoc();
                $equip_total = (float)($equip_total_row['total'] ?? 0);
                $booking_amount += $equip_total;
            }

            // Update booking
            $stmt = $conn->prepare("
                UPDATE booking SET 
                    client_id=?, table_id=?, booking_date=?, booking_start_time=?, booking_end_time=?,
                    booking_hours=?, booking_pax=?, booking_description=?, booking_status=?, booking_amount=?
                WHERE booking_id=?
            ");
            // types: i i s s s d i s s d i
            $stmt->bind_param("iisssdissdi", $client, $table, $date, $startTime, $endTime, $total_hours, $pax, $desc, $status, $booking_amount, $id);

            if($stmt->execute()){
                // Update equipment
                $conn->query("DELETE FROM booking_equipment WHERE booking_id=$id");
                foreach($equipment_ids as $eq_id){
                    $conn->query("INSERT INTO booking_equipment (booking_id, equipment_id) VALUES ($id,".(int)$eq_id.")");
                }
                $message = "Booking updated successfully!";
                $booking = $conn->query("SELECT * FROM booking WHERE booking_id=$id")->fetch_assoc();
                $booked_equipment = $equipment_ids;
                $initial_formatted_hours = $formatted_hours;
            } else {
                $message = "Error: ".$conn->error;
            }
        }
    }
}
?>

<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 700px;">
    <h2>Edit Booking #<?= (int)$booking['booking_id'] ?></h2>
    <?php if($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">

        <label>Client</label>
        <select name="client_id" class="form-control mb-3" required>
            <?php while($c = $clients->fetch_assoc()): ?>
                <option value="<?= (int)$c['client_id'] ?>" <?= $c['client_id']==$booking['client_id']?'selected':'' ?>>
                    <?= htmlspecialchars($c['client_fname'].' '.$c['client_lname']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Table</label>
        <select name="table_id" class="form-control mb-3" required>
            <?php while($t = $tables->fetch_assoc()): ?>
                <option value="<?= (int)$t['table_id'] ?>" <?= $t['table_id']==$booking['table_id']?'selected':'' ?>>
                    <?= htmlspecialchars($t['table_name']) ?> (<?= (int)$t['capacity'] ?> pax)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Date</label>
        <input type="date" name="booking_date" class="form-control mb-3" value="<?= htmlspecialchars($booking['booking_date']) ?>" min="<?= date('Y-m-d') ?>" required>

        <label>Start Time</label>
        <input type="time" name="booking_start_time" class="form-control mb-3" value="<?= htmlspecialchars($booking['booking_start_time']) ?>" min="13:00" max="22:00" required>

        <label>End Time</label>
        <input type="time" name="booking_end_time" class="form-control mb-3" value="<?= htmlspecialchars($booking['booking_end_time']) ?>" min="13:00" max="22:00" required>

        <label>Pax</label>
        <input type="number" name="booking_pax" class="form-control mb-3" min="1" value="<?= htmlspecialchars($booking['booking_pax']) ?>" required>

        <label>Description</label>
        <textarea name="booking_description" class="form-control mb-3"><?= htmlspecialchars($booking['booking_description']) ?></textarea>

        <label>Status</label>
        <select name="booking_status" class="form-control mb-3">
            <option value="Pending" <?= $booking['booking_status']=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Confirmed" <?= $booking['booking_status']=='Confirmed'?'selected':'' ?>>Confirmed</option>
            <option value="Cancelled" <?= $booking['booking_status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
            <option value="Completed" <?= $booking['booking_status']=='Completed'?'selected':'' ?>>Completed</option>
        </select>

        <h5>Equipment</h5>
        <?php while($eq = $equipment->fetch_assoc()): ?>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="equipment_ids[]" value="<?= (int)$eq['equipment_id'] ?>" 
                    <?= in_array($eq['equipment_id'],$booked_equipment)?'checked':'' ?>>
                <label class="form-check-label"><?= htmlspecialchars($eq['equipment_name']) ?> (₱<?= number_format((float)$eq['equipment_price'],2) ?>)</label>
            </div>
        <?php endwhile; ?>

        <!-- Display Converted Hours -->
        <p class="mt-3"><strong>Total Hours (HH:MM):</strong> <?= htmlspecialchars($initial_formatted_hours) ?></p>

        <button type="submit" class="btn btn-primary mt-3">Update Booking</button>
        <a href="booking_list.php" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
