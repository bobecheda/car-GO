<?php
// Include activity logger (use __DIR__ to reference this file's directory)
include __DIR__ . '/log_activity.php';

// Ensure a session is started so we can read the logged-in user id when logging
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// vehicle.php - Customer vehicle detail page


// DB connection — try multiple paths
if (file_exists(__DIR__ . '/../config/db.php')) {
    include __DIR__ . '/../config/db.php';
} elseif (file_exists(__DIR__ . '/../config/db_connection.php')) {
    include __DIR__ . '/../config/db_connection.php';
} elseif (file_exists(__DIR__ . '/../db_connection.php')) {
    include __DIR__ . '/../db_connection.php';
} else {
    die("Database connection file not found.");
}

if (!isset($_GET['id'])) {
    die("Vehicle not found.");
}

$car_id = intval($_GET['id']);

// Fetch selected vehicle
$car_sql = "SELECT * FROM cars WHERE id = $car_id";
$car_result = mysqli_query($conn, $car_sql);

if (mysqli_num_rows($car_result) == 0) {
    die("Vehicle not found.");
}

$car = mysqli_fetch_assoc($car_result);

// Image fallback
$image = !empty($car['image_url']) ? "../uploads/" . $car['image_url'] : "../assets/images/no-image.png";

// Fetch similar vehicles (same brand or model)
$similar_sql = "
    SELECT * FROM cars 
    WHERE id != $car_id 
    AND (brand = '{$car['brand']}' OR model = '{$car['model']}') 
    LIMIT 4
";
$similar_result = mysqli_query($conn, $similar_sql);

