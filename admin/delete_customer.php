<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

if(!isset($_GET['id'])) {
    die("Customer ID missing.");
}

$customer_id = (int)$_GET['id'];

// Optional: Check if customer has bookings
$check_sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id=$customer_id";
$res = mysqli_query($conn, $check_sql);
$row = mysqli_fetch_assoc($res);

if($row['total'] > 0){
    die("Cannot delete customer with existing bookings.");
}

// Delete customer
$delete_sql = "DELETE FROM users WHERE id=$customer_id";
if(mysqli_query($conn, $delete_sql)) {
    header("Location: manage_customers.php");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
