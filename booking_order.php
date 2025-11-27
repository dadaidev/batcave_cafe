<?php 
session_start();
include "includes/db.php";

if (!isset($_SESSION['client_id'])) {
    header("Location: customer_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$message = "";

$equipment = $conn->query("SELECT * FROM equipment ORDER BY equipment_name ASC");

$selected_date = $_POST['booking_date'] ?? date('Y-m-d');

$tables = $conn->query("
    SELECT t.*, 
           (t.capacity - IFNULL(SUM(b.booking_pax),0)) AS available_seats
    FROM table_seats t
    LEFT JOIN booking b 
        ON t.table_id = b.table_id 
        AND b.booking_date = '".$conn->real_escape_string($selected_date)."'
        AND b.booking_status IN ('Pending','Confirmed')
    GROUP BY t.table_id
    ORDER BY t.table_name
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_id'])) {
    $table       = (int)$_POST['table_id'];
    $date        = $conn->real_escape_string($_POST['booking_date']);
    $startTime   = $conn->real_escape_string($_POST['booking_start_time']);
    $endTime     = $conn->real_escape_string($_POST['booking_end_time']);
    $pax         = (int)$_POST['booking_pax'];
    $desc        = $conn->real_escape_string($_POST['booking_description']);
    $equipment_ids = isset($_POST['equipment_ids']) ? array_map('intval', $_POST['equipment_ids']) : [];
    $status      = "Pending";

    $check_avail_row = $conn->query("
        SELECT t.capacity, (t.capacity - IFNULL(SUM(b.booking_pax),0)) AS available_seats
        FROM table_seats t
        LEFT JOIN booking b 
            ON t.table_id = b.table_id 
            AND b.booking_date = '$date' 
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

    if ($pax > $check_avail_row['available_seats']) {
        $message = "Cannot book. Only ".$check_avail_row['available_seats']." seats are available for this table/time.";
    } else {
        $start_sec = strtotime($startTime);
        $end_sec   = strtotime($endTime);
        if($end_sec <= $start_sec) {
            $message = "End time must be after start time.";
        } else {
            $total_hours = round(($end_sec - $start_sec)/3600,2);
            $table_price = (float)$conn->query("SELECT table_price FROM table_seats WHERE table_id=$table")->fetch_assoc()['table_price'];
            $booking_amount = ($total_hours < 2) ? $total_hours * ($table_price + 25) : $total_hours * $table_price;

            if(count($equipment_ids) > 0) {
                $ids = implode(',', $equipment_ids);
                $equip_total_row = $conn->query("SELECT SUM(equipment_price) as total FROM equipment WHERE equipment_id IN ($ids)")->fetch_assoc();
                $equip_total = (float)($equip_total_row['total'] ?? 0);
                $booking_amount += $equip_total;
            }

            $stmt = $conn->prepare("
                INSERT INTO booking 
                (client_id, table_id, booking_date, booking_start_time, booking_end_time, booking_hours, booking_pax, booking_description, booking_status, booking_amount)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iisssdissd", $client_id, $table, $date, $startTime, $endTime, $total_hours, $pax, $desc, $status, $booking_amount);
            if($stmt->execute()){
                $booking_id = $stmt->insert_id;
                foreach($equipment_ids as $eq_id){
                    $conn->query("INSERT INTO booking_equipment (booking_id, equipment_id) VALUES (".(int)$booking_id.",".(int)$eq_id.")");
                }
                header("Location: booking_payment.php?booking_id=$booking_id");
                exit();
            } else {
                $message = "Error: ".$conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Booking</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 700px;">
    <h2>Book a Table</h2>
    <?php if($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Table</label>
        <select name="table_id" class="form-control mb-3" required>
            <option value="">Select table</option>
            <?php while($t = $tables->fetch_assoc()):
                $avail = isset($t['available_seats']) ? (int)$t['available_seats'] : (int)$t['capacity'];
                $disabled = $avail <= 0 ? 'disabled' : '';
            ?>
                <option value="<?= (int)$t['table_id'] ?>" <?= (isset($_POST['table_id']) && $_POST['table_id']==$t['table_id'])?'selected':'' ?> <?= $disabled ?> >
                    <?= htmlspecialchars($t['table_name']) ?> (<?= (int)$t['capacity'] ?> pax) - <?= $avail ?> seats left
                    <?= $disabled ? ' [FULL]' : '' ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Date</label>
        <input type="date" name="booking_date" class="form-control mb-3" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($selected_date) ?>" required>

        <label>Start Time</label>
        <input type="time" name="booking_start_time" class="form-control mb-3" min="13:00" max="22:00" value="<?= htmlspecialchars($_POST['booking_start_time'] ?? '') ?>" required>

        <label>End Time</label>
        <input type="time" name="booking_end_time" class="form-control mb-3" min="13:00" max="22:00" value="<?= htmlspecialchars($_POST['booking_end_time'] ?? '') ?>" required>

        <label>Pax</label>
        <input type="number" name="booking_pax" class="form-control mb-3" min="1" value="<?= htmlspecialchars($_POST['booking_pax'] ?? '1') ?>" required>

        <label>Description</label>
        <textarea name="booking_description" class="form-control mb-3"><?= htmlspecialchars($_POST['booking_description'] ?? '') ?></textarea>

        <h5>Equipment</h5>
        <?php
        $equipment->data_seek(0);
        while($eq = $equipment->fetch_assoc()): ?>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="equipment_ids[]" value="<?= (int)$eq['equipment_id'] ?>" <?= (isset($_POST['equipment_ids']) && in_array($eq['equipment_id'], $_POST['equipment_ids'])) ? 'checked' : '' ?>>
                <label class="form-check-label"><?= htmlspecialchars($eq['equipment_name']) ?> (₱<?= number_format((float)$eq['equipment_price'],2) ?>)</label>
            </div>
        <?php endwhile; ?>

        <button type="submit" class="btn btn-primary mt-3">Book Table</button>
    </form>
</div>

</body>
</html>
