<?php
// customer/index.php
// Full working dashboard (fixed header/sidebar overlap, welcome name, statistics)

// start session early
session_start();

// Try common locations for your DB connection file. Adjust if needed.
if (file_exists(__DIR__ . '/../config/db.php')) {
    include __DIR__ . '/../config/db.php';
} elseif (file_exists(__DIR__ . '/../db_connection.php')) {
    include __DIR__ . '/../db_connection.php';
} elseif (file_exists(__DIR__ . '/../config/db_connection.php')) {
    include __DIR__ . '/../config/db_connection.php';
} else {
    die('Database connection file not found. Please update the include path in customer/index.php');
}

// Ensure $conn exists
if (!isset($conn) || !$conn) {
    die('Database connection failed. Please check your DB connection file.');
}

// ---------------------------
// Helper: safe GET
// ---------------------------
function get_str_param($conn, $name) {
    return isset($_GET[$name]) ? mysqli_real_escape_string($conn, trim($_GET[$name])) : '';
}
function get_int_param($name, $default = 0) {
    return isset($_GET[$name]) ? (int) $_GET[$name] : $default;
}

// Build filter values from GET (sanitised)
$transmission_filter = get_str_param($conn, 'transmission');
$price_filter = get_str_param($conn, 'price');
$search = get_str_param($conn, 'search');

// Build WHERE clause
$whereClauses = [];

// Only show active/available vehicles by default
$whereClauses[] = "status IN ('available','active')";

// Transmission filter (type)
if ($transmission_filter !== '') {
    // allow only letters/numbers/hyphen/underscore
    $transmission_filter_s = preg_replace('/[^a-z0-9_\- ]/i', '', $transmission_filter);
    $whereClauses[] = "transmission = '". mysqli_real_escape_string($conn, $transmission_filter_s) ."'";
}

// Price ranges: low (<3000), mid (3000-7000), high (>7000)
if ($price_filter !== '') {
    if ($price_filter == 'low') {
        $whereClauses[] = "price_per_day < 3000";
    } elseif ($price_filter == 'mid') {
        $whereClauses[] = "price_per_day BETWEEN 3000 AND 7000";
    } elseif ($price_filter == 'high') {
        $whereClauses[] = "price_per_day > 7000";
    }
}

// Search by brand or model (safe wildcard)
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $whereClauses[] = "(brand LIKE '%{$s}%' OR model LIKE '%{$s}%')";
}

$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get distinct transmissions for filter dropdown
$transmissions = [];
$transQ = "SELECT DISTINCT transmission FROM cars WHERE transmission IS NOT NULL AND transmission <> ''";
if ($transResult = mysqli_query($conn, $transQ)) {
    while ($t = mysqli_fetch_assoc($transResult)) {
        $transmissions[] = $t['transmission'];
    }
}

// Fetch vehicles
$sql = "SELECT * FROM cars $whereSQL ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// ---------------------------
// STATISTICS: fetch booking counts for logged in user
// - Assumes bookings table has columns: user_id, status
// - Status values assumed: pending, active, completed
// Adjust queries if your schema uses different names/values.
// ---------------------------
$total_bookings = 0;
$active_bookings = 0;
$completed_bookings = 0;
$upcoming_bookings = 0;

// Determine user id from session
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

// If we don't have a session name, try to load from session or DB later
$display_name = '';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $display_name = $_SESSION['user_name'];
} elseif (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
    $display_name = $_SESSION['name'];
} elseif (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    $display_name = $_SESSION['username'];
} elseif (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
    $display_name = $_SESSION['email'];
} else {
    // if we have a user_id but no name in session, attempt a DB lookup (users table assumed)
    if ($user_id > 0) {
        $uid = $user_id;
        $uQ = "SELECT COALESCE(name, username, email) AS display_name FROM users WHERE id = $uid LIMIT 1";
        if ($r = @mysqli_query($conn, $uQ)) {
            if ($row = mysqli_fetch_assoc($r)) {
                $display_name = $row['display_name'];
                // store back into session for convenience
                if (!empty($display_name)) {
                    $_SESSION['user_name'] = $display_name;
                }
            }
        }
    }
}

