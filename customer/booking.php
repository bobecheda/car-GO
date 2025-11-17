<?php
session_start();
include("../config/db.php");

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to book a car.");
}

// Get car ID from query string
if (!isset($_GET['id'])) {
    die("No car selected.");
}
$car_id = $_GET['id'];

// Fetch car details
$car_query = "SELECT * FROM cars WHERE id = $car_id";
$car_result = mysqli_query($conn, $car_query);
$car = mysqli_fetch_assoc($car_result);
if (!$car) {
    die("Car not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book <?php echo $car['brand'] . ' ' . $car['model']; ?></title>

    <!-- Bootstrap for quick styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <script>
        // JS Validation
        function validateBooking() {
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;

            if (!start || !end) {
                alert("Please select both start and end dates.");
                return false;
            }

            const startDate = new Date(start);
            const endDate = new Date(end);
            const today = new Date();
            today.setHours(0,0,0,0);

            if (startDate < today) {
                alert("Start date cannot be in the past.");
                return false;
            }

            if (endDate < startDate) {
                alert("End date cannot be before start date.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body class="bg-light">

<div class="container py-4" style="max-width:600px;">
    <h2 class="mb-4">Book <?php echo $car['brand'] . ' ' . $car['model']; ?></h2>

    <form action="process_booking.php" method="POST" onsubmit="return validateBooking();">
        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
        <input type="hidden" name="price_per_day" value="<?php echo $car['price_per_day']; ?>">

        <div class="mb-3">
            <label class="form-label">Pickup Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Return Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <p><strong>Price per day:</strong> KES <?php echo number_format($car['price_per_day']); ?></p>
        </div>

        <button type="submit" class="btn btn-primary w-100">Book Now</button>
    </form>
</div>

</body>
</html>
