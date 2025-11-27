<?php
    session_start();
    include "includes/db.php";

    if (! isset($_SESSION['client_id'])) {
        header("Location: customer_login.php");
        exit();
    }

    if (! isset($_GET['menu_id'])) {
        die("No item selected.");
    }

    $menu_id = intval($_GET['menu_id']);
    $stmt    = $conn->prepare("SELECT * FROM menu WHERE menu_id = ?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Menu item not found.");
    }

    $menu = $result->fetch_assoc();

    $quantity = 1;
    $price    = floatval($menu['menu_price']);
    $subtotal = $price;
    $total    = $price;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $quantity = intval($_POST['quantity']);
        $price    = floatval($menu['menu_price']);
        $subtotal = $price * $quantity;
        $total    = $subtotal;

        $client_id         = $_SESSION['client_id'];
        $payment_method    = $_POST['payment_method'];
        $payment_reference = $_POST['payment_reference'] ?? null;
        $temperature       = $_POST['temperature'] ?? null;

        $payment_proof = null;
        if (! empty($_FILES['payment_proof']['name'])) {
            $targetDir = "uploads/";
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName   = time() . "_" . basename($_FILES["payment_proof"]["name"]);
            $targetFile = $targetDir . $fileName;
            move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $targetFile);
            $payment_proof = $targetFile;
        }

        $booking_id      = null;
        $special_request = $temperature ?? null;

        $stmt_order = $conn->prepare("
    INSERT INTO order_items
    (client_id, booking_id, menu_id, order_quantity, order_amount, order_date, special_request)
    VALUES (?, ?, ?, ?, ?, NOW(), ?)
");
        $stmt_order->bind_param("iiidss",
            $client_id,
            $booking_id,
            $menu_id,
            $quantity,
            $subtotal,
            $special_request
        );
        $stmt_order->execute();

        $order_item_id = $stmt_order->insert_id;

        $stmt_payment = $conn->prepare("
        INSERT INTO payment
        (payment_date, payment_amount, payment_method, client_id, order_item_id, payment_type, payment_proof, payment_reference)
        VALUES
        (NOW(), ?, ?, ?, ?, 'Order', ?, ?)
    ");
        $stmt_payment->bind_param("dsiiss", $total, $payment_method, $client_id, $order_item_id, $payment_proof, $payment_reference);
        $stmt_payment->execute();
        $payment_id = $stmt_payment->insert_id;

        header("Location: thankyou.php?payment_id=" . $payment_id);
        exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="payment-page">
    <div class="order-container">

        <div class="order-left">
            <img src="<?php echo $menu['menu_image']; ?>" alt="<?php echo htmlspecialchars($menu['menu_name']); ?>">
        </div>

        <div class="order-right">
            <h2><?php echo htmlspecialchars($menu['menu_name']); ?></h2>
            <h5><?php echo htmlspecialchars($menu['menu_category']); ?></h5>
            <p><?php echo htmlspecialchars($menu['menu_description']); ?></p>
            <h4>₱<?php echo number_format($menu['menu_price'], 2); ?></h4>

            <?php if (strtolower(trim($menu['menu_category'])) == 'specialty coffee'): ?>
                <div class="temperature-info">
                    <label><strong>Variety:</strong></label><br>
                    <label class="radio-option">
                        <input type="radio" name="temperature" value="Hot" form="payment-form" required> Hot
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="temperature" value="Cold" form="payment-form" required> Cold
                    </label>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="payment-container">

        <form method="POST" id="payment-form" class="payment-form" enctype="multipart/form-data">

            <div class="payment-grid">

                <div class="left-col">
                    <label>Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="<?php echo $quantity; ?>" min="1" required>

                    <div class="summary-box">
                        <h3>Order Summary</h3>
                        <p>Price per Item: ₱<?php echo number_format($price, 2); ?></p>
                        <p>Quantity: <span id="summary_qty"><?php echo $quantity; ?></span></p>
                        <p><strong>Total:</strong> ₱<span id="total"><?php echo number_format($total, 2); ?></span></p>
                    </div>
                </div>

                <div class="right-col">
                    <label>Payment Method:</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="GCash">GCash</option>
                    </select>

                    <div id="payment_instructions" class="payment-instructions"></div>

                    <div id="proof_container" style="display:none;">
                        <label>Upload Payment Proof:</label>
                        <input type="file" name="payment_proof" accept="image/*">
                    </div>

                    <div id="reference_container" style="display:none;">
                        <label>Reference Number:</label>
                        <input type="text" name="payment_reference" placeholder="Enter reference number">
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Pay Now</button>

        </form>

    </div>
</div>

<script>
const pricePerItem =                     <?php echo $price; ?>;

const quantityInput = document.getElementById('quantity');
const totalEl = document.getElementById('total');
const summaryQty = document.getElementById('summary_qty');

function updateSummary() {
    const qty = Math.max(1, parseInt(quantityInput.value) || 1);
    summaryQty.textContent = qty;
    totalEl.textContent = (pricePerItem * qty).toFixed(2);
}

quantityInput.addEventListener('input', updateSummary);
updateSummary();

const paymentMethod = document.getElementById('payment_method');
const proofBox = document.getElementById('proof_container');
const refBox = document.getElementById('reference_container');
const instructions = document.getElementById('payment_instructions');

paymentMethod.addEventListener('change', function () {
    const method = this.value;

    proofBox.style.display = "none";
    refBox.style.display = "none";

    if (method === 'Cash') {
        instructions.innerHTML = `
            <p><strong>Cash Payment Instructions:</strong><br>
            Pay directly at the counter.</p>
        `;
    }
    else if (method === 'GCash') {
        instructions.innerHTML = `
            <p><strong>GCash Payment Instructions:</strong><br>
            Send payment to <b>0966-947-6532</b><br>
            Upload proof & reference number.</p>
        `;
        proofBox.style.display = "block";
        refBox.style.display = "block";
    }
    else if (method === 'Card') {
        instructions.innerHTML = `
            <p><strong>Card Payment Instructions:</strong><br>
            Pay using your Debit/Credit Card at the counter only.<br>
            Upload proof & reference number.</p>
        `;
        proofBox.style.display = "block";
        refBox.style.display = "block";
    }
});

paymentMethod.dispatchEvent(new Event('change'));
</script>

</body>
</html>
