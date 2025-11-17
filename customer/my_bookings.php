<?php
session_start();
include("../config/db.php");

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your bookings.");
}

// ensure we only use an integer user id (prevents accidental SQL injection)
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Fetch all bookings for this user
$sql = "
SELECT b.*, c.brand, c.model
FROM bookings b
JOIN cars c ON b.car_id = c.id
WHERE b.user_id = $user_id
ORDER BY b.start_date DESC
";

$result = mysqli_query($conn, $sql);
// If the query failed, capture the error for debugging (do not expose raw SQL in production)
if ($result === false) {
    error_log("my_bookings query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4 text-center">My Bookings</h2>
    <?php
// Show latest admin messages for logged-in user
$msg_sql = "
SELECT admin_message 
FROM bookings 
WHERE user_id = $user_id AND admin_message IS NOT NULL AND status IN ('approved','rejected')
ORDER BY start_date DESC
LIMIT 5
";
$msg_res = mysqli_query($conn, $msg_sql);
if ($msg_res) {
    while ($msg = mysqli_fetch_assoc($msg_res)) {
        echo "<div class='alert alert-info'>" . htmlspecialchars($msg['admin_message']) . "</div>";
    }
} else {
    // optional: log error
    if ($msg_res === false) error_log("my_bookings msg query error: " . mysqli_error($conn));
}
?>


    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Car</th>
                <th>Pickup Date</th>
                <th>Return Date</th>
                <th>Total Price (KES)</th>
                <th>Status</th>
                <th>Action</th>
                <th>Admin Message</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                ?>
            <tr>
                <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['brand'] . " " . $row['model']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                    <td><?php echo number_format((float)$row['total_price']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                <td>
                    <?php if ($row['status'] == 'pending') { ?>
                        <a href="cancel_booking.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to cancel this booking?');">
                           Cancel
                        </a>
                    <?php } else { echo "-"; } ?>
                </td>
                    <td>
                         <?php 
                         if (!empty($row['admin_message'])) {
                             echo "<span class='badge bg-info text-dark'>" . htmlspecialchars($row['admin_message']) . "</span>";
                         } else {
                             echo "-";
                         }
                         ?>
                     </td>
            </tr>
                <?php
                }
            } else {
                echo '<tr><td colspan="8" class="text-center">You have no bookings yet.</td></tr>';
            }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
