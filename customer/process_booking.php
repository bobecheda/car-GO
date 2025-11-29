<?php
// ---------------------------
// process_booking.php
// ---------------------------

// 1. Start session FIRST
if (session_status() === PHP_SESSION_NONE) session_start();

// 2. Include DB connection BEFORE anything else
if (file_exists(__DIR__ . '/../config/db.php')) {
    include __DIR__ . '/../config/db.php';
} else {
    die("Database connection file missing.");
}

// 3. User must be logged in
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to make a booking.');
}

$user_id = (int) $_SESSION['user_id'];

// 4. Get POST data BEFORE logging activity
$car_id = isset($_POST['car_id']) ? intval($_POST['car_id']) : 0;
$name = isset($_POST['customer_name']) ? mysqli_real_escape_string($conn, trim($_POST['customer_name'])) : '';
$email = isset($_POST['customer_email']) ? mysqli_real_escape_string($conn, trim($_POST['customer_email'])) : '';
$phone = isset($_POST['customer_phone']) ? mysqli_real_escape_string($conn, trim($_POST['customer_phone'])) : '';
$pickup = isset($_POST['pickup_date']) ? mysqli_real_escape_string($conn, trim($_POST['pickup_date'])) : '';
$return = isset($_POST['return_date']) ? mysqli_real_escape_string($conn, trim($_POST['return_date'])) : '';
$payment = isset($_POST['payment_method']) ? mysqli_real_escape_string($conn, trim($_POST['payment_method'])) : '';

// 5. Now include log_activity (AFTER $conn and session exist)
include 'log_activity.php';

// Log the booking attempt
log_activity(
    $conn,
    $user_id,
    "book_vehicle",
    "Begin booking process for car ID: $car_id",
    "process_booking.php"
);

// ---------------------------
// VALIDATION
// ---------------------------

if (strtotime($pickup) > strtotime($return)) {
    die("Pickup date cannot be after return date.");
}

// Check availability: prevent double-booking
$availability_sql = "
SELECT * FROM bookings
WHERE car_id = $car_id
AND status != 'cancelled'
AND (
    (start_date <= '$return' AND end_date >= '$pickup')
)
";

$availability_res = mysqli_query($conn, $availability_sql);

if (mysqli_num_rows($availability_res) > 0) {
    die("Sorry, this car is already booked for the selected dates.");
}

// Fetch car price
$car_sql = "SELECT price_per_day FROM cars WHERE id = $car_id LIMIT 1";
$car_res = mysqli_query($conn, $car_sql);
$car = mysqli_fetch_assoc($car_res);

$price_per_day = $car['price_per_day'];

// Calculate total days
$days = (strtotime($return) - strtotime($pickup)) / (60 * 60 * 24);
if ($days <= 0) { $days = 1; }

$total_price = $days * $price_per_day;

// Insert booking
$sql = "INSERT INTO bookings (car_id, user_id, customer_name, email, phone,
    start_date, end_date, total_price, status)
    VALUES ($car_id, $user_id, '$name', '$email', '$phone', '$pickup', '$return',
    $total_price, 'pending')";

if (mysqli_query($conn, $sql)) {

    // Mark car as booked
    mysqli_query($conn, "UPDATE cars SET status='booked' WHERE id=$car_id");

    // Log success
    log_activity(
        $conn,
        $user_id,
        "booking_success",
        "Successfully booked car ID: $car_id",
        "process_booking.php"
    );

    header("Location: booking_success.php?booking=1");
    exit;

} else {
    echo "Error: " . mysqli_error($conn);
}
?>
