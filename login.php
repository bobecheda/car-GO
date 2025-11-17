<?php
// Start session
session_start();

// Include database connection
include 'config/db.php';

$errors = [];
$email = '';

// Handle form submission
if(isset($_POST['login'])){
    // Get and sanitize inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Validation
    if(empty($email)) $errors[] = "Email is required";
    if(empty($password)) $errors[] = "Password is required";
    
    if(empty($errors)){
        // Check if user exists
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($query) == 1){
            $user = mysqli_fetch_assoc($query);
            // Verify password
            if(password_verify($password, $user['password'])){
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                // Use project-relative paths (avoid leading slash) so redirects work when the app is in a subfolder
                if($user['role'] == 'admin'){
                    header("Location: admin/dashboard.php");
                    exit();
                } else {
                    header("Location: customer/index.php");
                    exit();
                }
            } else {
                $errors[] = "Incorrect password";
            }
        } else {
            $errors[] = "Email not registered";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Car Rental System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h2>Login</h2>

<?php
if(!empty($errors)){
    echo '<ul style="color:red;">';
    foreach($errors as $error){
        echo "<li>$error</li>";
    }
    echo '</ul>';
}
?>

<form action="" method="POST">
    <label>Email</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>
    
    <label>Password</label><br>
    <input type="password" name="password"><br><br>
    
    <button type="submit" name="login">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>
