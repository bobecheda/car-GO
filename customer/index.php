<?php
// customer/index.php
// Customer home page - list vehicles with filters

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

// Build filter values from GET
$transmission_filter = isset($_GET['transmission']) ? mysqli_real_escape_string($conn, $_GET['transmission']) : '';
$price_filter = isset($_GET['price']) ? mysqli_real_escape_string($conn, $_GET['price']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build WHERE clause
$whereClauses = [];

// Only show active/available vehicles by default (you can change this)
// NOTE: previously this included 'booked' and 'maintenance' which would also return
// non-rentable vehicles. Limit default to vehicles that can be rented.
$whereClauses[] = "status IN ('available','active')";

// Transmission filter (type)
if ($transmission_filter !== '') {
    $whereClauses[] = "transmission = '{$transmission_filter}'";
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

// Search by brand or model
if ($search !== '') {
    $whereClauses[] = "(brand LIKE '%$search%' OR model LIKE '%$search%')";
}

$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get distinct transmissions for filter dropdown
$transResult = mysqli_query($conn, "SELECT DISTINCT transmission FROM cars WHERE transmission IS NOT NULL AND transmission <> ''");
$transmissions = [];
while ($t = mysqli_fetch_assoc($transResult)) {
    $transmissions[] = $t['transmission'];
}

// Fetch vehicles
$sql = "SELECT * FROM cars $whereSQL ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Car Rental - Vehicles</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- optional: your global CSS -->
    <style>
        /* Minimal page-specific styling to ensure presentable layout */
        body { font-family: Arial, sans-serif; background:#f7f9fb; margin:0; padding:0; color:#333;}
        .container { max-width:1100px; margin:30px auto; padding:0 15px; }
        header { display:flex; justify-content:space-between; align-items:center; padding:20px 0; }
        .filters { background:#fff; padding:12px; border-radius:8px; display:flex; gap:12px; align-items:center; margin-bottom:20px; flex-wrap:wrap; }
        .filters input[type="text"], .filters select { padding:8px; border:1px solid #ddd; border-radius:6px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; }
        .card { background:#fff; border-radius:8px; padding:12px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        .card img { width:100%; height:150px; object-fit:cover; border-radius:6px; }
        .meta { display:flex; justify-content:space-between; margin-top:8px; align-items:center; }
        .brand { font-weight:700; }
        .price { color:#1abc9c; font-weight:700; }
        .btn { display:inline-block; padding:8px 12px; background:#3498db; color:#fff; border-radius:6px; text-decoration:none; }
        .status { padding:4px 8px; border-radius:6px; font-size:13px; color:#fff; }
        .status.available { background:#27ae60; }
        .status.maintenance { background:#e74c3c; }
        .status.booked { background:#f39c12; }
        .no-results { padding:30px; text-align:center; color:#777; }
        @media(max-width:600px){ .meta{flex-direction:column; align-items:flex-start; gap:8px;} }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Available Vehicles</h1>
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search brand or model" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn" style="background:#2ecc71;">Search</button>
        </form>
    </header>

    <div class="filters">
        <form method="GET" id="filterForm" style="display:flex; gap:8px; align-items:center;">
            <!-- Keep search param when filtering -->
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

            <label>
                Type:
                <select name="transmission" onchange="document.getElementById('filterForm').submit();">
                    <option value="">All Types</option>
                    <?php foreach($transmissions as $tr): ?>
                        <option value="<?php echo htmlspecialchars($tr); ?>" <?php if($transmission_filter==$tr) echo 'selected'; ?>><?php echo htmlspecialchars(ucfirst($tr)); ?></option>
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

            <a href="index.php" class="btn" style="background:#95a5a6;">Reset</a>
        </form>
    </div>

    <?php if(!$result || mysqli_num_rows($result) == 0): ?>
        <div class="no-results">No vehicles found matching your filters.</div>
    <?php else: ?>
        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Fallbacks for image and display fields
                $img = !empty($row['image_url']) ? '../uploads/' . $row['image_url'] : '../assets/images/no-image.png';
                $status = $row['status'];
                ?>
                <div class="card">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['brand'].' '.$row['model']); ?>">
                    <div style="margin-top:10px;">
                        <div class="brand"><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></div>
                        <div style="font-size:13px; color:#666;"><?php echo htmlspecialchars($row['year']); ?> • <?php echo htmlspecialchars($row['transmission']); ?> • <?php echo htmlspecialchars($row['capacity']); ?> seats</div>

                        <div class="meta">
                            <div class="price">KES <?php echo number_format($row['price_per_day']); ?></div>
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
                            <a href="vehicle.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                            <!-- Quick Rent (link to booking page) -->
                            <?php if($status == 'available' || $status == 'active'): ?>
                                <a href="rent.php?car_id=<?php echo $row['id']; ?>" class="btn" style="background:#e67e22; margin-left:8px;">Rent Now</a>
                            <?php else: ?>
                                <button class="btn" style="background:#bdc3c7; cursor:not-allowed; margin-left:8px;" disabled>Not Available</button>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Optional small script to preserve search on filter changes (already handled) -->
<script>
    // nothing fancy needed for now
</script>
</body>
</html>
