<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
include '../config/db.php'; // make sure this path is correct

// -------------------- Dashboard Metrics --------------------

// Total vehicles
$result = mysqli_query($conn, "SELECT COUNT(*) AS total_vehicles FROM cars");
$total_vehicles = mysqli_fetch_assoc($result)['total_vehicles'];

// Total bookings
$result = mysqli_query($conn, "SELECT COUNT(*) AS total_bookings FROM bookings");
$total_bookings = mysqli_fetch_assoc($result)['total_bookings'];

// Total customers
$result = mysqli_query($conn, "SELECT COUNT(*) AS total_customers FROM users WHERE role='customer'");
$total_customers = mysqli_fetch_assoc($result)['total_customers'];

// Cars Available Today
$today = date('Y-m-d');
$result = mysqli_query($conn, "
    SELECT COUNT(*) AS available_today
    FROM cars
    WHERE status='active'
      AND id NOT IN (
          SELECT car_id FROM bookings 
          WHERE status='approved' 
            AND '$today' BETWEEN start_date AND end_date
      )
");
$cars_available_today = mysqli_fetch_assoc($result)['available_today'];

// Cars Currently Booked
$result = mysqli_query($conn, "
    SELECT COUNT(*) AS currently_booked
    FROM cars 
    WHERE status='booked' 

        
");
$cars_currently_booked = mysqli_fetch_assoc($result)['currently_booked'];

// Cars in Maintenance
$result = mysqli_query($conn, "SELECT COUNT(*) AS in_maintenance FROM cars WHERE status='maintenance'");
$cars_in_maintenance = mysqli_fetch_assoc($result)['in_maintenance'];

// Upcoming Bookings
$result = mysqli_query($conn, "
    SELECT COUNT(*) AS upcoming_bookings 
    FROM bookings 
    WHERE status='paid' AND start_date > '$today'
");
$upcoming_bookings = mysqli_fetch_assoc($result)['upcoming_bookings'];

// Popular Car Model
$result = mysqli_query($conn, "
    SELECT c.model, COUNT(*) AS usage_count 
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    WHERE b.status='paid'
    GROUP BY c.model
    ORDER BY usage_count DESC
    LIMIT 1
");
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
    $result = mysqli_query($conn, "
        SELECT COUNT(*) AS count 
        FROM bookings 
        WHERE status='paid' 
          AND start_date BETWEEN '$start' AND '$end'
    ");
    $monthly_bookings[] = mysqli_fetch_assoc($result)['count'];
}
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div style="margin-left:220px; padding:20px;">
    <h2>Dashboard</h2>

    <!-- Top Metrics Row -->
    <div style="display:flex; gap:20px;">
        <div style="flex:1; background:#1abc9c; padding:20px; color:#fff; margin-bottom:30px;">
            <h3>Total Vehicles</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $total_vehicles; ?></p>
        </div>
        <div style="flex:1; background:#3498db; padding:20px; color:#fff; margin-bottom:30px;">
            <h3>Total Bookings</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $total_bookings; ?></p>
        </div>
        <div style="flex:1; background:#e74c3c; padding:20px; color:#fff; margin-bottom:30px;">
            <h3>Total Customers</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $total_customers; ?></p>
        </div>
    </div>

    <!-- Widgets Row -->
    <div style="display:flex; flex-wrap:wrap; gap:20px; margin-bottom:30px;">
        <div style="flex:1; min-width:200px; background:#1abc9c; padding:20px; color:#fff; border-radius:8px;">
            <h3>Total Cars in Fleet</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $total_vehicles; ?></p>
        </div>
        <div style="flex:1; min-width:200px; background:#3498db; padding:20px; color:#fff; border-radius:8px;">
            <h3>Cars Available Today</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $cars_available_today; ?></p>
        </div>
        <div style="flex:1; min-width:200px; background:#e67e22; padding:20px; color:#fff; border-radius:8px;">
            <h3>Cars Currently Booked</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $cars_currently_booked; ?></p>
        </div>
        <div style="flex:1; min-width:200px; background:#e74c3c; padding:20px; color:#fff; border-radius:8px;">
            <h3>Cars in Maintenance</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $cars_in_maintenance; ?></p>
        </div>
        <div style="flex:1; min-width:200px; background:#9b59b6; padding:20px; color:#fff; border-radius:8px;">
            <h3>Upcoming Bookings</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $upcoming_bookings; ?></p>
        </div>
        <div style="flex:1; min-width:200px; background:#f1c40f; padding:20px; color:#fff; border-radius:8px;">
            <h3>Popular Car Model</h3>
            <p style="font-size:24px; font-weight:bold;"><?php echo $popular_car_model; ?></p>
        </div>
    </div>

    <!-- Charts Row -->
    <div style="display:flex; flex-wrap:wrap; gap:30px;">
        <div style="flex:1; min-width:400px; background:#ecf0f1; padding:20px; border-radius:8px;">
            <h3>Car Usage Rate (%)</h3>
            <canvas id="carUsageChart"></canvas>
        </div>
        <div style="flex:1; min-width:400px; background:#ecf0f1; padding:20px; border-radius:8px;">
            <h3>Monthly Booking Rate</h3>
            <canvas id="monthlyBookingChart"></canvas>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Car Usage Rate Chart (Doughnut)
    const ctx1 = document.getElementById('carUsageChart').getContext('2d');
    const carUsageChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Booked', 'Active', 'Maintenance'],
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
                y: { beginAtZero: true }
            }
        }
    });
</script>
