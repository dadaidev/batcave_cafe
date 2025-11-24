<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['client_id'])) {
    header("Location: customer_login.php");
    exit();
}

$show_summary = false;
$total_hours = 0;
$total_price = 0;
$error = '';
$table = null;
$equipment_selected = [];
$equipment_details = []; // for summary display
$equipment_total = 0.00;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $table_id = intval($_POST['table_id'] ?? 0);
    // Note: booking_start_time and end are expected in "HH:MM" format (sent from booking modal)
    $booking_start_time = $_POST['booking_start_time'] ?? '';
    $booking_end_time = $_POST['booking_end_time'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_description = trim($_POST['booking_description'] ?? '');
    $booking_pax = intval($_POST['booking_pax'] ?? 1);

    // Equipment inputs (from booking.php modal)
    $raw_eq = $_POST['equipment_ids'] ?? []; // array of equipment_id strings
    // If you later add equipment_qty[...] in the modal, it'll pick it up here:
    $equipment_qty_post = $_POST['equipment_qty'] ?? [];

    // Normalize equipment IDs to integers, ignore invalid
    $equipment_selected = array_values(array_filter(array_map('intval', (array)$raw_eq), function($v){ return $v > 0; }));

    // Get table info
    $stmt_table = $conn->prepare("SELECT * FROM table_seats WHERE table_id = ?");
    $stmt_table->bind_param("i", $table_id);
    $stmt_table->execute();
    $res_table = $stmt_table->get_result();
    if ($res_table->num_rows == 0) {
        die("Table not found.");
    }
    $table = $res_table->fetch_assoc();
    $base_price = floatval($table['table_price']);

    // Convert start/end to timestamps
    // If booking_start_time/post values might already include date, attempt strtotime; otherwise assume HH:MM on same day
    $start_ts = strtotime($booking_start_time);
    $end_ts = strtotime($booking_end_time);
    if ($start_ts === false || $end_ts === false) {
        // try parsing as "HH:MM" and attach booking_date if provided
        if (!empty($booking_date)) {
            $start_ts = strtotime($booking_date . ' ' . $booking_start_time);
            $end_ts = strtotime($booking_date . ' ' . $booking_end_time);
        } else {
            $start_ts = false;
            $end_ts = false;
        }
    }

    if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
        $error = "Invalid time selection.";
    } else {
        $total_hours = ($end_ts - $start_ts) / 3600.0;

        // Minimum booking: 0.5 hour (30 mins)
        if ($total_hours < 0.5) {
            $error = "Minimum booking is 30 minutes.";
        } else {
            // Fee calculation
            if ($total_hours < 2) {
                $total_price = $total_hours * ($base_price + 25);
            } else {
                $total_price = $total_hours * $base_price;
            }
            // At this stage we also compute equipment total (before showing summary / paying)
            $equipment_total = 0.00;
            if (!empty($equipment_selected)) {
                // Prepare statement once
                $stmt_eq = $conn->prepare("SELECT equipment_id, equipment_name, equipment_price FROM equipment WHERE equipment_id = ?");
                foreach ($equipment_selected as $eqid) {
                    $stmt_eq->bind_param("i", $eqid);
                    $stmt_eq->execute();
                    $res_eq = $stmt_eq->get_result();
                    if ($res_eq && $res_eq->num_rows > 0) {
                        $row = $res_eq->fetch_assoc();
                        // default qty = 1 (if you later send equipment_qty[<id>] it will be used)
                        $qty = isset($equipment_qty_post[$eqid]) ? max(1, intval($equipment_qty_post[$eqid])) : 1;
                        $line_total = floatval($row['equipment_price']) * $qty;
                        $equipment_total += $line_total;
                        $equipment_details[] = [
                            'equipment_id' => $row['equipment_id'],
                            'equipment_name' => $row['equipment_name'],
                            'equipment_price' => floatval($row['equipment_price']),
                            'qty' => $qty,
                            'line_total' => $line_total
                        ];
                    }
                }
                $stmt_eq->close();
            }

            // Add equipment_total to the overall total price
            $total_price = $total_price + $equipment_total;

            $show_summary = true;
        }
    }

    // If Pay Now clicked, insert into DB (booking, payment, booking_equipment)
    if (isset($_POST['pay_now']) && empty($error)) {
        $client_id = $_SESSION['client_id'];
        $payment_method = $_POST['payment_method'] ?? 'Cash';
        $payment_reference = trim($_POST['payment_reference'] ?? null);
        $booking_time = (is_numeric($booking_start_time) || strpos($booking_start_time, ':') !== false) ? ($booking_start_time . ' - ' . $booking_end_time) : $booking_start_time . ' - ' . $booking_end_time;

        // Recompute equipment details & total here to be safe (in case someone tampered with POST)
        $equipment_total = 0.00;
        $equipment_details = [];
        if (!empty($equipment_selected)) {
            $stmt_eq2 = $conn->prepare("SELECT equipment_id, equipment_name, equipment_price FROM equipment WHERE equipment_id = ?");
            foreach ($equipment_selected as $eqid) {
                $stmt_eq2->bind_param("i", $eqid);
                $stmt_eq2->execute();
                $res_eq2 = $stmt_eq2->get_result();
                if ($res_eq2 && $res_eq2->num_rows > 0) {
                    $row = $res_eq2->fetch_assoc();
                    $qty = isset($equipment_qty_post[$eqid]) ? max(1, intval($equipment_qty_post[$eqid])) : 1;
                    $line_total = floatval($row['equipment_price']) * $qty;
                    $equipment_total += $line_total;
                    $equipment_details[] = [
                        'equipment_id' => $row['equipment_id'],
                        'equipment_name' => $row['equipment_name'],
                        'equipment_price' => floatval($row['equipment_price']),
                        'qty' => $qty,
                        'line_total' => $line_total
                    ];
                }
            }
            $stmt_eq2->close();
        }

        // Recompute booking price for safety (server-side)
        $total_hours = ($end_ts - $start_ts) / 3600.0;
        if ($total_hours < 2) {
            $booking_price = $total_hours * ($base_price + 25);
        } else {
            $booking_price = $total_hours * $base_price;
        }
        $final_total_price = $booking_price + $equipment_total;

        // Handle payment proof upload
        $payment_proof = null;
        if (!empty($_FILES['payment_proof']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            // sanitize file name
            $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES["payment_proof"]["name"]));
            $fileName = time() . "_" . $safeName;
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $targetFile)) {
                $payment_proof = $targetFile;
            } else {
                // Not fatal: continue without proof, or set error
                // $error = "Failed to upload payment proof.";
            }
        }

        // Ensure booking_equipment table exists (non-destructive)
        $create_table_sql = "CREATE TABLE IF NOT EXISTS booking_equipment (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_id INT NOT NULL,
            equipment_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            FOREIGN KEY (booking_id) REFERENCES booking(booking_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $conn->query($create_table_sql);

        // Start transaction
        $conn->begin_transaction();
        try {
            // Insert booking
            $stmt_booking = $conn->prepare("INSERT INTO booking 
                (booking_description, booking_date, booking_time, booking_pax, table_id, booking_status, client_id)
                VALUES (?, ?, ?, ?, ?, 'Pending', ?)");
            $stmt_booking->bind_param("sssiii", $booking_description, $booking_date, $booking_time, $booking_pax, $table_id, $client_id);
            $stmt_booking->execute();
            $booking_id = $stmt_booking->insert_id;
            $stmt_booking->close();

            // Insert payment
            $stmt_payment = $conn->prepare("INSERT INTO payment 
                (payment_date, payment_amount, payment_method, client_id, booking_id, payment_type, payment_proof, payment_reference)
                VALUES (NOW(), ?, ?, ?, ?, 'Booking', ?, ?)");
            // types: payment_amount (d), payment_method (s), client_id (i), booking_id (i), payment_proof (s), payment_reference (s)
            $stmt_payment->bind_param("dsiiss", $final_total_price, $payment_method, $client_id, $booking_id, $payment_proof, $payment_reference);
            $stmt_payment->execute();
            $payment_id = $stmt_payment->insert_id;
            $stmt_payment->close();

            // Insert booking_equipment rows
            if (!empty($equipment_details)) {
                $stmt_insert_eq = $conn->prepare("INSERT INTO booking_equipment (booking_id, equipment_id, quantity) VALUES (?, ?, ?)");
                foreach ($equipment_details as $ed) {
                    $stmt_insert_eq->bind_param("iii", $booking_id, $ed['equipment_id'], $ed['qty']);
                    $stmt_insert_eq->execute();
                }
                $stmt_insert_eq->close();
            }

            $conn->commit();

            header("Location: thankyou.php?payment_id=" . $payment_id);
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to save booking/payment: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bat Café - Booking Payment</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="payment-page container mt-4">
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>"; ?>

    <?php if ($table): ?>
        <div class="order-container d-flex gap-4 mb-4">
            <div class="order-left">
                <img src="<?php echo htmlspecialchars($table['table_image'] ?: 'images/default_table.jpg'); ?>" alt="<?php echo htmlspecialchars($table['table_name']); ?>" class="img-fluid">
            </div>
            <div class="order-right">
                <h2><?php echo htmlspecialchars($table['table_name']); ?> (Table <?php echo htmlspecialchars($table['table_number']); ?>)</h2>
                <p>Seats: <?php echo htmlspecialchars($table['capacity']); ?></p>
                <p>Rate per hour: ₱<?php echo number_format($base_price,2); ?></p>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table['table_id']); ?>">
            <input type="hidden" name="booking_start_time" value="<?php echo htmlspecialchars($_POST['booking_start_time'] ?? ''); ?>">
            <input type="hidden" name="booking_end_time" value="<?php echo htmlspecialchars($_POST['booking_end_time'] ?? ''); ?>">
            <input type="hidden" name="total_hours" value="<?php echo htmlspecialchars($total_hours); ?>">
            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

            <label>Description:</label>
            <input type="text" name="booking_description" class="form-control mb-2" value="<?php echo htmlspecialchars($_POST['booking_description'] ?? ''); ?>" required>

            <label>Date:</label>
            <input type="date" name="booking_date" class="form-control mb-2" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($_POST['booking_date'] ?? ''); ?>" required>

            <label>Start Time:</label>
            <input type="time" name="booking_start_time_display" class="form-control mb-2" value="<?php echo htmlspecialchars($_POST['booking_start_time'] ?? ''); ?>" required>

            <label>End Time:</label>
            <input type="time" name="booking_end_time_display" class="form-control mb-2" value="<?php echo htmlspecialchars($_POST['booking_end_time'] ?? ''); ?>" required>

            <label>Pax:</label>
            <input type="number" name="booking_pax" class="form-control mb-2" min="1" value="<?php echo htmlspecialchars($_POST['booking_pax'] ?? 1); ?>" required>

            <?php
            // If any equipment was posted, include hidden inputs so pay stage receives them too
            if (!empty($equipment_selected)) {
                foreach ($equipment_selected as $eid) {
                    echo '<input type="hidden" name="equipment_ids[]" value="' . intval($eid) . '">';
                    // If qty posted, carry it as hidden too
                    if (isset($equipment_qty_post[$eid])) {
                        echo '<input type="hidden" name="equipment_qty['.intval($eid).']" value="'.intval($equipment_qty_post[$eid]).'">';
                    }
                }
            }
            ?>

            <?php if ($show_summary): ?>
                <div class="summary-box mb-2">
                    <h4>Booking Summary</h4>
                    <p>Total Hours: <?php echo number_format($total_hours,2); ?></p>
                    <p>Booking Fee: ₱<?php echo number_format(($total_hours < 2 ? $total_hours * ($base_price + 25) : $total_hours * $base_price), 2); ?></p>

                    <?php if (!empty($equipment_details)): ?>
                        <hr>
                        <h5>Equipment</h5>
                        <ul>
                            <?php foreach ($equipment_details as $ed): ?>
                                <li><?php echo htmlspecialchars($ed['equipment_name']); ?> x<?php echo intval($ed['qty']); ?> — ₱<?php echo number_format($ed['line_total'],2); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p>Equipment Total: ₱<?php echo number_format($equipment_total,2); ?></p>
                    <?php endif; ?>

                    <hr>
                    <p><strong>Total Price: ₱<?php echo number_format($total_price,2); ?></strong></p>
                </div>

                <label>Payment Method:</label>
                <select name="payment_method" id="payment_method" class="form-control mb-2" required>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="GCash">GCash</option>
                </select>

                <div id="proof_container" style="display:none;">
                    <label>Upload Payment Proof:</label>
                    <input type="file" name="payment_proof" class="form-control mb-2" accept="image/*">
                </div>

                <div id="reference_container" style="display:none;">
                    <label>Reference Number:</label>
                    <input type="text" name="payment_reference" class="form-control mb-2" placeholder="Enter reference number">
                </div>

                <button type="submit" name="pay_now" class="btn btn-primary">Pay Now</button>
            <?php else: ?>
                <button type="submit" name="calculate" class="btn btn-secondary">Calculate Total</button>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

<script>
const paymentMethod = document.getElementById('payment_method');
const proofBox = document.getElementById('proof_container');
const refBox = document.getElementById('reference_container');

if (paymentMethod) {
    paymentMethod.addEventListener('change', function () {
        const method = this.value;
        if (method === 'Cash') {
            proofBox.style.display = "none";
            refBox.style.display = "none";
        } else {
            proofBox.style.display = "block";
            refBox.style.display = "block";
        }
    });
    paymentMethod.dispatchEvent(new Event('change'));
}
</script>
</body>
</html>
