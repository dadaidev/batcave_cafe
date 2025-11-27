<?php 
require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/admin_authorization.php';
require_login();
include "../includes/db.php";
require 'admin_header.php';

// Fetch bookings with client & table info
$bookings = $conn->query("
    SELECT b.*,
           c.client_fname, c.client_lname,
           t.table_name, t.table_price, t.capacity
    FROM booking b
    JOIN client c ON b.client_id = c.client_id
    JOIN table_seats t ON b.table_id = t.table_id
    ORDER BY booking_id ASC
");
?>

<div class="container mt-5">
    <h2>Bookings</h2>
    <a href="booking_add.php" class="btn btn-success mb-3">Add Booking</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Table</th>
                <th>Date</th>
                <th>Time</th>
                <th>Pax</th>
                <th>Equipment</th>
                <th>Hours</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($b = $bookings->fetch_assoc()): ?>
                <?php
                    // Fetch equipment
                    $equipments = $conn->query("
                        SELECT e.equipment_name, e.equipment_price
                        FROM equipment e
                        JOIN booking_equipment be ON e.equipment_id = be.equipment_id
                        WHERE be.booking_id = " . (int)$b['booking_id']
                    );
                    $equipment_list  = [];
                    $equipment_total = 0;
                    while ($eq = $equipments->fetch_assoc()) {
                        $equipment_list[] = $eq['equipment_name'];
                        $equipment_total += (float)$eq['equipment_price'];
                    }

                    // Calculate total price
                    $hours       = (float)$b['booking_hours'];
                    $table_price = (float)$b['table_price'];
                    if ($hours < 2) {
                        $amount = $hours * ($table_price + 25) + $equipment_total;
                    } else {
                        $amount = $hours * $table_price + $equipment_total;
                    }

                    // Format time
                    $start_time = date("H:i", strtotime($b['booking_start_time']));
                    $end_time   = date("H:i", strtotime($b['booking_end_time']));

                    // Convert decimal hours to HH:MM
                    $hrs             = floor($hours);
                    $mins            = round(($hours - $hrs) * 60);
                    // adjust if rounding pushed mins to 60
                    if ($mins === 60) { $hrs += 1; $mins = 0; }
                    $formatted_hours = sprintf("%02d:%02d", $hrs, $mins);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($b['booking_id']) ?></td>
                    <td><?php echo htmlspecialchars($b['client_fname'] . ' ' . $b['client_lname']) ?></td>
                    <td><?php echo htmlspecialchars($b['table_name'])?> (<?php echo htmlspecialchars($b['capacity'])?> pax)</td>
                    <td><?php echo htmlspecialchars($b['booking_date'])?></td>
                    <td><?php echo $start_time?> - <?php echo $end_time?></td>
                    <td><?php echo htmlspecialchars($b['booking_pax'])?></td>
                    <td><?php echo htmlspecialchars(implode(', ', $equipment_list))?></td>
                    <td><?php echo $formatted_hours?></td>
                    <td>₱<?php echo number_format($amount, 2)?></td>
                    <td>
                        <form method="POST" action="booking_edit.php?id=<?php echo (int)$b['booking_id']?>">
                            <select name="booking_status" class="form-select" onchange="this.form.submit()">
                                <option value="Pending" <?php echo $b['booking_status'] == 'Pending' ? 'selected' : ''?>>Pending</option>
                                <option value="Confirmed" <?php echo $b['booking_status'] == 'Confirmed' ? 'selected' : ''?>>Confirmed</option>
                                <option value="Cancelled" <?php echo $b['booking_status'] == 'Cancelled' ? 'selected' : ''?>>Cancelled</option>
                                <option value="Completed" <?php echo $b['booking_status'] == 'Completed' ? 'selected' : ''?>>Completed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="booking_edit.php?id=<?php echo (int)$b['booking_id']?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="booking_delete.php?id=<?php echo $b['booking_id']?>" 
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this booking?');">
                            Delete
                            </a>
                            
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
