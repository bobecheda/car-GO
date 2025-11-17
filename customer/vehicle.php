<?php
// vehicle.php - Customer vehicle detail page

// DB connection — try multiple paths
if (file_exists(__DIR__ . '/../config/db.php')) {
    include __DIR__ . '/../config/db.php';
} elseif (file_exists(__DIR__ . '/../config/db_connection.php')) {
    include __DIR__ . '/../config/db_connection.php';
} elseif (file_exists(__DIR__ . '/../db_connection.php')) {
    include __DIR__ . '/../db_connection.php';
} else {
    die("Database connection file not found.");
}

if (!isset($_GET['id'])) {
    die("Vehicle not found.");
}

$car_id = intval($_GET['id']);

// Fetch selected vehicle
$car_sql = "SELECT * FROM cars WHERE id = $car_id";
$car_result = mysqli_query($conn, $car_sql);

if (mysqli_num_rows($car_result) == 0) {
    die("Vehicle not found.");
}

$car = mysqli_fetch_assoc($car_result);

// Image fallback
$image = !empty($car['image_url']) ? "../uploads/" . $car['image_url'] : "../assets/images/no-image.png";

// Fetch similar vehicles (same brand or model)
$similar_sql = "
    SELECT * FROM cars 
    WHERE id != $car_id 
    AND (brand = '{$car['brand']}' OR model = '{$car['model']}') 
    LIMIT 4
";
$similar_result = mysqli_query($conn, $similar_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $car['brand'] . " " . $car['model']; ?> - Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; background:#f7f9fb; margin:0; padding:0; color:#333; }
        .container { max-width:1100px; margin:30px auto; padding:0 15px; }

        .vehicle-details { display:flex; gap:20px; flex-wrap:wrap; }
        .vehicle-details img { width:100%; max-width:480px; border-radius:8px; object-fit:cover; }

        .info-box { background:#fff; padding:20px; border-radius:8px; flex:1; box-shadow:0 2px 6px rgba(0,0,0,0.08); }

        .title { font-size:28px; font-weight:bold; margin-bottom:5px; }
        .sub { font-size:14px; color:#777; margin-bottom:20px; }

        .spec-list p { margin:6px 0; font-size:15px; }

        .price { font-size:22px; font-weight:bold; color:#12b886; margin-top:20px; }

        .btn { padding:12px 18px; display:inline-block; text-decoration:none; background:#3498db; color:#fff; border-radius:6px; font-size:16px; margin-top:18px; }
        .btn.disabled { background:#bbb; cursor:not-allowed; }

        .status-badge { padding:6px 10px; border-radius:6px; color:#fff; font-size:14px; }
        .available { background:#2ecc71; }
        .booked { background:#f1c40f; color:#000; }
        .maintenance { background:#e74c3c; }

        .similar { margin-top:40px; }
        .similar h3 { margin-bottom:10px; }

        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:16px; }
        .card { background:#fff; border-radius:8px; padding:10px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        .card img { width:100%; height:130px; object-fit:cover; border-radius:6px; }
        .card-title { font-weight:bold; margin-top:6px; }
    </style>
</head>
<body>

<div class="container">

    <div class="vehicle-details">
        <img src="<?php echo $image; ?>" alt="Vehicle Image">

        <div class="info-box">
            <div class="title"><?php echo $car['brand'] . " " . $car['model']; ?></div>
            <div class="sub"><?php echo $car['year']; ?> • <?php echo $car['transmission']; ?> • <?php echo $car['capacity']; ?> seats</div>

            <div>
                <span class="status-badge 
                    <?php 
                        echo ($car['status']=="available" || $car['status']=="active") ? "available" : 
                             (($car['status']=="maintenance") ? "maintenance" : "booked"); 
                    ?>">
                    <?php echo ucfirst($car['status']); ?>
                </span>
            </div>

            <div class="spec-list">
                <p><strong>Brand:</strong> <?php echo $car['brand']; ?></p>
                <p><strong>Model:</strong> <?php echo $car['model']; ?></p>
                <p><strong>Year:</strong> <?php echo $car['year']; ?></p>
                <p><strong>Transmission:</strong> <?php echo $car['transmission']; ?></p>
                <p><strong>Fuel Type:</strong> <?php echo $car['fuel_type']; ?></p>
                <p><strong>Capacity:</strong> <?php echo $car['capacity']; ?> seats</p>
            </div>

            <div class="price">KES <?php echo number_format($car['price_per_day']); ?> / day</div>

            <?php if ($car["status"] == "available" || $car["status"] == "active"): ?>
                <a href="rent.php?car_id=<?php echo $car_id; ?>" class="btn">Rent Now</a>
            <?php else: ?>
                <a class="btn disabled">Not Available</a>
            <?php endif; ?>
        </div>
    </div>


    <!-- Similar Vehicles Section -->
    <div class="similar">
        <h3>Similar Vehicles</h3>
        <div class="grid">

            <?php if (mysqli_num_rows($similar_result) == 0): ?>

                <p>No similar vehicles found.</p>

            <?php else: ?>
                <?php while ($s = mysqli_fetch_assoc($similar_result)): ?>
                    <div class="card">
                        <img src="<?php echo !empty($s['image_url']) ? '../uploads/'.$s['image_url'] : '../assets/images/no-image.png'; ?>">
                        <div class="card-title"><?php echo $s['brand']." ".$s['model']; ?></div>
                        <p style="font-size:14px; color:#666;">
                            <?php echo $s['year']; ?> • <?php echo $s['transmission']; ?>
                        </p>
                        <p><strong>KES <?php echo number_format($s['price_per_day']); ?></strong></p>
                        <a href="vehicle.php?id=<?php echo $s['id']; ?>" class="btn" style="padding:6px 10px; font-size:14px;">View</a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>
