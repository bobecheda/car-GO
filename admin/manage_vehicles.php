<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';
include 'header.php';
include 'sidebar.php';
?>

<style>
:root { --bg:#0f141a; --surface:#111820; --card:#141c24; --text:#e8f0f7; --muted:#8aa0b4; --border:#1e2a36; --shadow:0 12px 30px rgba(0,0,0,0.25); --accent:#16a085; --blue:#3498db; --red:#e74c3c; }
* { box-sizing:border-box; }
body { background: linear-gradient(180deg, #0c1217 0%, #0f141a 100%); color: var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

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
.sidebar-collapsed .admin-sidebar { transform: translateX(-100%); }
.sidebar-brand { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.sidebar-nav .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px; }
.nav-link { display:block; padding:12px 12px; border-radius:10px; color:#dbe7f1; text-decoration:none; transition: all .2s ease; }
.nav-link:hover { background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transform: translateX(2px); }
.nav-link[href="manage_vehicles.php"] { background: linear-gradient(180deg, #203040 0%, #182534 100%); border:1px solid var(--border); }
@media (min-width: 769px) { .admin-header { margin-left:240px; width: calc(100% - 240px); } body.sidebar-collapsed .admin-header { margin-left:0; width:100%; } }

/* Page layout */
.vehicles-page { margin-left:240px; padding:20px; transition: margin-left .25s ease; }
body.sidebar-collapsed .vehicles-page { margin-left:0; }
.page-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:8px 0 16px; }
.page-title { margin:0; font-size:20px; letter-spacing:.3px; }

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:8px; text-decoration:none; border-radius:10px; padding:10px 14px; border:1px solid var(--border); transition: all .2s ease; box-shadow: var(--shadow); }
.btn:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,0.35); }
.btn-sm { padding:8px 12px; border-radius:8px; box-shadow:none; }
.btn-primary { background: linear-gradient(180deg, #2b7fb8 0%, #1f5e8c 100%); color:#fff; }
.btn-danger { background: linear-gradient(180deg, #d14e3d 0%, #a93a2f 100%); color:#fff; }
.btn-accent { background: linear-gradient(180deg, #1f3a31 0%, #162a23 100%); color:#dff7ef; }
.btn-accent:hover { filter: brightness(1.05); }

/* Cards */
.card { background: var(--card); border:1px solid var(--border); border-radius:16px; box-shadow: var(--shadow); padding:16px; }
.card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }

/* Table */
.table-responsive { overflow-x:auto; border-radius:14px; }
.data-table { width:100%; border-collapse: separate; border-spacing:0; background: var(--surface); color: var(--text); }
.data-table thead th { text-align:left; padding:12px 14px; background: linear-gradient(180deg, #233244 0%, #1a2633 100%); color:#e6f0f7; font-weight:600; border-bottom:1px solid var(--border); position:sticky; top:0; }
.data-table tbody td { padding:12px 14px; border-bottom:1px solid var(--border); }
.data-table tbody tr { transition: background .18s ease; }
.data-table tbody tr:hover { background: rgba(255,255,255,0.04); }
.status-pill { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); }
.status-pill[data-status="available"] { background: rgba(22,160,133,0.15); color:#bff2e6; }
.status-pill[data-status="maintenance"] { background: rgba(231,76,60,0.15); color:#ffd7d3; }
.status-pill[data-status="booked"] { background: rgba(52,152,219,0.15); color:#d7ecff; }
.vehicle-img { width:80px; height:60px; object-fit:cover; border-radius:8px; border:1px solid var(--border); box-shadow: 0 6px 14px rgba(0,0,0,0.25); }

/* Responsive */
@media (max-width: 1024px){ .page-title { font-size:18px; } }
@media (max-width: 768px){ .vehicles-page { margin-left:0; padding:16px; } .admin-sidebar { transform: translateX(-100%); } body.sidebar-open .admin-sidebar { transform: translateX(0); } .admin-header h1 { font-size:16px; } .page-header { flex-direction:column; align-items:flex-start; gap:10px; } .data-table thead { display:none; } .data-table, .data-table tbody, .data-table tr, .data-table td { display:block; width:100%; }
 .data-table tbody tr { margin-bottom:12px; background: rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:12px; padding:10px; }
 .data-table td { border:none; padding:8px 10px; }
}
</style>

<div class="vehicles-page">
    <div class="page-header">
        <h2 class="page-title">Manage Vehicles</h2>
        <a href="add_vehicle.php" class="btn btn-accent">+ Add New Vehicle</a>
    </div>

    <section class="card">
        <div class="table-responsive">
            <table class="data-table" id="vehiclesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Price Per Day</th>
                        <th>Status</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM cars ORDER BY id DESC");

                while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['brand']; ?></td>
                        <td><?= $row['model']; ?></td>
                        <td><?= $row['year']; ?></td>
                        <td><?= $row['price_per_day']; ?></td>
                        <td>
                            <span class="status-pill" data-status="<?= $row['status']; ?>"><?= $row['status']; ?></span>
                        </td>
                        <td><img src="../uploads/<?= $row['image_url']; ?>" class="vehicle-img"></td>
                        <td>
                            <a href="edit_vehicle.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_vehicle.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
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
        window.addEventListener('resize', function(){ if(isMobile()){ document.body.classList.remove('sidebar-collapsed'); } });
    });
</script>

<?php include 'footer.php'; ?>
