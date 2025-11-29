<?php
// Start session
session_start();

// Include database connection
include 'config/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/car_rental_system/customer/log_activity.php';

$errors = [];
$email = '';

// Handle form submission
if (isset($_POST['login'])) {

    // Get and sanitize inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validation
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";

    if (empty($errors)) {

        // Check if user exists
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($query) == 1) {

            $user = mysqli_fetch_assoc($query);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // 🔥 Log activity ONLY after successful login
                log_activity(
                    $conn,
                    $_SESSION['user_id'],
                    "login",
                    "You logged into the system",
                    "login"
                );

                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin/dashboard.php");
                    exit;
                } else {
                    header("Location: customer/index.php");
                    exit;
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
        .alert svg { width:18px; height:18px; flex-shrink:0; }
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
            <div class="brand">Car Rental System</div>
            <h1>Welcome back</h1>
            <p>Sign in to continue your booking experience.</p>
        </div>
        <div class="card">
            <div>
                <h2 class="title">Login</h2>
                <div class="subtitle">Secure access to your dashboard</div>
            </div>
            <?php
            if (!empty($errors)) {
                echo '<div class="alert">'
                    .'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>'
                    .'<div><strong>There was a problem</strong><ul style="margin:6px 0 0; padding-left:18px;">';
                foreach ($errors as $error) {
                    echo '<li>'.htmlspecialchars($error).'</li>';
                }
                echo '</ul></div></div>';
            }
            ?>
            <form action="" method="POST" id="loginForm">
                <div>
                    <label>Email</label>
                    <div class="input">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 13.5l-8-6V18h16V7.5l-8 6zm0-3L4 6h16l-8 4.5z"/></svg>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
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
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            <div class="links">
                <span></span>
                <span>Don't have an account? <a href="register.php">Register here</a></span>
            </div>
        </div>
    </div>
</div>
<script>
    (function(){
        var toggle = document.getElementById('togglePw');
        var pw = document.getElementById('password');
        if(toggle && pw){ toggle.addEventListener('click', function(){ var is = pw.type === 'password'; pw.type = is ? 'text' : 'password'; toggle.textContent = is ? 'Hide' : 'Show'; }); }
    })();
</script>
</body>
</html>
