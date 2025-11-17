<?php
// rent.php — Booking form page

// DB connection file
if (file_exists(__DIR__ . '/../config/db.php')) {
    include __DIR__ . '/../config/db.php';
} else {
    die("Database connection file missing.");
}

// Check if car_id is provided
if (!isset($_GET['car_id'])) {
    die("Car not found.");
}

$car_id = intval($_GET['car_id']);

// Fetch selected car details
$sql = "SELECT * FROM cars WHERE id = $car_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$car = mysqli_fetch_assoc($result);

if (!$car) {
    die("Car not found in database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent <?php echo $car['brand'] . " " . $car['model']; ?></title>
    <style>
        body { font-family: Arial; background:#f4f6f7; padding:20px; }
        .container { max-width:600px; margin:auto; background:#fff; padding:20px;
                     border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        .title { font-size:22px; font-weight:bold; margin-bottom:10px; }
        label { font-weight:bold; display:block; margin-top:10px; }
        input, select { width:100%; padding:10px; margin-top:6px; border:1px solid #ccc;
                         border-radius:6px; }
        .btn { margin-top:20px; padding:12px; background:#27ae60; color:#fff;
               text-align:center; display:block; border-radius:6px; text-decoration:none; }
    </style>
</head>
<body>

<div class="container">
    <div class="title">Book: <?php echo $car['brand'] . " " . $car['model']; ?></div>

    <form action="process_booking.php" method="POST">
        <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">

        <label>Your Full Name</label>
        <input type="text" name="customer_name" required>

        <label>Email Address</label>
        <input type="email" name="customer_email" required>

        <label>Phone Number</label>
        <input type="text" name="customer_phone" required>

        <label>Pickup Date</label>
        <input type="date" name="pickup_date" required>

        <label>Return Date</label>
        <input type="date" name="return_date" required>

        <label>Payment Method</label>
        <select name="payment_method" required>
            <option value="cash">Cash</option>
            <option value="mpesa">M-Pesa</option>
            <option value="card">Card Payment</option>
        </select>

        <button class="btn">Confirm Booking</button>
    </form>
</div>

</body>
</html>
