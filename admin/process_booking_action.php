<?php
session_start();
include("../config/db.php");

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied.");
}

if (!isset($_GET['id'], $_GET['action'])) {
    die("Invalid request.");
}

$booking_id = intval($_GET['id']);
$action = $_GET['action'];

// Fetch booking info
$booking_res = mysqli_query($conn, "SELECT * FROM bookings WHERE id=$booking_id LIMIT 1");
$booking = mysqli_fetch_assoc($booking_res);

if (!$booking) {
    die("Booking not found.");
}

// Perform action
if ($action == 'approve') {
    // Update booking status
    mysqli_query($conn, "UPDATE bookings SET status='approved', admin_message='Booking approved by admin' WHERE id=$booking_id");
    // Update car status
    mysqli_query($conn, "UPDATE cars SET status='booked' WHERE id={$booking['car_id']}");
} elseif ($action == 'reject') {
    // Update booking status
    mysqli_query($conn, "UPDATE bookings SET status='rejected', admin_message='Booking rejected by admin' WHERE id=$booking_id");
    // Keep car available
    mysqli_query($conn, "UPDATE cars SET status='available' WHERE id={$booking['car_id']}");
}

header("Location: manage_bookings.php");
exit;
?>
