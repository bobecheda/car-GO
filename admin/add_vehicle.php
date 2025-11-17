<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';
include 'header.php';
include 'sidebar.php';
?>

<div style="margin-left:220px; padding:20px;">
    <h2>Add New Vehicle</h2>

    <form action="process_add_vehicle.php" method="POST" enctype="multipart/form-data"
          style="max-width:600px; background:#fff; padding:20px; border-radius:8px;">

        <label>Brand:</label>
        <input type="text" name="brand" required class="input">

        <label>Model:</label>
        <input type="text" name="model" required class="input">

        <label>Year:</label>
        <input type="number" name="year" required class="input">

        <label>Price Per Day:</label>
        <input type="number" step="0.01" name="price_per_day" required class="input">

        <label>Status:</label>
        <select name="status" class="input">
            <option value="available">Available</option>
            <option value="maintenance">Maintenance</option>
        </select>

        <label>Vehicle Image:</label>
        <input type="file" name="image" accept="image/*" required class="input">

        <button type="submit" style="background:#1abc9c; color:#fff; padding:10px 20px; border:none; margin-top:10px; border-radius:5px;">
            Add Vehicle
        </button>
    </form>
</div>

<style>
.input{
    width:100%;
    padding:8px;
    margin-bottom:15px;
    border-radius:5px;
    border:1px solid #ccc;
}
</style>

<?php include 'footer.php'; ?>
