<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['client_id'])) {
    header("Location: customer_login.php");
    exit();
}

if (!isset($_GET['booking_id'])) {
    die("No booking selected.");
}

$booking_id = intval($_GET['booking_id']);
$client_id = $_SESSION['client_id'];

// Fetch booking with table info
$stmt = $conn->prepare("
    SELECT b.*, t.table_name, t.table_price
    FROM booking b
    JOIN table_seats t ON b.table_id = t.table_id
    WHERE b.booking_id = ? AND b.client_id = ?
");
$stmt->bind_param("ii", $booking_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

// Fetch booked equipment
$equipment_total = 0;
$equipment_list = $conn->query("
    SELECT e.*
    FROM booking_equipment be
    JOIN equipment e ON be.equipment_id = e.equipment_id
    WHERE be.booking_id = $booking_id
");
$equipment_names = [];
while ($eq = $equipment_list->fetch_assoc()) {
    $equipment_total += floatval($eq['equipment_price']);
    $equipment_names[] = $eq['equipment_name'];
}

$total_amount = floatval($booking['booking_amount']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $payment_reference = $_POST['payment_reference'] ?? null;

    $payment_proof = null;
    if (!empty($_FILES['payment_proof']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["payment_proof"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $targetFile);
        $payment_proof = $targetFile;
    }

    $stmt_payment = $conn->prepare("
        INSERT INTO payment
        (payment_date, payment_amount, payment_method, client_id, booking_id, payment_type, payment_proof, payment_reference)
        VALUES (NOW(), ?, ?, ?, ?, 'Booking', ?, ?)
    ");
    $stmt_payment->bind_param("dsisss", $total_amount, $payment_method, $client_id, $booking_id, $payment_proof, $payment_reference);
    $stmt_payment->execute();

    $payment_id = $stmt_payment->insert_id;
    header("Location: thankyou.php?payment_id=$payment_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Payment</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 700px;">
    <h2>Booking Payment</h2>

    <div class="mb-3">
        <p><strong>Booking:</strong> <?= htmlspecialchars($booking['table_name']) ?> on <?= $booking['booking_date'] ?></p>
        <p><strong>Time:</strong> <?= $booking['booking_start_time'] ?> - <?= $booking['booking_end_time'] ?></p>
        <p><strong>Pax:</strong> <?= $booking['booking_pax'] ?></p>
        <?php if(count($equipment_names) > 0): ?>
            <p><strong>Equipment:</strong> <?= implode(', ', $equipment_names) ?></p>
        <?php endif; ?>
        <p><strong>Total Amount:</strong> ₱<?= number_format($total_amount, 2) ?></p>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <label>Payment Method</label>
        <select name="payment_method" id="payment_method" class="form-control mb-3" required>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="GCash">GCash</option>
        </select>

        <div id="payment_instructions" class="mb-3"></div>

        <div id="proof_container" style="display:none;" class="mb-3">
            <label>Upload Payment Proof</label>
            <input type="file" name="payment_proof" accept="image/*" class="form-control">
        </div>

        <div id="reference_container" style="display:none;" class="mb-3">
            <label>Reference Number</label>
            <input type="text" name="payment_reference" class="form-control" placeholder="Enter reference number">
        </div>

        <button type="submit" class="btn btn-primary">Pay Now</button>
    </form>
</div>

<script>
const paymentMethod = document.getElementById('payment_method');
const proofBox = document.getElementById('proof_container');
const refBox = document.getElementById('reference_container');
const instructions = document.getElementById('payment_instructions');

paymentMethod.addEventListener('change', function() {
    const method = this.value;
    proofBox.style.display = 'none';
    refBox.style.display = 'none';
    if (method === 'Cash') {
        instructions.innerHTML = "<p>Pay directly at the counter.</p>";
    } else if (method === 'GCash') {
        instructions.innerHTML = "<p>Send payment to 0966-947-6532. Upload proof & reference number.</p>";
        proofBox.style.display = 'block';
        refBox.style.display = 'block';
    } else if (method === 'Card') {
        instructions.innerHTML = "<p>Pay at the counter with your card. Upload proof & reference number.</p>";
        proofBox.style.display = 'block';
        refBox.style.display = 'block';
    }
});
paymentMethod.dispatchEvent(new Event('change'));
</script>

</body>
</html>
