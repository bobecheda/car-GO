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
    <style>
        :root { --bg:#0f141a; --surface:#111820; --card:#141c24; --text:#e8f0f7; --muted:#8aa0b4; --border:#1e2a36; --shadow:0 12px 30px rgba(0,0,0,0.25); --green:#16a085; --blue:#3498db; --red:#e74c3c; --yellow:#f1c40f; --orange:#e67e22; }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg, #0c1217 0%, #0f141a 100%); color: var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        /* Header */
        .admin-header { position: sticky; top:0; z-index:1000; background: linear-gradient(180deg, #16202b 0%, #121a22 100%); border-bottom: 1px solid var(--border); box-shadow: 0 6px 20px rgba(0,0,0,0.25); }
        .admin-header .header-inner { display:flex; align-items:center; justify-content:space-between; padding:8px 14px; }
        .admin-header h1 { margin:0; font-size:16px; letter-spacing:0.3px; }
        .welcome-text { margin-right:14px; color: var(--muted); }
        .logout-link { color: #fff; text-decoration:none; padding:8px 12px; border-radius:10px; background: linear-gradient(180deg, #213040 0%, #1a2633 100%); border:1px solid var(--border); transition: all .2s ease; }
        .logout-link:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,0.28); }
        .brand-logo { width:26px; height:26px; border-radius:6px; background: linear-gradient(180deg,#1f2a36,#16202b); border:1px solid var(--border); margin-right:10px; box-shadow: 0 6px 14px rgba(0,0,0,0.25); }
        .sidebar-toggle { display:flex; flex-direction:column; gap:4px; margin-right:12px; border:none; background:transparent; cursor:pointer; padding:6px; border-radius:8px; }
        .sidebar-toggle span { width:18px; height:2px; background:#fff; display:block; border-radius:2px; transition:transform .2s ease, opacity .2s ease; }
        /* Sidebar */
        .admin-sidebar { position:fixed; inset:0 auto 0 0; width:240px; background: linear-gradient(180deg, #131b24 0%, #0f161d 100%); border-right:1px solid var(--border); box-shadow: 8px 0 24px rgba(0,0,0,0.25); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index:1200; }
        body.sidebar-collapsed .admin-sidebar { transform: translateX(-100%); }
        .sidebar-nav .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px; }
        .nav-link { display:block; padding:12px 12px; border-radius:10px; color:#dbe7f1; text-decoration:none; transition: all .2s ease; }
        .nav-link:hover { background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transform: translateX(2px); }
        .nav-link[href="manage_customers.php"] { background: linear-gradient(180deg, #203040 0%, #182534 100%); border:1px solid var(--border); }
        /* Footer */
        .admin-footer { clear:both; text-align:center; padding:6px 10px; background:#16202b; color:#fff; position:fixed; bottom:0; width:100%; border-top:1px solid var(--border); font-size:12px; }
        /* Layout */
        @media (min-width: 769px) { .admin-header { margin-left:240px; width: calc(100% - 240px); } }
        .customers-page { margin-left:240px; padding:20px; transition: margin-left .25s ease; }
        body.sidebar-collapsed .customers-page { margin-left:0; }
        .page-title { margin:8px 0 16px; font-size:22px; letter-spacing:.3px; }
        .card { background: var(--card); border:1px solid var(--border); border-radius:18px; box-shadow: var(--shadow); padding:16px; }
        .section { margin-bottom:16px; }
        /* Search */
        .search-bar { display:flex; gap:10px; align-items:center; }
        .form-control { flex:1; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#1a2430 0%, #121a22 100%); color: var(--text); }
        .form-control::placeholder { color: var(--muted); }
        .btn { display:inline-flex; align-items:center; gap:8px; text-decoration:none; border-radius:10px; padding:8px 12px; border:1px solid var(--border); transition: all .2s ease; cursor:pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,0.35); }
        .btn-primary { background: linear-gradient(180deg, #2b7fb8 0%, #1f5e8c 100%); color:#fff; }
        .btn-danger { background: linear-gradient(180deg, #3c1f1f 0%, #2a1616 100%); color:#ffd7d3; }
        .btn-success { background: linear-gradient(180deg, #1f3a31 0%, #162a23 100%); color:#dff7ef; }
        .btn-warning { background: linear-gradient(180deg, #e67e22 0%, #c76a1d 100%); color:#fff7e9; }
        .btn-sm { padding:6px 10px; border-radius:8px; }
        .table tbody td:nth-child(7) a { margin-right:8px; display:inline-flex; }
        .table tbody td:nth-child(7) a:last-child { margin-right:0; }
        /* Table */
        .table { width:100%; border-collapse: separate; border-spacing:0; background: var(--surface); color: var(--text); }
        .table thead th { text-align:left; padding:12px 14px; background: linear-gradient(180deg, #233244 0%, #1a2633 100%); color:#e6f0f7; font-weight:600; border-bottom:1px solid var(--border); position:sticky; top:0; }
        .table tbody td { padding:12px 14px; border-bottom:1px solid var(--border); vertical-align: middle; }
        .table tbody tr { transition: background .18s ease; }
        .table tbody tr:hover { background: rgba(255,255,255,0.04); }
        /* Status */
        .status-badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); }
        .status-active { background: rgba(22,160,133,0.15); color:#bff2e6; }
        .status-suspended { background: rgba(231,76,60,0.15); color:#ffd7d3; }
        /* Pagination */
        .pagination { display:flex; gap:8px; padding:0; list-style:none; }
        .page-item a.page-link { display:inline-block; padding:8px 12px; border-radius:10px; border:1px solid var(--border); background: linear-gradient(180deg,#203040,#182534); color:#e6f0f7; text-decoration:none; transition: all .18s ease; }
        .page-item a.page-link:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,0.28); }
        .page-item.active a.page-link { background: linear-gradient(180deg,#2b7fb8,#1f5e8c); color:#fff; }
        @media (max-width: 768px){ .customers-page { margin-left:0; padding:16px; } .admin-sidebar { transform: translateX(-100%); } body.sidebar-open .admin-sidebar { transform: translateX(0); } .admin-header h1 { font-size:16px; } }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="customers-page">
    <h2 class="page-title">Manage Customers</h2>
    <section class="card section">
        <form method="GET" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </section>
    <section class="card section">
    <table class="table">
        <thead>
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
</section>
</div>

<?php include 'footer.php'; ?>

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
    var statusCells = document.querySelectorAll('.table tbody tr td:nth-child(6)');
    statusCells.forEach(function(cell){
        var t = (cell.textContent || '').trim().toLowerCase();
        var cls = 'status-badge ' + (t==='active' ? 'status-active' : 'status-suspended');
        cell.innerHTML = '<span class="'+cls+'">'+cell.textContent.trim()+'</span>';
    });
    var pagelinks = document.querySelectorAll('.pagination .page-link');
    pagelinks.forEach(function(a){ a.addEventListener('click', function(){ window.scrollTo({ top: 0, behavior: 'smooth' }); }); });
});
</script>
</body>
</html>
