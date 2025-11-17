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

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_sql = "UPDATE users SET name='$name', email='$email', phone='$phone', status='$status' WHERE id=$customer_id";
    if(mysqli_query($conn, $update_sql)) {
        header("Location: manage_customers.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch customer details
$sql = "SELECT * FROM users WHERE id=$customer_id LIMIT 1";
$res = mysqli_query($conn, $sql);
if(mysqli_num_rows($res) == 0){
    die("Customer not found.");
}
$customer = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Customer</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($customer['phone']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" <?php if($customer['status']=='active') echo 'selected'; ?>>Active</option>
                <option value="suspended" <?php if($customer['status']=='suspended') echo 'selected'; ?>>Suspended</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Customer</button>
        <a href="manage_customers.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