// Booking stats if we have a user_id
if ($user_id > 0) {
    // use defensive @ to avoid page-break on missing table/column; we'll fallback to 0
    // total
    $q1 = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id");
    if ($q1 && $r1 = mysqli_fetch_assoc($q1)) { $total_bookings = (int)$r1['total']; }

    // active
    $q2 = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id AND status = 'active'");
    if ($q2 && $r2 = mysqli_fetch_assoc($q2)) { $active_bookings = (int)$r2['total']; }

    // completed
    $q3 = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id AND status = 'approved'");
    if ($q3 && $r3 = mysqli_fetch_assoc($q3)) { $completed_bookings = (int)$r3['total']; }

    // upcoming (pending / future)
    $q4 = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id AND status = 'pending'");
    if ($q4 && $r4 = mysqli_fetch_assoc($q4)) { $upcoming_bookings = (int)$r4['total']; }
}

// Optionally fetch routes if you have a routes table (graceful fallback)
$routes = [];
$rq = "SELECT origin, `destination`, distance_km FROM routes LIMIT 6";
if ($rres = @mysqli_query($conn, $rq)) {
    while ($rr = mysqli_fetch_assoc($rres)) {
        $routes[] = $rr;
    }
}

// ---------------------------
// End PHP preparation - output HTML
// ---------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Car Rental - Dashboard</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg,var(--bg) 0%, #eef3f8 100%); color:var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

        /* Header */
        .header { position: sticky; height: 60px; top: 0; z-index: 1200; background: linear-gradient(180deg, #f7fbff 0%, var(--surface) 100%); border-bottom:1px solid var(--border); }
        .header-inner { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; max-width:1200px; margin:0 auto; }
        .left { display:flex; align-items:center; gap:10px; }
        .brand { font-weight:700; letter-spacing:.3px; }
        .hamburger { display:inline-flex; flex-direction:column; gap:4px; padding:8px; border-radius:10px; border:1px solid var(--border); background:var(--surface); cursor:pointer; }
        .hamburger span { width:18px; height:2px; background:var(--text); border-radius:2px; }
        .right { display:flex; align-items:center; gap:10px; }
        .avatar { width:36px; height:36px; border-radius:50%; background: linear-gradient(180deg,#e3edf6,#ffffff); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:#2b3e50; font-weight:700; cursor:pointer; overflow:hidden; }

        /* Sidebar (fixed left) */
        .sidebar { position: fixed; left: 0; top: 60px; height: 100vh; width: 240px; background: linear-gradient(180deg, var(--surface) 0%, #f7fbff 100%); border-right:1px solid var(--border); box-shadow: var(--shadow); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index: 1100; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
        .nav { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px; }
        .nav a { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--text); text-decoration:none; border:1px solid var(--border); background: var(--surface); transition: transform .18s ease, box-shadow .18s ease; }
        .nav a:hover { transform: translateX(2px); box-shadow: var(--shadow); }
        .nav a.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); }

        /* Layout - page accounts for sidebar width */
        .page { margin-left: 240px; padding:16px; transition: margin-left .25s ease; max-width:1200px; margin-right:auto; margin-left:260px; }
        body.sidebar-collapsed .page { margin-left: 0; }

        /* Stats */
        .stats { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:16px; margin:16px 0; }
        .stat { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:14px; box-shadow: var(--shadow); }
        .stat .label { color:var(--muted); font-size:12px; }
        .stat .value { font-size:22px; font-weight:700; margin-top:6px; }

        /* Filters & grid */
        .filters { background:var(--surface); padding:12px; border-radius:16px; border:1px solid var(--border); display:flex; gap:12px; align-items:center; margin-bottom:16px; flex-wrap:wrap; box-shadow: var(--shadow); }
        .filters input[type="text"], .filters select { padding:10px 12px; border:1px solid var(--border); border-radius:12px; background: linear-gradient(180deg,#f6f9fc,#ffffff); color:var(--text); }
        .filters .btn { padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg, #59a9de, #3498db); color:#fff; text-decoration:none; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; }
        .card { background:var(--surface); border-radius:16px; padding:12px; box-shadow: var(--shadow); border:1px solid var(--border); }
        .card img { width:100%; height:150px; object-fit:cover; border-radius:10px; }
        .meta { display:flex; justify-content:space-between; margin-top:8px; align-items:center; }
        .brand { font-weight:700; }
        .price { color:var(--accent2); font-weight:700; }
        .btn { display:inline-block; padding:8px 12px; background:var(--accent); color:#fff; border-radius:10px; text-decoration:none; border:1px solid var(--border); }
        .status { padding:4px 8px; border-radius:8px; font-size:13px; color:#fff; }
        .status.available { background:#27ae60; }
        .status.maintenance { background:#e74c3c; }
        .status.booked { background:#f39c12; color:#111; }
        .no-results { padding:30px; text-align:center; color:var(--muted); }

        /* Articles & routes */
        .section-title { margin:18px 0 10px; font-size:18px; font-weight:600; }
        .articles { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:16px; }
        .article { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:14px; box-shadow: var(--shadow); }
        .article h4 { margin:0 0 8px; font-size:15px; }
        .article p { margin:0; color:var(--muted); font-size:13px; }
        .routes { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:16px; }
        .route { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:14px; box-shadow: var(--shadow); }
        .route .top { display:flex; align-items:center; gap:8px; font-weight:600; }
        .route .sub { color:var(--muted); font-size:12px; margin-top:6px; }

        @media (max-width: 900px){ .stats{grid-template-columns: repeat(2,1fr);} .articles{grid-template-columns: repeat(2,1fr);} .routes{grid-template-columns: repeat(2,1fr);} }
        @media (max-width: 768px){
            .page { padding:12px; padding-top: 70px; margin-left:0 !important; }
            .sidebar { transform: translateX(-100%); }
            body.sidebar-open .sidebar { transform: translateX(0); }
            .header-inner { padding:10px; }
        }
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
                <div class="avatar" id="avatar" title="Go to profile">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <aside class="sidebar" id="sidebar">
        <ul class="nav">
            <li><a href="index.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="booking.php"><i class="fa-solid fa-list-check"></i> My Activities</a></li>
            <li><a href="my_bookings.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="page" id="page">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <h1 style="margin:10px 0;">
                Welcome<?php echo $display_name ? ', ' . htmlspecialchars($display_name) : ''; ?>
            </h1>
            <form method="GET" style="display:flex; gap:8px; align-items:center;">
                <input type="text" name="search" placeholder="Search brand or model" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <section class="stats">
            <div class="stat"><div class="label">Total Bookings</div><div class="value"><?php echo (int)$total_bookings; ?></div></div>
            <div class="stat"><div class="label">Active Bookings</div><div class="value"><?php echo (int)$active_bookings; ?></div></div>
            <div class="stat"><div class="label">Completed Bookings</div><div class="value"><?php echo (int)$completed_bookings; ?></div></div>
            <div class="stat"><div class="label">Upcoming Bookings</div><div class="value"><?php echo (int)$upcoming_bookings; ?></div></div>
        </section>

        <div class="filters">
            <form method="GET" id="filterForm" style="display:flex; gap:8px; align-items:center;">
                <!-- Keep search param when filtering -->
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

                <label>
                    Type:
                    <select name="transmission" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Types</option>
                        <?php foreach($transmissions as $tr): $trEsc = htmlspecialchars($tr); ?>
                            <option value="<?php echo $trEsc; ?>" <?php if($transmission_filter==$tr) echo 'selected'; ?>><?php echo $trEsc ? ucfirst($trEsc) : ''; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Price:
                    <select name="price" onchange="document.getElementById('filterForm').submit();">
                        <option value="">Any</option>
                        <option value="low" <?php if($price_filter=='low') echo 'selected'; ?>>Below 3,000</option>
                        <option value="mid" <?php if($price_filter=='mid') echo 'selected'; ?>>3,000 - 7,000</option>
                        <option value="high" <?php if($price_filter=='high') echo 'selected'; ?>>Above 7,000</option>
                    </select>
                </label>

                <a href="index.php" class="btn" style="background: linear-gradient(180deg,#95a5a6,#7f8c8d);">Reset</a>
            </form>
        </div>

        <?php if(!$result || mysqli_num_rows($result) == 0): ?>
            <div class="no-results">No vehicles found matching your filters.</div>
        <?php else: ?>
            <div class="grid">
                <?php while($row = mysqli_fetch_assoc($result)):
                    // Fallbacks for image and display fields
                    $img = !empty($row['image_url']) ? '../uploads/' . $row['image_url'] : '../assets/images/no-image.png';
                    $status = isset($row['status']) ? $row['status'] : 'unknown';
                    $brand_model = htmlspecialchars(trim(($row['brand'] ?? '') . ' ' . ($row['model'] ?? '')));
                    $year = isset($row['year']) ? htmlspecialchars($row['year']) : '—';
                    $trans = isset($row['transmission']) ? htmlspecialchars($row['transmission']) : '—';
                    $capacity = isset($row['capacity']) ? htmlspecialchars($row['capacity']) : '—';
                    $price = isset($row['price_per_day']) ? number_format($row['price_per_day']) : '—';
                    ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo $brand_model; ?>">
                        <div style="margin-top:10px;">
                            <div class="brand"><?php echo $brand_model; ?></div>
                            <div style="font-size:13px; color:#666;"><?php echo $year; ?> • <?php echo $trans; ?> • <?php echo $capacity; ?> seats</div>

                            <div class="meta">
                                <div class="price">KES <?php echo $price; ?></div>
                                <div>
                                    <?php if($status == 'available' || $status == 'active'): ?>
                                        <span class="status available">Available</span>
                                    <?php elseif($status == 'maintenance'): ?>
                                        <span class="status maintenance">Maintenance</span>
                                    <?php else: ?>
                                        <span class="status booked">Booked</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p style="margin-top:10px;">
                                <a href="vehicle.php?id=<?php echo (int)$row['id']; ?>" class="btn">View Details</a>
                                <!-- Quick Rent (link to booking page) -->
                                <?php if($status == 'available' || $status == 'active'): ?>
                                    <a href="rent.php?car_id=<?php echo (int)$row['id']; ?>" class="btn" style="background:#e67e22; margin-left:8px;">Rent Now</a>
                                <?php else: ?>
                                    <button class="btn" style="background:#bdc3c7; cursor:not-allowed; margin-left:8px;" disabled>Not Available</button>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <section>
            <h3 class="section-title">Explore More About Our Fleet</h3>
            <div class="articles">
                <div class="article"><h4>Car Maintenance Tips</h4><p>Keep your ride smooth with regular checkups and tire rotations.</p></div>
                <div class="article"><h4>Fuel-Saving Techniques</h4><p>Drive efficiently: steady speeds and proper tire pressure save fuel.</p></div>
                <div class="article"><h4>Best Travel Practices</h4><p>Plan routes ahead, take breaks, and stay hydrated on long trips.</p></div>
                <div class="article"><h4>Toyota Facts</h4><p>Toyota engines are renowned for reliability and long-term performance.</p></div>
                <div class="article"><h4>Subaru Symmetrical AWD</h4><p>Enhanced stability and traction for challenging road conditions.</p></div>
                <div class="article"><h4>Safety First</h4><p>Check lights, brakes, and seats before every journey.</p></div>
            </div>
        </section>

        <section>
            <h3 class="section-title">Available Travel Routes</h3>
            <div class="routes">
                <?php if(isset($routes) && is_array($routes) && count($routes)>0): ?>
                    <?php foreach($routes as $r): ?>
                        <div class="route">
                            <div class="top"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($r['origin']); ?> → <?php echo htmlspecialchars($r['destination']); ?></div>
                            <div class="sub">Distance: <?php echo isset($r['distance_km']) ? htmlspecialchars($r['distance_km']) : 'Not provided';?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="route"><div class="top"><i class="fa-solid fa-location-dot"></i> No routes available</div><div class="sub">Please check back later.</div></div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        (function(){
            var body = document.body;
            var hamburger = document.getElementById('hamburger');
            var avatar = document.getElementById('avatar');
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
            if(avatar){ avatar.addEventListener('click', function(){ window.location.href = 'profile.php'; }); }
            window.addEventListener('resize', function(){ if(isMobile()){ body.classList.remove('sidebar-collapsed'); } });
        })();
    </script>
</body>
</html>
