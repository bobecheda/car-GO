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
    <h2>Manage Vehicles</h2>

    <a href="add_vehicle.php" 
       style="background:#1abc9c; color:#fff; padding:10px; border-radius:5px; text-decoration:none;">
       + Add New Vehicle
    </a>

    <br><br>

<table border="1" cellpadding="10" cellspacing="0" width="100%" style="background:#fff;">
    <tr style="background:#3498db; color:#fff;">
        <th>ID</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Year</th>
        <th>Price Per Day</th>
        <th>Status</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>

    <?php
    $result = mysqli_query($conn, "SELECT * FROM cars ORDER BY id DESC");

    while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['brand']; ?></td>
            <td><?= $row['model']; ?></td>
            <td><?= $row['year']; ?></td>
            <td><?= $row['price_per_day']; ?></td>
            <td><?= $row['status']; ?></td>
            <td><img src="../uploads/<?= $row['image_url']; ?>" width="80"></td>

            <td>
                <a href="edit_vehicle.php?id=<?= $row['id']; ?>" 
                   class="btn btn-primary btn-sm">Edit</a>

                <a href="delete_vehicle.php?id=<?= $row['id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this vehicle?');">
                   Delete
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>

</div>

<?php include 'footer.php'; ?>
