<?php
include("../config/db.php");

// Check if ID exists
if (!isset($_GET['id'])) {
    die("Vehicle not found.");
}

$car_id = $_GET['id'];

// Fetch vehicle details
$query = "SELECT * FROM cars WHERE id = '$car_id'";
$result = mysqli_query($conn, $query);
$car = mysqli_fetch_assoc($result);

if (!$car) {
    die("Vehicle not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $car['brand'] . " " . $car['model']; ?> - Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional -->
</head>

<body>

<div class="container" style="max-width: 900px; margin: auto;">

    <h1><?php echo $car['brand'] . " " . $car['model']; ?></h1>

    <!-- Vehicle Image -->
    <img src="../uploads/<?php echo $car['image_url']; ?>" 
         alt="Vehicle Image"
         style="width:100%; max-height: 350px; object-fit:cover; border-radius:8px;">

    <h2>Vehicle Specifications</h2>
    <ul style="font-size: 18px;">
        <li><strong>Brand:</strong> <?php echo $car['brand']; ?></li>
        <li><strong>Model:</strong> <?php echo $car['model']; ?></li>
        <li><strong>Year:</strong> <?php echo $car['year']; ?></li>
        <li><strong>Transmission:</strong> <?php echo $car['transmission']; ?></li>
        <li><strong>Fuel Type:</strong> <?php echo $car['fuel_type']; ?></li>
        <li><strong>Capacity:</strong> <?php echo $car['capacity']; ?> seats</li>
        <li><strong>Status:</strong> 
            <span style="color:<?php echo ($car['status']=='available'?'green':'red'); ?>">
                <?php echo ucfirst($car['status']); ?>
            </span>
        </li>
        <li><strong>Price per Day:</strong> KES <?php echo number_format($car['price_per_day']); ?></li>
    </ul>

    <!-- Rent Button -->
    <?php if ($car['status'] == "available"): ?>
        <a href="booking.php?id=<?php echo $car['id']; ?>" 
           style="display:block; padding:12px; background:#007bff; color:white; text-align:center; text-decoration:none; border-radius:6px;">
            Rent this Vehicle
        </a>
    <?php else: ?>
        <button disabled
                style="padding:12px; width:100%; background:gray; color:white; border:none; border-radius:6px;">
            Not Available
        </button>
    <?php endif; ?>

</div>

</body>
</html>
