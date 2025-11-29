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
    <link rel="icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg, var(--bg) 0%, #eef3f8 100%); color:var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .auth-wrap { width:100%; max-width:980px; padding:16px; animation: fadeIn .45s ease both; }
        .auth { display:grid; grid-template-columns: 1.1fr 1fr; gap:0; border:1px solid var(--border); border-radius:20px; box-shadow: var(--shadow); overflow:hidden; background: var(--surface); }
        .visual { background: radial-gradient(1200px 600px at 0% 0%, #eaf3fb 0%, #f8fbff 50%, #ffffff 100%); padding:26px; display:flex; flex-direction:column; justify-content:center; }
        .visual h1 { margin:0 0 8px; font-size:28px; font-weight:800; letter-spacing:.3px; }
        .visual p { margin:0; color:var(--muted); font-size:13px; }
        .card { padding:26px; display:flex; flex-direction:column; gap:14px; }
        .brand { font-weight:700; font-size:16px; color:#2b3e50; }
        .title { margin:0; font-size:22px; font-weight:700; }
        .subtitle { margin-top:2px; color:var(--muted); font-size:12px; }
        .alert { display:flex; gap:10px; padding:10px 12px; border-radius:12px; border:1px solid #f5d2d2; background: linear-gradient(180deg,#fff5f5,#fffefe); color:#7a2c2c; box-shadow: 0 8px 24px rgba(255,0,0,0.08); }
        .success { display:flex; gap:10px; padding:10px 12px; border-radius:12px; border:1px solid #cfe9da; background: linear-gradient(180deg,#f5fffa,#ffffff); color:#1b5136; box-shadow: var(--shadow); }
        form { display:flex; flex-direction:column; gap:12px; }
        label { font-size:12px; color:var(--muted); }
        .input { display:flex; align-items:center; gap:10px; border:1px solid var(--border); background: var(--surface); border-radius:12px; padding:10px 12px; box-shadow: 0 4px 12px rgba(20,40,80,0.05) inset; }
        .input svg { width:16px; height:16px; fill:#6b7a8c; }
        .input input { flex:1; border:none; outline:none; background:transparent; font-size:14px; color:var(--text); }
        .toggle { cursor:pointer; color:#6b7a8c; font-size:12px; }
        .btn { width:100%; padding:12px 14px; border:none; border-radius:999px; background: linear-gradient(135deg, #3498db 0%, #1abc9c 100%); color:#fff; font-weight:600; font-size:15px; cursor:pointer; transition: transform .18s ease, box-shadow .18s ease; }
        .btn:hover { transform: scale(1.02); box-shadow: 0 16px 40px rgba(20,40,80,0.18); }
        .links { display:flex; justify-content:space-between; align-items:center; font-size:12px; }
        .links a { color:var(--accent); text-decoration:none; }
        .links a:hover { text-decoration:underline; }
        @media (max-width: 900px){ .auth{ grid-template-columns: 1fr; } .visual{ padding:22px; } }
        @keyframes fadeIn { from{ opacity:0; transform: translateY(6px);} to{ opacity:1; transform: translateY(0);} }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth">
        <div class="visual">
            <div class="brand">Car-GO System</div>
            <h1>Create your account</h1>
            <p>Join and start booking premium vehicles.</p>
        </div>
        <div class="card">
            <div>
                <h2 class="title">Register to get started</h2>
                <div class="subtitle">It only takes a minute</div>
            </div>
            <?php
            if(!empty($errors)){
                echo '<div class="alert">'
                    .'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>'
                    .'<div><strong>Please fix the following</strong><ul style="margin:6px 0 0; padding-left:18px;">';
                foreach($errors as $error){
                    echo '<li>'.htmlspecialchars($error).'</li>';
                }
                echo '</ul></div></div>';
            }
            if(isset($_SESSION['success'])){
                echo '<div class="success">'
                    .'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17l-3.5-3.5L4 14.17l5 5 12-12-1.41-1.41z"/></svg>'
                    .'<div>'.htmlspecialchars($_SESSION['success']).'</div>'
                    .'</div>';
                unset($_SESSION['success']);
            }
            ?>
            <form action="" method="POST" id="registerForm">
                <div>
                    <label>Full Name</label>
                    <div class="input">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                </div>
                <div>
                    <label>Email</label>
                    <div class="input">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 13.5l-8-6V18h16V7.5l-8 6zm0-3L4 6h16l-8 4.5z"/></svg>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                </div>
                <div>
                    <label>Phone</label>
                    <div class="input">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.06-.24c1.16.39 2.41.6 3.53.6a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h2.46a1 1 0 0 1 1 1c0 1.12.21 2.37.6 3.53a1 1 0 0 1-.24 1.06l-2.2 2.2z"/></svg>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                    </div>
                </div>
                <div>
                    <label>Password</label>
                    <div class="input">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 0 0 2-2v-3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h1v3a2 2 0 0 0 2 2h2zm-3-8a3 3 0 1 1 6 0v3H9V9z"/></svg>
                        <input type="password" name="password" id="password" required>
                        <span class="toggle" id="togglePw">Show</span>
                    </div>
                </div>
                <div>
                    <label>Confirm Password</label>
                    <div class="input">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 0 0 2-2v-3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h1v3a2 2 0 0 0 2 2h2zm-3-8a3 3 0 1 1 6 0v3H9V9z"/></svg>
                        <input type="password" name="confirm_password" id="confirm" required>
                        <span class="toggle" id="toggleConfirm">Show</span>
                    </div>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
            <div class="links">
                <span></span>
                <span>Already have an account? <a href="login.php">Login</a></span>
            </div>
        </div>
    </div>
</div>
<script>
    (function(){
        var togglePw = document.getElementById('togglePw');
        var pw = document.getElementById('password');
        if(togglePw && pw){ togglePw.addEventListener('click', function(){ var is = pw.type === 'password'; pw.type = is ? 'text' : 'password'; togglePw.textContent = is ? 'Hide' : 'Show'; }); }
        var toggleConfirm = document.getElementById('toggleConfirm');
        var cf = document.getElementById('confirm');
        if(toggleConfirm && cf){ toggleConfirm.addEventListener('click', function(){ var is = cf.type === 'password'; cf.type = is ? 'text' : 'password'; toggleConfirm.textContent = is ? 'Hide' : 'Show'; }); }
    })();
</script>
</body>
</html>
