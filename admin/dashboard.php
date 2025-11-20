
<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
include '../config/db.php'; // make sure the path is correct


// Total vehicles
$result = mysqli_query($conn, "SELECT COUNT(*) AS total_vehicles FROM cars");
$row = mysqli_fetch_assoc($result);
$total_vehicles = $row['total_vehicles'];

// Total bookings
$result = mysqli_query($conn, "SELECT COUNT(*) AS total_bookings FROM bookings");
$row = mysqli_fetch_assoc($result);
$total_bookings = $row['total_bookings'];

// Total customers
$result = mysqli_query($conn, "SELECT COUNT(*) AS total_customers FROM users WHERE role='customer'");
$row = mysqli_fetch_assoc($result);
$total_customers = $row['total_customers'];

// Cars Available Today
$today = date('Y-m-d');
$result = mysqli_query($conn, "SELECT COUNT(*) AS available_today FROM cars 
LEFT JOIN bookings ON cars.id = bookings.car_id AND bookings.start_date='$today'
WHERE cars.status='active' AND bookings.id IS NULL");
$row = mysqli_fetch_assoc($result);
$cars_available_today = $row['available_today'];

// Cars Currently Booked
$result = mysqli_query($conn, "SELECT COUNT(*) AS currently_booked FROM cars 
INNER JOIN bookings ON cars.id = bookings.car_id
WHERE bookings.start_date='$today'");
$row = mysqli_fetch_assoc($result);
$cars_currently_booked = $row['currently_booked'];

// Cars in Maintenance
$result = mysqli_query($conn, "SELECT COUNT(*) AS in_maintenance FROM cars WHERE status='maintenance'");
$row = mysqli_fetch_assoc($result);
$cars_in_maintenance = $row['in_maintenance'];

// Upcoming Bookings
$result = mysqli_query($conn, "SELECT COUNT(*) AS upcoming_bookings FROM bookings WHERE start_date > '$today'");
$row = mysqli_fetch_assoc($result);
$upcoming_bookings = $row['upcoming_bookings'];

// Popular Car Model
$result = mysqli_query($conn, "SELECT model, COUNT(*) AS usage_count FROM bookings 
INNER JOIN cars ON bookings.car_id = cars.id
GROUP BY model ORDER BY usage_count DESC LIMIT 1");
$row = mysqli_fetch_assoc($result);
$popular_car_model = $row['model'] ?? 'N/A';

// Car Usage Rate (for today)
$car_usage_rate = ($total_vehicles > 0) ? round(($cars_currently_booked / $total_vehicles) * 100, 2) : 0;

// Monthly Booking Rate
$current_year = date('Y');
$monthly_bookings = [];
for($month=1; $month<=12; $month++){
    $start = "$current_year-$month-01";
    $end = date("Y-m-t", strtotime($start));
    $result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM bookings WHERE booking_date BETWEEN '$start' AND '$end'");
    $row = mysqli_fetch_assoc($result);
    $monthly_bookings[] = $row['count'];
}
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
/* Theme */
:root { --bg:#0f141a; --surface:#111820; --card:#141c24; --muted:#8aa0b4; --text:#e8f0f7; --accent:#16a085; --accent-blue:#3498db; --accent-red:#e74c3c; --accent-orange:#e67e22; --accent-purple:#9b59b6; --accent-yellow:#f1c40f; --border:#1e2a36; --shadow:0 12px 30px rgba(0,0,0,0.25); }
* { box-sizing:border-box; }
html, body { height:100%; }
body { margin:0; font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: linear-gradient(180deg, #0c1217 0%, #0f141a 100%); color: var(--text); }

/* Header */
.admin-header { position: sticky; top:0; z-index:1000; background: linear-gradient(180deg, #16202b 0%, #121a22 100%); border-bottom: 1px solid var(--border); box-shadow: 0 6px 20px rgba(0,0,0,0.25); }
.admin-header .header-inner { display:flex; align-items:center; justify-content:space-between; padding:8px 14px; }
.admin-header h1 { margin:0; font-size:16px; letter-spacing:0.3px; }
.welcome-text { margin-right:14px; color: var(--muted); }
.logout-link { color: #fff; text-decoration:none; padding:8px 12px; border-radius:10px; background: linear-gradient(180deg, #213040 0%, #1a2633 100%); border:1px solid var(--border); transition: all .2s ease; }
.logout-link:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,0.28); }

/* Sidebar toggle */
.brand-logo { width:26px; height:26px; border-radius:6px; background: linear-gradient(180deg,#1f2a36,#16202b); border:1px solid var(--border); margin-right:10px; box-shadow: 0 6px 14px rgba(0,0,0,0.25); }
.sidebar-toggle { display:flex; flex-direction:column; gap:4px; margin-right:12px; border:none; background:transparent; cursor:pointer; padding:6px; border-radius:8px; }
.sidebar-toggle span { width:18px; height:2px; background:#fff; display:block; border-radius:2px; transition:transform .2s ease, opacity .2s ease; }

/* Sidebar */
.admin-sidebar { position:fixed; inset:0 auto 0 0; width:240px; background: linear-gradient(180deg, #131b24 0%, #0f161d 100%); border-right:1px solid var(--border); box-shadow: 8px 0 24px rgba(0,0,0,0.25); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index:1200; }
.sidebar-brand { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.sidebar-nav .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px; }
.nav-link { display:block; padding:12px 12px; border-radius:10px; color:#dbe7f1; text-decoration:none; transition: all .2s ease; }
.nav-link:hover { background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transform: translateX(2px); }

/* Layout */
.dashboard { padding:20px; margin-left:240px; transition: margin-left .25s ease; }
.page-title { margin:8px 0 20px; font-size:22px; }

/* Cards */
.stats-grid { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:18px; margin-bottom:22px; }
.widgets-grid { display:grid; grid-template-columns: repeat(3, minmax(220px,1fr)); gap:18px; margin-bottom:26px; }
.stat-card, .widget-card { background: var(--card); border:1px solid var(--border); border-radius:16px; padding:18px; box-shadow: var(--shadow); transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; animation: fadeUp .45s ease both; }
.stat-card:hover, .widget-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.35); filter: brightness(1.03); }
.stat-card h3, .widget-card h3 { margin:0 0 10px; font-weight:600; font-size:15px; color:#d3e1ee; letter-spacing:0.3px; }
.stat-value { font-size:28px; font-weight:700; }

/* Accents */
.accent-teal { background: linear-gradient(180deg, rgba(22,160,133,0.18) 0%, rgba(22,160,133,0.08) 100%); }
.accent-blue { background: linear-gradient(180deg, rgba(52,152,219,0.18) 0%, rgba(52,152,219,0.08) 100%); }
.accent-red { background: linear-gradient(180deg, rgba(231,76,60,0.18) 0%, rgba(231,76,60,0.08) 100%); }
.accent-orange { background: linear-gradient(180deg, rgba(230,126,34,0.18) 0%, rgba(230,126,34,0.08) 100%); }
.accent-purple { background: linear-gradient(180deg, rgba(155,89,182,0.18) 0%, rgba(155,89,182,0.08) 100%); }
.accent-yellow { background: linear-gradient(180deg, rgba(241,196,15,0.18) 0%, rgba(241,196,15,0.08) 100%); }

/* Charts */
.charts-grid { display:grid; grid-template-columns: repeat(2, minmax(380px,1fr)); gap:22px; margin-bottom:24px; }
.chart-card { background: var(--surface); border:1px solid var(--border); border-radius:16px; padding:16px; box-shadow: var(--shadow); animation: fadeUp .45s ease both; }
.chart-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
.chart-header h3 { margin:0; font-size:16px; color:#d3e1ee; }
.chart-card canvas { width:100%; height:280px; }

/* Animations */
@keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }

/* Responsive */
@media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } .widgets-grid { grid-template-columns: repeat(2, minmax(220px,1fr)); } .charts-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .dashboard { margin-left:0; padding:16px; } .admin-sidebar { transform: translateX(-100%); } body.sidebar-open .admin-sidebar { transform: translateX(0); } .admin-header h1 { font-size:16px; } }
@media (min-width: 769px) { .admin-header { margin-left:240px; width: calc(100% - 240px); } body.sidebar-collapsed .admin-header { margin-left:0; width:100%; } }

/* Collapsed (desktop) */
body.sidebar-collapsed .admin-sidebar { transform: translateX(-100%); }
body.sidebar-collapsed .dashboard { margin-left:0; }

/* Footer */
.admin-footer { clear:both; text-align:center; padding:6px 10px; background:#16202b; color:#fff; position:fixed; bottom:0; width:100%; border-top:1px solid var(--border); font-size:12px; }
</style>

<div class="dashboard">
    <h2 class="page-title">Dashboard</h2>
    <section class="charts-grid">
        <article class="chart-card">
            <div class="chart-header">
                <h3>Car Usage Rate (%)</h3>
            </div>
            <canvas id="carUsageChart"></canvas>
        </article>
        <article class="chart-card">
            <div class="chart-header">
                <h3>Monthly Booking Rate</h3>
            </div>
            <canvas id="monthlyBookingChart"></canvas>
        </article>
    </section>

    <section class="stats-grid">
        <article class="stat-card accent-teal">
            <h3>Total Vehicles</h3>
            <p class="stat-value"><?php echo $total_vehicles; ?></p>
        </article>
        <article class="stat-card accent-blue">
            <h3>Total Bookings</h3>
            <p class="stat-value"><?php echo $total_bookings; ?></p>
        </article>
        <article class="stat-card accent-red">
            <h3>Total Customers</h3>
            <p class="stat-value"><?php echo $total_customers; ?></p>
        </article>
    </section>

    <section class="widgets-grid">
        <article class="widget-card accent-teal">
            <h3>Total Cars in Fleet</h3>
            <p class="stat-value"><?php echo $total_vehicles; ?></p>
        </article>
        <article class="widget-card accent-blue">
            <h3>Cars Available Today</h3>
            <p class="stat-value"><?php echo $cars_available_today; ?></p>
        </article>
        <article class="widget-card accent-orange">
            <h3>Cars Currently Booked</h3>
            <p class="stat-value"><?php echo $cars_currently_booked; ?></p>
        </article>
        <article class="widget-card accent-red">
            <h3>Cars in Maintenance</h3>
            <p class="stat-value"><?php echo $cars_in_maintenance; ?></p>
        </article>
        <article class="widget-card accent-purple">
            <h3>Upcoming Bookings</h3>
            <p class="stat-value"><?php echo $upcoming_bookings; ?></p>
        </article>
        <article class="widget-card accent-yellow">
            <h3>Popular Car Models</h3>
            <p class="stat-value"><?php echo $popular_car_model; ?></p>
        </article>
    </section>

    
</div>



<?php include 'footer.php'; ?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Car Usage Rate Chart (Pie)
    const ctx1 = document.getElementById('carUsageChart').getContext('2d');
    const carUsageChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Booked', 'Available', 'Maintenance'],
            datasets: [{
                label: 'Car Usage Rate',
                data: [<?php echo $cars_currently_booked; ?>, <?php echo $cars_available_today; ?>, <?php echo $cars_in_maintenance; ?>],
                backgroundColor: ['#e67e22','#3498db','#e74c3c'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
        }
    });

    // Monthly Booking Rate Chart (Bar)
    const ctx2 = document.getElementById('monthlyBookingChart').getContext('2d');
    const monthlyBookingChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Bookings',
                data: [<?php echo implode(',', $monthly_bookings); ?>],
                backgroundColor: '#1abc9c'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        var toggle = document.getElementById('sidebarToggle');
        function isMobile(){ return window.matchMedia('(max-width: 768px)').matches; }
        if(toggle){
            toggle.addEventListener('click', function(){
                if(isMobile()){
                    document.body.classList.toggle('sidebar-open');
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                }
            });
        }
        window.addEventListener('resize', function(){
            if(isMobile()){
                document.body.classList.remove('sidebar-collapsed');
            }
        });
        document.body.classList.add('page-loaded');
    });
</script>
