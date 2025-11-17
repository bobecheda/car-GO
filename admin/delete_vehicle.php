<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID missing.");
}

$id = $_GET['id'];

$sql = "DELETE FROM cars WHERE id = $id";
mysqli_query($conn, $sql);

header("Location: manage_vehicles.php");
exit;
?>
