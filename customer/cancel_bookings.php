<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['id']);

// Only allow cancelling your own booking
$sql = "UPDATE bookings 
        SET status='cancelled' 
        WHERE id=$booking_id AND user_id=$user_id AND status='pending'";

mysqli_query($conn, $sql);

header("Location: my_bookings.php");
exit;
?>
