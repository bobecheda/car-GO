<?php
// rent.php — Booking form page

// DB connection file
if (file_exists(__DIR__ . '/../config/db.php')) {
    include __DIR__ . '/../config/db.php';
} else {
    die("Database connection file missing.");
}

// Check if car_id is provided
if (!isset($_GET['car_id'])) {
    die("Car not found.");
}

$car_id = intval($_GET['car_id']);

// Fetch selected car details
$sql = "SELECT * FROM cars WHERE id = $car_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$car = mysqli_fetch_assoc($result);

if (!$car) {
    die("Car not found in database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent <?php echo $car['brand'] . " " . $car['model']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
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
        /* Form Card */
        .container { max-width:820px; margin:0 auto; }
        .card { background: var(--surface); border:1px solid var(--border); border-radius:20px; box-shadow: var(--shadow); overflow:hidden; transform: translateZ(0); animation: fadeUp .45s ease both; }
        .card-header { background: linear-gradient(135deg, #eaf3fb 0%, #f8fbff 100%); padding:16px; border-bottom:1px solid var(--border); }
        .card-header h2 { margin:0; font-size:18px; }
        .card-body { padding:18px; display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
        .full { grid-column: 1 / -1; }
        .form-group { display:flex; flex-direction:column; }
        label { font-size:13px; color:var(--muted); margin-bottom:6px; }
        .input { display:flex; align-items:center; gap:8px; border:1px solid var(--border); background: var(--surface); border-radius:12px; padding:10px 12px; box-shadow: 0 4px 12px rgba(20,40,80,0.05) inset; }
        .input i { color:#6b7a8c; }
        .input input, .input select { flex:1; border:none; outline:none; background:transparent; font-size:14px; color:var(--text); }
        .input input:focus, .input select:focus { box-shadow: 0 0 0 2px rgba(52,152,219,0.25); border-radius:10px; }
        .price-box { background: linear-gradient(180deg,#f7fbff,#ffffff); border:1px solid var(--border); border-radius:16px; padding:14px; box-shadow: var(--shadow); }
        .price-row { display:flex; align-items:center; justify-content:space-between; }
        .price { font-weight:700; color:var(--accent2); }
        .est { font-size:13px; color:var(--muted); }
        .cta { grid-column: 1 / -1; }
        .btn { width:100%; padding:12px 14px; border:none; border-radius:999px; background: linear-gradient(135deg, #3498db 0%, #1abc9c 100%); color:#fff; font-weight:600; font-size:15px; cursor:pointer; transition: transform .18s ease, box-shadow .18s ease; }
        .btn:hover { transform: scale(1.02); box-shadow: 0 16px 40px rgba(20,40,80,0.18); }
        .toast { position:fixed; right:16px; bottom:16px; background:var(--surface); border:1px solid var(--border); border-radius:12px; box-shadow: var(--shadow); padding:10px 12px; color:var(--text); display:none; }
        @keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
        @media (max-width: 768px){ .page{ margin-left:0; padding:12px; } .sidebar { transform: translateX(-100%); } body.sidebar-open .sidebar { transform: translateX(0); } .card-body{ grid-template-columns:1fr; } }
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
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="page" id="page">

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Complete Your Booking</h2>
        </div>
        <div class="card-body">
            <div class="full" style="margin-bottom:6px;">
                <div style="font-weight:700; font-size:18px;">Book: <?php echo $car['brand'] . " " . $car['model']; ?></div>
                <div style="font-size:12px; color:var(--muted);">Seamless experience • Secure checkout</div>
            </div>
            <form action="process_booking.php" method="POST" class="full" id="rentForm">
                <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">

                <div class="form-group">
                    <label>Your Full Name</label>
                    <div class="input"><i class="fa-regular fa-user"></i><input type="text" name="customer_name" required></div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input"><i class="fa-regular fa-envelope"></i><input type="email" name="customer_email" required></div>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input"><i class="fa-solid fa-phone"></i><input type="text" name="customer_phone" required></div>
                </div>
                <div class="form-group">
                    <label>Pickup Date</label>
                    <div class="input"><i class="fa-regular fa-calendar"></i><input type="date" name="pickup_date" id="pickup" required></div>
                </div>
                <div class="form-group">
                    <label>Return Date</label>
                    <div class="input"><i class="fa-regular fa-calendar-check"></i><input type="date" name="return_date" id="return" required></div>
                </div>
                <div class="form-group full">
                    <label>Payment Method</label>
                    <div class="input"><i class="fa-solid fa-credit-card"></i>
                        <select name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="card">Card Payment</option>
                        </select>
                    </div>
                </div>

                <div class="price-box full">
                    <div class="price-row">
                        <div>Price per day</div>
                        <div class="price">KES <?php echo number_format($car['price_per_day']); ?></div>
                    </div>
                    <div class="price-row" style="margin-top:8px;">
                        <div class="est">Estimated total (client-side)</div>
                        <div id="estimate" class="price">—</div>
                    </div>
                </div>

                <div class="cta">
                    <button class="btn">Confirm Booking</button>
                </div>
            </form>
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
            // Date constraints and estimate
            var pickup = document.getElementById('pickup');
            var rtn = document.getElementById('return');
            var est = document.getElementById('estimate');
            var pricePerDay = <?php echo (int)$car['price_per_day']; ?>;
            function todayStr(){ var d=new Date(), m=('0'+(d.getMonth()+1)).slice(-2), day=('0'+d.getDate()).slice(-2); return d.getFullYear()+'-'+m+'-'+day; }
            function diffDays(a,b){ var da=new Date(a), db=new Date(b); var ms=db-da; var days=Math.max(0, Math.ceil(ms/86400000)); return days; }
            function updateEstimate(){ var pd=pickup && pickup.value; var rd=rtn && rtn.value; if(pd && rd){ var days=diffDays(pd, rd); if(days>0){ est.textContent = 'KES ' + (days*pricePerDay).toLocaleString(); } else { est.textContent = '—'; showToast('Return date must be after pickup date'); } } else { est.textContent = '—'; } }
            function showToast(text){ var t=document.getElementById('toast'); if(!t) return; t.textContent=text||''; t.style.display=text?'block':'none'; if(text){ setTimeout(function(){ t.style.display='none'; }, 3000); } }
            if(pickup){ pickup.min = todayStr(); pickup.addEventListener('change', function(){ if(rtn){ rtn.min = pickup.value; if(rtn.value && rtn.value < pickup.value){ rtn.value = pickup.value; showToast('Return date adjusted to pickup date'); } } updateEstimate(); }); }
            if(rtn){ rtn.addEventListener('change', updateEstimate); }
            updateEstimate();
        })();
    </script>
</body>
</html>
