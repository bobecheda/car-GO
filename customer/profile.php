<?php
session_start();
include("../config/db.php");
include 'log_activity.php';

log_activity($conn, $_SESSION['user_id'], "profile_update", "Updated profile details", "profile.php");

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --muted:#6b7a8c; --text:#1b2a3a; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg,#f5f7fa 0%, #eaf1f8 100%); color:var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        /* Header */
        .header { position: sticky; height:60px; top:0; z-index:1200; background: linear-gradient(180deg, #f7fbff 0%, var(--surface) 100%); border-bottom:1px solid var(--border); }
        .header-inner { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; max-width:1200px; margin:0 auto; }
        .left { display:flex; align-items:center; gap:10px; }
        .brand { font-weight:700; letter-spacing:.3px; }
        .hamburger { display:inline-flex; flex-direction:column; gap:4px; padding:8px; border-radius:10px; border:1px solid var(--border); background:var(--surface); cursor:pointer; }
        .hamburger span { width:18px; height:2px; background:var(--text); border-radius:2px; }
        .right { display:flex; align-items:center; gap:10px; }
        .header-avatar { width:36px; height:36px; border-radius:50%; background: linear-gradient(180deg,#e3edf6,#ffffff); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; overflow:hidden; cursor:pointer; }
        .header-avatar svg { width:22px; height:22px; fill:#6b7a8c; }
        /* Sidebar */
        .sidebar { position: fixed; left: 0; top: 60px; width: 240px; height: 100vh; background: linear-gradient(180deg, var(--surface) 0%, #f7fbff 100%); border-right:1px solid var(--border); box-shadow: var(--shadow); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index: 900; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
        .nav { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px; }
        .nav a { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--text); text-decoration:none; border:1px solid var(--border); background: var(--surface); transition: transform .18s ease, box-shadow .18s ease; }
        .nav a:hover { transform: translateX(2px); box-shadow: var(--shadow); }
        .nav a.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); }
        /* Layout */
        .page { margin-left:240px; padding:16px; transition: margin-left .25s ease; }
        body.sidebar-collapsed .page { margin-left:0; }
        .profile-page { max-width:920px; margin:0 auto; padding:0 16px; }
        .hero { background: linear-gradient(135deg, #eaf3fb 0%, #f8fbff 100%); border:1px solid var(--border); border-radius:18px; padding:18px; box-shadow: var(--shadow); display:flex; align-items:center; gap:16px; }
        .avatar { width:64px; height:64px; border-radius:50%; background: linear-gradient(180deg,#e3edf6,#ffffff); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:700; color:#2b3e50; box-shadow: 0 6px 18px rgba(0,0,0,0.06); overflow:hidden; }
        .avatar svg { width:36px; height:36px; fill:#6b7a8c; }
        .hero-text { display:flex; flex-direction:column; }
        .hero-title { margin:0; font-size:20px; }
        .hero-sub { margin:2px 0 0; color:var(--muted); font-size:13px; }
        .grid { display:grid; grid-template-columns: 1fr; gap:16px; margin-top:16px; }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:18px; box-shadow: var(--shadow); padding:16px; }
        .card-title { margin:0 0 12px; font-size:16px; font-weight:600; letter-spacing:.3px; }
        .form { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; }
        .form .full { grid-column: 1 / -1; }
        .label { font-weight:600; font-size:13px; color:#41566e; }
        .input { width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#f6f9fc,#ffffff); color:var(--text); transition: box-shadow .2s ease, border-color .2s ease; }
        .input:focus { outline:none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(52,152,219,0.18); }
        .actions { display:flex; justify-content:flex-end; margin-top:6px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:12px; border:1px solid var(--border); text-decoration:none; cursor:pointer; transition: transform .18s ease, box-shadow .18s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,0.15); }
        .btn-primary { background: linear-gradient(180deg, #59a9de 0%, #3498db 100%); color:#fff; }
        .btn-secondary { background: linear-gradient(180deg, #aee6d9 0%, #1abc9c 100%); color:#0e2b37; }
        .alert { display:block; margin:12px 0; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#eaf3fb,#f7fbff); color:#2b3e50; }
        .alert.success { border-color:#b7e0ff; background: linear-gradient(180deg,#e7f3ff,#f3f9ff); color:#215076; }
        .alert.error { border-color:#ffd2ce; background: linear-gradient(180deg,#fff0ee,#fff8f7); color:#7a2e2a; }
        .pw { position:relative; }
        .toggle { position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--muted); cursor:pointer; }
        @keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
        .card { animation: fadeUp .45s ease both; }
        @media (max-width: 768px){ .form { grid-template-columns: 1fr; } .hero { align-items:flex-start; } .page { margin-left:0; padding:12px; } .sidebar { transform: translateX(-100%); } body.sidebar-open .sidebar { transform: translateX(0); } }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-inner">
            <div class="left">
                <button class="hamburger" id="hamburger" aria-label="Toggle sidebar"><span></span><span></span><span></span></button>
                <div class="brand">Car-GO Dashboard</div>
            </div>
            <div class="right">
                <div class="header-avatar" id="headerAvatar" title="Go to profile">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>
                </div>
            </div>
        </div>
    </div>
    <aside class="sidebar" id="sidebar">
        <ul class="nav">
            <li><a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="booking.php"><i class="fa-solid fa-list-check"></i> My Activities</a></li>
            <li><a href="my_bookings.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
            <li><a href="profile.php" class="active"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

<main class="page" id="page">
<div class="profile-page">
    <section class="hero">
        <div class="avatar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg></div>
        <div class="hero-text">
            <h2 class="hero-title">Your Profile</h2>
            <div class="hero-sub">Manage your account • <?php echo htmlspecialchars($user['email']); ?></div>
        </div>
    </section>

    <?php if(isset($success_msg)) { ?>
        <div class="alert success"><?php echo $success_msg; ?></div>
    <?php } ?>
    <?php if(isset($error_msg)) { ?>
        <div class="alert error"><?php echo $error_msg; ?></div>
    <?php } ?>

    <section class="card">
        <h3 class="card-title">Personal Information</h3>
        <form action="" method="POST" id="profileForm" class="form">
            <div class="full">
                <label class="label">Full Name</label>
                <input type="text" name="name" class="input" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="full">
                <label class="label">Email Address</label>
                <input type="email" name="email" class="input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="full">
                <label class="label">Phone Number</label>
                <input type="text" name="phone" class="input" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="full actions">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </section>

    <section class="card">
        <h3 class="card-title">Change Password</h3>
        <div class="form">
            <div class="full pw">
                <label class="label">New Password</label>
                <input type="password" class="input" id="newPassword" placeholder="Enter new password">
                <span class="toggle" data-target="newPassword">Show</span>
            </div>
            <div class="full pw">
                <label class="label">Confirm Password</label>
                <input type="password" class="input" id="confirmPassword" placeholder="Confirm new password">
                <span class="toggle" data-target="confirmPassword">Show</span>
            </div>
            <div class="full actions">
                <button type="button" class="btn btn-secondary" id="changePwBtn">Change Password</button>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var body = document.body;
    var hamburger = document.getElementById('hamburger');
    var headerAvatar = document.getElementById('headerAvatar');
    function isMobile(){ return window.matchMedia('(max-width: 768px)').matches; }
    if(hamburger){
        hamburger.addEventListener('click', function(){
            if(isMobile()){
                body.classList.toggle('sidebar-open');
            } else {
                body.classList.toggle('sidebar-collapsed');
            }
        });
    }
    if(headerAvatar){ headerAvatar.addEventListener('click', function(){ window.location.href = 'profile.php'; }); }
    var form = document.getElementById('profileForm');
    function isEmail(v){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v||'').trim()); }
    if(form){
        form.addEventListener('submit', function(e){
            var name = form.querySelector('[name="name"]').value;
            var email = form.querySelector('[name="email"]').value;
            if(!String(name||'').trim() || !isEmail(email)){
                e.preventDefault();
                alert('Please provide a valid name and email address.');
                return false;
            }
        });
    }
    var toggles = document.querySelectorAll('.toggle');
    toggles.forEach(function(t){
        t.addEventListener('click', function(){
            var id = t.getAttribute('data-target');
            var input = document.getElementById(id);
            if(input){ input.type = input.type === 'password' ? 'text' : 'password'; t.textContent = input.type === 'password' ? 'Show' : 'Hide'; }
        });
    });
    var changeBtn = document.getElementById('changePwBtn');
    if(changeBtn){ changeBtn.addEventListener('click', function(){ alert('This is a visual component. Please contact support to change your password.'); }); }
});
</script>

</main>
</body>
</html>
