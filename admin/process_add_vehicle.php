<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';

// Collect form data
$brand = $_POST['brand'];
$model = $_POST['model'];
$year = $_POST['year'];
$price_per_day = $_POST['price_per_day'];
$status = $_POST['status'];

// Handle image upload
$target_dir = "../uploads/";
if(!is_dir($target_dir)){
    mkdir($target_dir);
}

$image_name = time() . "_" . basename($_FILES["image"]["name"]);
$target_file = $target_dir . $image_name;

move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

// Insert into database
$query = "INSERT INTO cars (brand, model, year, price_per_day, status, image_url)
          VALUES ('$brand', '$model', '$year', '$price_per_day', '$status', '$image_name')";

if(mysqli_query($conn, $query)){
    header("Location: manage_vehicles.php?success=1");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
