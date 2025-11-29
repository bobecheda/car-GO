<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Booking Successful</title>
<style>
    :root { --bg:#f5f7fa; --surface:#ffffff; --text:#1b2a3a; --muted:#6b7a8c; --border:#e3edf6; --accent:#3498db; --accent2:#1abc9c; --shadow:0 12px 30px rgba(20,40,80,0.12); }
    * { box-sizing:border-box; }
    body { margin:0; background: linear-gradient(180deg, var(--bg) 0%, #eef3f8 100%); color:var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    .header { position: sticky; height:60px; top:0; z-index:1200; background: linear-gradient(180deg, #f7fbff 0%, var(--surface) 100%); border-bottom:1px solid var(--border); }
    .header-inner { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; max-width:1200px; margin:0 auto; }
    .left { display:flex; align-items:center; gap:10px; }
    .brand { font-weight:700; letter-spacing:.3px; }
    .hamburger { display:inline-flex; flex-direction:column; gap:4px; padding:8px; border-radius:10px; border:1px solid var(--border); background:var(--surface); cursor:pointer; }
    .hamburger span { width:18px; height:2px; background:var(--text); border-radius:2px; }
    .right { display:flex; align-items:center; gap:10px; }
    .header-avatar { width:36px; height:36px; border-radius:50%; background: linear-gradient(180deg,#e3edf6,#ffffff); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; overflow:hidden; cursor:pointer; }
    .header-avatar svg { width:22px; height:22px; fill:#6b7a8c; }
    .sidebar { position: fixed; left: 0; top: 60px; width: 240px; height: 100vh; background: linear-gradient(180deg, var(--surface) 0%, #f7fbff 100%); border-right:1px solid var(--border); box-shadow: var(--shadow); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index: 900; }
    body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
    .nav { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px; }
    .nav a { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--text); text-decoration:none; border:1px solid var(--border); background: var(--surface); transition: transform .18s ease, box-shadow .18s ease; }
    .nav a:hover { transform: translateX(2px); box-shadow: var(--shadow); }
    .nav a.active { background: linear-gradient(180deg, #eaf3fb 0%, #ffffff 100%); }
    .page { margin-left:240px; padding:16px; min-height: calc(100vh - 60px); display:flex; align-items:center; justify-content:center; }
    .success { max-width:640px; width:100%; background: var(--surface); border:1px solid var(--border); border-radius:22px; box-shadow: var(--shadow); padding:26px; text-align:center; transform: translateZ(0); animation: fadeUp .45s ease both; position:relative; overflow:hidden; }
    .icon-wrap { width:84px; height:84px; border-radius:50%; margin:0 auto 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(180deg,#eaf3fb,#ffffff); border:1px solid var(--border); box-shadow: var(--shadow); animation: pop .5s ease both; }
    .icon-wrap svg { width:40px; height:40px; fill: var(--accent2); }
    .title { margin:8px 0 4px; font-size:24px; font-weight:800; }
    .subtitle { margin:0; color:var(--muted); font-size:13px; }
    .info { margin:16px 0 0; display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .info .box { background: linear-gradient(180deg,#f7fbff,#ffffff); border:1px solid var(--border); border-radius:14px; padding:12px; }
    .cta { display:flex; gap:10px; justify-content:center; margin-top:16px; }
    .btn { padding:12px 14px; border:none; border-radius:999px; background: linear-gradient(135deg, #3498db 0%, #1abc9c 100%); color:#fff; font-weight:600; font-size:14px; cursor:pointer; text-decoration:none; transition: transform .18s ease, box-shadow .18s ease; }
    .btn.alt { background: linear-gradient(135deg, #6b7a8c 0%, #96a3b2 100%); }
    .btn:hover { transform: scale(1.02); box-shadow: 0 16px 40px rgba(20,40,80,0.18); }
    .confetti { position:absolute; inset:0; pointer-events:none; }
    .confetti span { position:absolute; width:6px; height:6px; border-radius:2px; animation: drop 1.6s ease forwards; opacity:0.8; }
    @keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
    @keyframes pop { 0%{ transform: scale(.9);} 100%{ transform: scale(1);} }
    @keyframes drop { 0%{ transform: translateY(-20px) translateX(0); opacity:0;} 50%{ opacity:1;} 100%{ transform: translateY(120px) translateX(20px); opacity:0; } }
    @media (max-width: 768px){ .page{ margin-left:0; padding:12px; } .sidebar { transform: translateX(-100%); } body.sidebar-open .sidebar { transform: translateX(0); } .info{ grid-template-columns:1fr; } }
    .spacer { height:8px; }
    .brand-small { font-size:12px; color:var(--muted); }
    .label { font-size:12px; color:var(--muted); margin-bottom:4px; }
    .value { font-weight:700; }
    .icon { width:16px; height:16px; vertical-align:middle; margin-right:6px; }
    .nav i { width:16px; height:16px; display:inline-block; }
    .header-avatar:hover { box-shadow: var(--shadow); }
    .hamburger:hover { box-shadow: var(--shadow); }
    .brand { cursor:default; }
    .success .confetti span:nth-child(1){ left:12%; background:#e67e22; }
    .success .confetti span:nth-child(2){ left:28%; background:#1abc9c; }
    .success .confetti span:nth-child(3){ left:44%; background:#3498db; }
    .success .confetti span:nth-child(4){ left:60%; background:#e74c3c; }
    .success .confetti span:nth-child(5){ left:76%; background:#9b59b6; }
    .success .confetti span:nth-child(6){ left:20%; background:#f1c40f; }
    .success .confetti span:nth-child(7){ left:52%; background:#2ecc71; }
    .success .confetti span:nth-child(8){ left:84%; background:#2980b9; }
    .success .confetti span:nth-child(9){ left:35%; background:#16a085; }
    .success .confetti span:nth-child(10){ left:67%; background:#d35400; }
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
            <li><a href="booking.php" class="active"><i class="fa-solid fa-list-check"></i> My Activities</a></li>
            <li><a href="my_bookings.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
            <li><a href="profile.php" ><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="../pages/index.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="page" id="page">
        <div class="success">
            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17l-3.5-3.5L4 14.17l5 5 12-12-1.41-1.41z"/></svg>
            </div>
            <div class="title">Booking Confirmed!</div>
            <p class="subtitle">Your booking has been submitted successfully.</p>
            <div class="spacer"></div>
            <div class="info">
                <div class="box">
                    <div class="label"><span class="icon">🚗</span>Vehicle</div>
                    <div class="value">Saved with your booking</div>
                </div>
                <div class="box">
                    <div class="label"><span class="icon">📅</span>Dates</div>
                    <div class="value">As selected in the booking</div>
                </div>
                <div class="box">
                    <div class="label"><span class="icon">💳</span>Payment</div>
                    <div class="value">Pending or Confirmed</div>
                </div>
                <div class="box">
                    <div class="label"><span class="icon">💰</span>Total</div>
                    <div class="value">Calculated in your booking</div>
                </div>
            </div>
            <div class="cta">
                <a class="btn" href="index.php">Go to Dashboard</a>
                <a class="btn alt" href="my_bookings.php">View My Bookings</a>
            </div>
            <div class="confetti">
                <span></span><span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="brand-small">Thank you for choosing our service</div>
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
