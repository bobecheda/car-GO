<?php
session_start();
include("../config/db.php");


// Optional: Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied.");
}

// Fetch all bookings with car and customer info
$sql = "
SELECT b.*, c.brand, c.model
FROM bookings b
JOIN cars c ON b.car_id = c.id
ORDER BY b.start_date DESC
";
$result = mysqli_query($conn, $sql);
if ($result === false) {
    error_log('manage_bookings query error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>  


<div style="margin-left:220px; padding:20px;">
<div class="container py-4">
    <h2 class="mb-4 text-center">Admin Booking Management</h2>

    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Car</th>
                <th>Pickup</th>
                <th>Return</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
                <th>Message</th>
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
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></td>
                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                <td><?php echo number_format((float)$row['total_price']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                <td>
                    <?php if ($row['status'] == 'pending') { ?>

                        <!-- APPROVE -->
                        <a href="process_booking_action.php?id=<?php echo $row['id']; ?>&action=approve" 
                           class="btn btn-sm btn-success"
                           onclick="return confirm('Approve this booking?');">
                           Approve
                        </a>

                        <!-- REJECT -->
                        <a href="process_booking_action.php?id=<?php echo $row['id']; ?>&action=reject" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Reject this booking?');">
                           Reject
                        </a>


                    <?php } else { ?>
                        -
                    <?php } ?>
                    </td>
                <td><?php echo isset($row['admin_message']) ? htmlspecialchars($row['admin_message']) : '-'; ?></td>
            </tr>
            <?php
            }
        } else {
            echo '<tr><td colspan="9" class="text-center">No bookings found.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

 <?php include 'footer.php'; ?>
</body>
</html>
