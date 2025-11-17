<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("Vehicle ID missing.");
}
$id = $_GET['id'];

$query = "SELECT * FROM cars WHERE id = $id";
$result = mysqli_query($conn, $query);
$vehicle = mysqli_fetch_assoc($result);

if (!$vehicle) {
    die("Vehicle not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>Edit Vehicle</h2>

<form action="process_edit_vehicle.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">

    Plate Number: <input type="text" name="plate_number" value="<?php echo $vehicle['plate_number']; ?>" required><br><br>
    Model: <input type="text" name="model" value="<?php echo $vehicle['model']; ?>" required><br><br>
    Brand: <input type="text" name="brand" value="<?php echo $vehicle['brand']; ?>" required><br><br>
    Year: <input type="number" name="year" value="<?php echo $vehicle['year']; ?>" required><br><br>
    Capacity: <input type="number" name="capacity" value="<?php echo $vehicle['capacity']; ?>" required><br><br>
    Transmission: <input type="text" name="transmission" value="<?php echo $vehicle['transmission']; ?>" required><br><br>
    Fuel Type: <input type="text" name="fuel_type" value="<?php echo $vehicle['fuel_type']; ?>" required><br><br>
    Price/Day: <input type="number" name="price_per_day" value="<?php echo $vehicle['price_per_day']; ?>" required><br><br>
    Status:
    <select name="status">
        <option value="active" <?php if($vehicle['status']=="active") echo "selected"; ?>>Active</option>
        <option value="inactive" <?php if($vehicle['status']=="inactive") echo "selected"; ?>>Inactive</option>
        <option value="maintenance" <?php if($vehicle['status']=="maintenance") echo "selected"; ?>>Maintenance</option>
        <option value="booked" <?php if($vehicle['status']=="booked") echo "selected"; ?>>booked</option>
    </select><br><br>

    Current Image: <br>
    <img src="uploads/<?php echo $vehicle['image_url']; ?>" width="120"><br><br>

    Upload New Image: <input type="file" name="image"><br><br>

    <button type="submit">Update Vehicle</button>
</form>

</body>
</html>