// Prepare values for logging: make sure user id and car name are defined
$log_user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$car_name = isset($car['brand'], $car['model']) ? $car['brand'] . ' ' . $car['model'] : 'vehicle#' . $car_id;
log_activity($conn, $log_user_id, "view_vehicle", "Viewed vehicle: $car_name", "vehicle.php?id=$car_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $car['brand'] . " " . $car['model']; ?> - Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
        * { box-sizing:border-box; }
        body { font-family: "Segoe UI", Roboto, Arial, sans-serif; background: linear-gradient(180deg,var(--bg) 0%, #eef3f8 100%); margin:0; color:var(--text); }
        /* Header */
        .header { position:sticky; height:60px; top:0; z-index:1200; background: linear-gradient(180deg, #f7fbff 0%, var(--surface) 100%); border-bottom:1px solid var(--border); }
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
        .container { max-width:1100px; margin:0 auto; padding:0 15px; }

        .vehicle-details { display:flex; gap:20px; flex-wrap:wrap; }
        .vehicle-details img { width:100%; max-width:480px; border-radius:12px; object-fit:cover; box-shadow: var(--shadow); transform: translateZ(0); animation: fadeUp .45s ease both; }

        .info-box { background:var(--surface); padding:20px; border-radius:16px; flex:1; box-shadow: var(--shadow); border:1px solid var(--border); animation: fadeUp .55s ease both; }

        .title { font-size:26px; font-weight:700; margin-bottom:6px; }
        .sub { font-size:13px; color:var(--muted); margin-bottom:16px; }

        .spec-list p { margin:6px 0; font-size:15px; }

        .price { font-size:20px; font-weight:700; color:var(--accent2); margin-top:14px; }

        .btn { padding:10px 14px; display:inline-block; text-decoration:none; background:var(--accent); color:#fff; border-radius:12px; font-size:14px; margin-top:14px; border:1px solid var(--border); transition: transform .18s ease, box-shadow .18s ease; }
        .btn:hover { transform: scale(1.02); box-shadow: var(--shadow); }
        .btn.disabled { background:#bbb; cursor:not-allowed; }

        .status-badge { padding:6px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); }
        .available { background: rgba(46,204,113,0.18); color:#0f3b23; }
        .booked { background: rgba(241,196,15,0.18); color:#5a4d0f; }
        .maintenance { background: rgba(231,76,60,0.18); color:#5a1f1f; }

        .similar { margin-top:28px; }
        .similar h3 { margin-bottom:10px; font-size:18px; }

        .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:12px; justify-items:center; align-items:start; }
        .card { background:var(--surface); border-radius:16px; padding:10px; box-shadow: var(--shadow); border:1px solid var(--border); width:100%; max-width:280px; transform: translateZ(0); transition: transform .18s ease, box-shadow .18s ease; animation: fadeUp .45s ease both; }
        .card:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 16px 40px rgba(20,40,80,0.18); }
        .card img { width:100%; height:120px; object-fit:cover; border-radius:10px; }
        .card-title { font-weight:700; margin-top:8px; }
        .toast { position:fixed; right:16px; bottom:16px; background:var(--surface); border:1px solid var(--border); border-radius:12px; box-shadow: var(--shadow); padding:10px 12px; color:var(--text); display:none; }
        @keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
        @media (max-width: 1024px){ .grid{ grid-template-columns: repeat(3,1fr); } }
        @media (max-width: 768px){ .grid{ grid-template-columns: repeat(2,1fr); } .page{ margin-left:0; padding:12px; } .sidebar { transform: translateX(-100%); } body.sidebar-open .sidebar { transform: translateX(0); } }
        @media (max-width: 520px){ .grid{ grid-template-columns: 1fr; } }
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
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="page" id="page">

<div class="container">

    <div class="vehicle-details">
        <img src="<?php echo $image; ?>" alt="Vehicle Image">

        <div class="info-box">
            <div class="title"><?php echo $car['brand'] . " " . $car['model']; ?></div>
            <div class="sub"><?php echo $car['year']; ?> • <?php echo $car['transmission']; ?> • <?php echo $car['capacity']; ?> seats</div>

            <div>
                <span class="status-badge 
                    <?php 
                        echo ($car['status']=="available" || $car['status']=="active") ? "available" : 
                             (($car['status']=="maintenance") ? "maintenance" : "booked"); 
                    ?>">
                    <?php echo ucfirst($car['status']); ?>
                </span>
            </div>

            <div class="spec-list">
                <p><strong>Brand:</strong> <?php echo $car['brand']; ?></p>
                <p><strong>Model:</strong> <?php echo $car['model']; ?></p>
                <p><strong>Year:</strong> <?php echo $car['year']; ?></p>
                <p><strong>Transmission:</strong> <?php echo $car['transmission']; ?></p>
                <p><strong>Fuel Type:</strong> <?php echo $car['fuel_type']; ?></p>
                <p><strong>Capacity:</strong> <?php echo $car['capacity']; ?> seats</p>
            </div>

            <div class="price">KES <?php echo number_format($car['price_per_day']); ?> / day</div>

            <?php if ($car["status"] == "available" || $car["status"] == "active"): ?>
                <a href="rent.php?car_id=<?php echo $car_id; ?>" class="btn">Rent Now</a>
            <?php else: ?>
                <a class="btn disabled">Not Available</a>
            <?php endif; ?>
        </div>
    </div>


    <!-- Similar Vehicles Section -->
    <div class="similar">
        <h3>Similar Vehicles</h3>
        <div class="grid">

            <?php if (mysqli_num_rows($similar_result) == 0): ?>

                <p>No similar vehicles found.</p>

            <?php else: ?>
                <?php while ($s = mysqli_fetch_assoc($similar_result)): ?>
                    <div class="card">
                        <img src="<?php echo !empty($s['image_url']) ? '../uploads/'.$s['image_url'] : '../assets/images/no-image.png'; ?>">
                        <div class="card-title"><?php echo $s['brand']." ".$s['model']; ?></div>
                        <p style="font-size:14px; color:#666;">
                            <?php echo $s['year']; ?> • <?php echo $s['transmission']; ?>
                        </p>
                        <p><strong>KES <?php echo number_format($s['price_per_day']); ?></strong></p>
                        <a href="vehicle.php?id=<?php echo $s['id']; ?>" class="btn" style="padding:6px 10px; font-size:14px;">View</a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>

    </div>
    </main>

    <div class="toast" id="toast"></div>
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
            function showToast(text){ var t = document.getElementById('toast'); if(!t) return; t.textContent = text || ''; t.style.display = text ? 'block' : 'none'; if(text){ setTimeout(function(){ t.style.display='none'; }, 3500); } }
            // If future session messages exist, echo them into a data attr and show
        })();
    </script>
</body>
</html>
