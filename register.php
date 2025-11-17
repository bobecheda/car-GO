<?php
// Start session
session_start();

// Include database connection
include 'config/db.php';

// Initialize variables
$errors = [];
$name = $email = $phone = '';

// Handle form submission
if(isset($_POST['register'])) {
    
    // Get and sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    // Validation
    if(empty($name)) $errors[] = "Name is required";
    if(empty($email)) $errors[] = "Email is required";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if(empty($password)) $errors[] = "Password is required";
    if($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0) $errors[] = "Email already registered";
    
    // If no errors, insert user
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert = mysqli_query($conn, "INSERT INTO users (name, email, phone, password, role) VALUES ('$name','$email','$phone','$hashed_password','customer')");
        
        if($insert) {
            $_SESSION['success'] = "Registration successful! You can now login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Registration - Car Rental System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h2>Customer Registration</h2>

<?php
if(!empty($errors)){
    echo '<ul style="color:red;">';
    foreach($errors as $error){
        echo "<li>$error</li>";
    }
    echo '</ul>';
}
if(isset($_SESSION['success'])){
    echo '<p style="color:green;">'.$_SESSION['success'].'</p>';
    unset($_SESSION['success']);
}
?>

<form action="" method="POST">
    <label>Full Name</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br><br>
    
    <label>Email</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>
    
    <label>Phone</label><br>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>"><br><br>
    
    <label>Password</label><br>
    <input type="password" name="password"><br><br>
    
    <label>Confirm Password</label><br>
    <input type="password" name="confirm_password"><br><br>
    
    <button type="submit" name="register">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
