<?php
include '../config/db.php';

$id = $_POST['id'];
$plate = $_POST['plate_number'];
$model = $_POST['model'];
$brand = $_POST['brand'];
$year = $_POST['year'];
$capacity = $_POST['capacity'];
$transmission = $_POST['transmission'];
$fuel = $_POST['fuel_type'];
$price = $_POST['price_per_day'];
$status = $_POST['status'];

$imageName = null;

// Handle new image upload
if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
    $imageName = time() . "_" . $_FILES['image']['name'];
    $target = "uploads/" . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // Update with new image
    $sql = "UPDATE cars SET 
            plate_number='$plate',
            model='$model',
            brand='$brand',
            year='$year',
            capacity='$capacity',
            transmission='$transmission',
            fuel_type='$fuel',
            price_per_day='$price',
            status='$status',
            image_url='$imageName'
            WHERE id=$id";
} else {
    // Update without changing image
    $sql = "UPDATE cars SET 
            plate_number='$plate',
            model='$model',
            brand='$brand',
            year='$year',
            capacity='$capacity',
            transmission='$transmission',
            fuel_type='$fuel',
            price_per_day='$price',
            status='$status'
            WHERE id=$id";
}

mysqli_query($conn, $sql);

header("Location: manage_vehicles.php");
exit;
?>
