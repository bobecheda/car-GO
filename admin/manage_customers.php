<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}


include '../config/db.php';

// Search
$search = '';
if(isset($_GET['search']) && $_GET['search'] != ''){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Pagination
$limit = 10; // customers per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total customers for pagination
$count_sql = "SELECT COUNT(*) AS total FROM users WHERE role='customer'";
if($search != ''){
    $count_sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}
// Run count query and handle possible failure
$count_res = mysqli_query($conn, $count_sql);
$total_customers = 0;
if ($count_res) {
    $count_row = mysqli_fetch_assoc($count_res);
    $total_customers = isset($count_row['total']) ? (int)$count_row['total'] : 0;
} else {
    // Log error for debugging; don't expose SQL errors to users
    error_log('manage_customers count query error: ' . mysqli_error($conn));
}
$total_pages = $total_customers > 0 ? ceil($total_customers / $limit) : 1;

// Fetch customers (build WHERE first, then ORDER and LIMIT so appended filters are valid)
$sql = "SELECT id, name, email, phone, created_at, status FROM users WHERE role='customer'";
if($search != ''){
    $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}
$sql .= " ORDER BY created_at DESC LIMIT $offset, $limit";

$result = mysqli_query($conn, $sql);
if ($result === false) {
    error_log('manage_customers fetch query error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div style="margin-left:220px; padding:20px;">
    <h2>Manage Customers</h2>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered On</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($result && mysqli_num_rows($result) > 0){
                $i = 1;
                while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                        
                        <td>
                            <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                            <!-- Suspend / Activate button goes here -->
                            <?php if($row['status']=='active'): ?>
                            <a href="toggle_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Suspend this customer?');">Suspend</a>
                            <?php else: ?>
                            <a href="toggle_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Reactivate this customer?');">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="7" class="text-center">No customers found.</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
    <nav>
        <ul class="pagination">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

</body>
</html>

<?php include 'footer.php'; ?>
