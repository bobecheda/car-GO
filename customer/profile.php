<?php
session_start();
include("../config/db.php");

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your profile.");
}

$user_id = $_SESSION['user_id'];

// Handle form submission (update profile)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update_sql = "UPDATE users 
                   SET name='$name', email='$email', phone='$phone' 
                   WHERE id=$user_id";
    if (mysqli_query($conn, $update_sql)) {
        $success_msg = "Profile updated successfully.";
    } else {
        $error_msg = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch current user info
$sql = "SELECT * FROM users WHERE id=$user_id LIMIT 1";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width:600px;">
    <h2 class="mb-4 text-center">My Profile</h2>

    <?php if(isset($success_msg)) { ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php } ?>
    <?php if(isset($error_msg)) { ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php } ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
</div>

</body>
</html>
