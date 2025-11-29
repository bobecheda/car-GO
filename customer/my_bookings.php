<?php
session_start();
include("../config/db.php");

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your bookings.");
}

// ensure we only use an integer user id (prevents accidental SQL injection)
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Fetch all bookings for this user
$sql = "
SELECT b.*, c.brand, c.model
FROM bookings b
JOIN cars c ON b.car_id = c.id
WHERE b.user_id = $user_id
ORDER BY b.start_date DESC
";

$result = mysqli_query($conn, $sql);
// If the query failed, capture the error for debugging (do not expose raw SQL in production)
if ($result === false) {
    error_log("my_bookings query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); --green:#16a085; --red:#e74c3c; --orange:#e67e22; --blue:#3498db; }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg, var(--bg) 0%, #eef3f8 100%); color:var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        /* Header */
        .header { position: sticky; height: 60px; top: 0; z-index: 1200; background: linear-gradient(180deg, #f7fbff 0%, var(--surface) 100%); border-bottom:1px solid var(--border); }
        .header-inner { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; max-width:1200px; margin:0 auto; }
        .left { display:flex; align-items:center; gap:10px; }
        .brand { font-weight:700; letter-spacing:.3px; }
        .hamburger { display:inline-flex; flex-direction:column; gap:4px; padding:8px; border-radius:10px; border:1px solid var(--border); background:var(--surface); cursor:pointer; }
        .hamburger span { width:18px; height:2px; background:var(--text); border-radius:2px; }
        .right { display:flex; align-items:center; gap:10px; }
        .avatar { width:36px; height:36px; border-radius:50%; background: linear-gradient(180deg,#e3edf6,#ffffff); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:#2b3e50; font-weight:700; cursor:pointer; overflow:hidden; }

                .sidebar { position: fixed; left: 0; top: 60px; width: 240px; height: 100vh; background: linear-gradient(180deg, var(--surface) 0%, #f7fbff 100%); border-right:1px solid var(--border); box-shadow: var(--shadow); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index: 900; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
        .nav { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px; }
        .nav a { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--text); text-decoration:none; border:1px solid var(--border); background: var(--surface); transition: transform .18s ease, box-shadow .18s ease; }
        .nav a:hover { transform: translateX(2px); box-shadow: var(--shadow); }
        .nav a.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); }
        /* Layout */
        .page { margin-left:240px; padding:16px; transition: margin-left .25s ease; }
        body.sidebar-collapsed .page { margin-left:0; }
        /* Alerts */
        .alert { margin:10px 0; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#eaf3fb,#f7fbff); color:#2b3e50; box-shadow: var(--shadow); }
        /* Table */
        .table { width:100%; border-collapse: separate; border-spacing:0; background: var(--surface); color: var(--text); border:1px solid var(--border); border-radius:16px; box-shadow: var(--shadow); overflow:hidden; opacity:0; transition: opacity .35s ease; }
        .table thead th { text-align:left; padding:12px 14px; background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); color:#2b3e50; font-weight:600; border-bottom:1px solid var(--border); }
        .table tbody td { padding:12px 14px; border-bottom:1px solid var(--border); vertical-align: middle; }
        .table tbody tr { transition: background .18s ease; }
        .table tbody tr:nth-child(even) { background: rgba(20,40,80,0.03); }
        .table tbody tr:hover { background: rgba(20,40,80,0.06); }
        .status-badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); }
        .status-approved { background: rgba(22,160,133,0.15); color:#0b3f34; }
        .status-pending { background: rgba(230,126,34,0.15); color:#5a3415; }
        .status-rejected { background: rgba(231,76,60,0.15); color:#5e1f1a; }
        .status-completed { background: rgba(52,152,219,0.15); color:#1a3e56; }
        .icon { width:16px; height:16px; display:inline-block; margin-right:6px; vertical-align:middle; }
        .spinner { display:none; width:36px; height:36px; border-radius:50%; border:3px solid #cfe0f2; border-top-color:#3498db; animation: spin 0.8s linear infinite; margin:16px auto; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 768px){ .page{ margin-left:0; padding:12px; } .sidebar { transform: translateX(-100%); } body.sidebar-open .sidebar { transform: translateX(0); } .table thead { display:none; } .table, .table tbody, .table tr, .table td { display:block; width:100%; } .table tbody tr { margin-bottom:12px; border:1px solid var(--border); border-radius:12px; padding:10px; } .table td { border:none; padding:8px 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-inner">
            <div class="left">
                <button class="hamburger" id="hamburger" aria-label="Toggle sidebar"><span></span><span></span><span></span></button>
                <div class="brand">Customer Dashboard</div>
            </div>
            <div class="right">
                <div class="avatar"><i class="fa-solid fa-user"></i></div>
            </div>
        </div>
    </div>
    <aside class="sidebar">
        <ul class="nav">
            <li><a href="index.php"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a></li>
            <li><a href="booking.php"><i class="fa-solid fa-list-check"></i><span>My activities</span></a></li>
            <li><a class="active" href="my_bookings.php"><i class="fa-solid fa-clock-rotate-left"></i><span>History</span></a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a></li>
            <li><a href="../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>
    <main class="page">
        <div class="alert">My Bookings</div>
        <div class="spinner" id="spinner"></div>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $status = isset($row['status']) ? strtolower($row['status']) : '';
                    $badgeClass = 'status-pending';
                    if($status === 'approved') { $badgeClass = 'status-approved'; }
                    elseif($status === 'rejected') { $badgeClass = 'status-rejected'; }
                    elseif($status === 'completed') { $badgeClass = 'status-completed'; }
                    $carName = trim(($row['brand'] ?? '').' '.($row['model'] ?? ''));
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($carName ?: ''); ?></td>
                    <td><?php echo htmlspecialchars($row['start_date'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['end_date'] ?? ''); ?></td>
                    <td><span class="status-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($row['status'] ?? ''); ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert">No bookings found.</div>
        <?php endif; ?>
    </main>
    <script>
    (function(){
        var btn = document.getElementById('hamburger');
        if(btn){
            btn.addEventListener('click', function(){
                if(window.innerWidth <= 768){
                    document.body.classList.toggle('sidebar-open');
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                }
            });
        }
        window.addEventListener('load', function(){
            var t = document.querySelector('.table');
            if(t){ t.style.opacity = '1'; }
        });
    })();
    </script>
</body>
</html>
