<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

if(!isset($_GET['id'])){
    die("Customer ID missing.");
}

$customer_id = (int)$_GET['id'];

// Get current status
$sql = "SELECT status FROM users WHERE id=$customer_id LIMIT 1";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

$new_status = ($row['status'] == 'active') ? 'suspended' : 'active';

// Update status
$update_sql = "UPDATE users SET status='$new_status' WHERE id=$customer_id";
if(mysqli_query($conn, $update_sql)){
    header("Location: manage_customers.php");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
