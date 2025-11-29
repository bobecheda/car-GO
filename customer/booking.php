<?php
session_start();
require '../config/db.php'; // Adjust path to your db connection file

// -----------------------------------------
// 1. CHECK IF CUSTOMER IS LOGGED IN
// -----------------------------------------


$user_id = $_SESSION['user_id'];

// -----------------------------------------
// 2. FETCH ACTIVITIES FOR LOGGED-IN CUSTOMER
// -----------------------------------------

// Pagination settings (optional)
$limit = 20; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch total count
$count_query = "SELECT COUNT(*) AS total FROM activities WHERE user_id = ?";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$total_activities = $stmt->get_result()->fetch_assoc()['total'];

$total_pages = ceil($total_activities / $limit);

// Fetch activities
$query = "
    SELECT activity_type, description, page_visited, timestamp
    FROM activities 
    WHERE user_id = ?
    ORDER BY timestamp DESC
    LIMIT ?, ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $offset, $limit);
$stmt->execute();
$activities = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Activity Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg, var(--bg) 0%, #eef3f8 100%); color:var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
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
        /* Top nav */
        .topnav { background: var(--surface); border-bottom:1px solid var(--border); }
        .topnav-inner { max-width:1200px; margin:0 auto; display:flex; gap:12px; padding:8px 16px; }
        .topnav a { text-decoration:none; color:var(--text); padding:8px 12px; border-radius:999px; border:1px solid var(--border); background: linear-gradient(180deg,#f7fbff,#ffffff); transition: transform .18s ease, box-shadow .18s ease; }
        .topnav a:hover { transform: translateY(-1px); box-shadow: var(--shadow); }
        .topnav a.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); }
        /* Sidebar */
        .sidebar { position: fixed; left: 0; top: 60px; width: 240px; height: 100vh; background: linear-gradient(180deg, var(--surface) 0%, #f7fbff 100%); border-right:1px solid var(--border); box-shadow: var(--shadow); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index: 900; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
        .nav { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px; }
        .nav a { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--text); text-decoration:none; border:1px solid var(--border); background: var(--surface); transition: transform .18s ease, box-shadow .18s ease; }
        .nav a:hover { transform: translateX(2px); box-shadow: var(--shadow); }
        .nav a.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); }
        /* Layout */
        .page { margin-left:240px; padding:16px; }
        body.sidebar-collapsed .page { margin-left:0; }
        .container { max-width:1200px; margin:0 auto; }
        /* Heading */
        .heading { display:flex; align-items:center; justify-content:space-between; margin:10px 0 14px; }
        .heading-left { display:flex; align-items:center; gap:12px; }
        .heading .iconwrap { width:38px; height:38px; border-radius:12px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); background: linear-gradient(180deg,#eaf3fb,#ffffff); }
        .heading h1 { margin:0; font-size:20px; }
        .heading p { margin:0; color:var(--muted); font-size:12px; }
        /* Card */
        .card { background: var(--surface); border:1px solid var(--border); border-radius:18px; box-shadow: var(--shadow); overflow:hidden; }
        .card-body { padding:14px; }
        /* Table */
        .table { width:100%; border-collapse: separate; border-spacing:0; color: var(--text); }
        .table thead th { text-align:left; padding:12px 14px; background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); color:#2b3e50; font-weight:600; border-bottom:1px solid var(--border); }
        .table tbody td { padding:12px 14px; border-bottom:1px solid var(--border); vertical-align: middle; }
        .table tbody tr { transition: background .18s ease; }
        .table tbody tr:nth-child(even) { background: rgba(20,40,80,0.03); }
        .table tbody tr:hover { background: rgba(20,40,80,0.06); }
        /* Pagination */
        .pagination { display:flex; gap:8px; justify-content:center; align-items:center; padding:12px; }
        .page-btn { display:inline-block; padding:8px 12px; border-radius:999px; border:1px solid var(--border); background: var(--surface); text-decoration:none; color:var(--text); transition: transform .18s ease, box-shadow .18s ease; }
        .page-btn:hover { transform: translateY(-1px); box-shadow: var(--shadow); }
        .page-btn.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); font-weight:700; }
        @media (max-width: 768px){ .page{ margin-left:0; padding:12px; } .sidebar { transform: translateX(-100%); } body.sidebar-open .sidebar { transform: translateX(0); } .topnav-inner{ flex-wrap:wrap; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-inner">
            <div class="left">
                <button class="hamburger" id="hamburger" aria-label="Toggle sidebar"><span></span><span></span><span></span></button>
                <div class="brand">Car-Go Dashboard</div>
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
            <li><a href="booking.php" class="active"><i class="fa-solid fa-list-check"></i> My Activities</a></li>
            <li><a href="my_bookings.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href= "../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="page" id="page">
        <div class="container">
            <div class="heading">
                <div class="heading-left">
                    <div class="iconwrap">🧭</div>
                    <div>
                        <h1>Your Recent Activities</h1>
                        <p>Latest account activity and interactions</p>
                    </div>
                </div>
            </div>

            <?php if ($activities->num_rows > 0): ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><span style="margin-right:6px;">⚡</span>Activity</th>
                                    <th><span style="margin-right:6px;">📝</span>Description</th>
                                    <th><span style="margin-right:6px;">📄</span>Page</th>
                                    <th><span style="margin-right:6px;">⏰</span>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $activities->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['activity_type']); ?></td>
                                        <td><?= htmlspecialchars($row['description']); ?></td>
                                        <td><?= htmlspecialchars($row['page_visited']); ?></td>
                                        <td><?= htmlspecialchars($row['timestamp']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="page-btn <?= ($i == $page ? 'active' : '') ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body" style="text-align:center;">
                        <p style="margin:10px 0; color:var(--muted);">No activities recorded yet.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        (function(){
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
        })();
    </script>
</body>
</html>
