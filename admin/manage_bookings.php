<?php
session_start();
include("../config/db.php");



// Optional: Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied.");
}

// Fetch all bookings with car and customer info
$sql = "
SELECT b.*, c.brand, c.model
FROM bookings b
JOIN cars c ON b.car_id = c.id
ORDER BY b.start_date DESC
";
$result = mysqli_query($conn, $sql);
if ($result === false) {
    error_log('manage_bookings query error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg:#0f141a; --surface:#111820; --card:#141c24; --muted:#8aa0b4; --text:#e8f0f7; --accent:#16a085; --accent-blue:#3498db; --accent-red:#e74c3c; --accent-orange:#e67e22; --accent-purple:#9b59b6; --accent-yellow:#f1c40f; --border:#1e2a36; --shadow:0 12px 30px rgba(0,0,0,0.25); }
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
        /* Sidebar */
        .admin-sidebar { position:fixed; inset:0 auto 0 0; width:240px; background: linear-gradient(180deg, #131b24 0%, #0f161d 100%); border-right:1px solid var(--border); box-shadow: 8px 0 24px rgba(0,0,0,0.25); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index:1200; }
        .sidebar-collapsed .admin-sidebar { transform: translateX(-100%); }
        .sidebar-brand { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .sidebar-nav .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px; }
        .nav-link { display:block; padding:12px 12px; border-radius:10px; color:#dbe7f1; text-decoration:none; transition: all .2s ease; }
        .nav-link:hover { background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transform: translateX(2px); }
        .nav-link[href="manage_vehicles.php"] { background: linear-gradient(180deg, #203040 0%, #182534 100%); border:1px solid var(--border); }
        @media (min-width: 769px) { .admin-header { margin-left:240px; width: calc(100% - 240px); } body.sidebar-collapsed .admin-header { margin-left:0; width:100%; } }
        .dashboard { margin-left:240px; padding:20px; transition: margin-left .25s ease; }
        .heading { display:flex; align-items:center; justify-content:space-between; margin:10px 0 14px; }
        .heading-left { display:flex; align-items:center; gap:12px; }
        .iconwrap { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); background: linear-gradient(180deg,#1f2a36,#16202b); color:#e8f0f7; }
        .titlebox h2 { margin:0; font-size:20px; }
        .titlebox p { margin:0; color:var(--muted); font-size:12px; }
        .card { background: var(--card); border:1px solid var(--border); border-radius:16px; box-shadow: var(--shadow); overflow:hidden; }
        .card-head { display:flex; flex-wrap:wrap; gap:10px; padding:12px 14px; border-bottom:1px solid var(--border); background: linear-gradient(180deg, #16202b 0%, #121a22 100%); }
        .search { flex: 1 1 260px; display:flex; align-items:center; gap:8px; border:1px solid var(--border); background: var(--surface); border-radius:12px; padding:8px 10px; }
        .search i { color:#8aa0b4; }
        .search input { flex:1; border:none; outline:none; background:transparent; font-size:14px; color:var(--text); }
        .filters { display:flex; gap:8px; align-items:center; }
        .filters select, .filters input[type=date] { border:1px solid var(--border); background: var(--surface); border-radius:12px; padding:8px 10px; font-size:14px; color:var(--text); }
        .admin-table { width:100%; border-collapse: separate; border-spacing:0; }
        .admin-table thead th { text-align:left; padding:12px 14px; background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); color:#d3e1ee; font-weight:600; border-bottom:1px solid var(--border); }
        .admin-table tbody td { padding:12px 14px; border-bottom:1px solid var(--border); vertical-align: middle; }
        .admin-table tbody tr { transition: background .18s ease; }
        .admin-table tbody tr:nth-child(even) { background: rgba(255,255,255,0.03); }
        .admin-table tbody tr:hover { background: rgba(255,255,255,0.06); }
        .status-badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); }
        .status-approved { background: rgba(22,160,133,0.18); color:#bdf2e6; }
        .status-pending { background: rgba(230,126,34,0.18); color:#f7dfc5; }
        .status-rejected { background: rgba(231,76,60,0.18); color:#f5c3bd; }
        .actions a { display:inline-block; padding:8px 10px; border-radius:10px; text-decoration:none; margin-right:6px; border:1px solid var(--border); transition: transform .18s ease, box-shadow .18s ease; }
        .actions a:hover { transform: translateY(-1px); box-shadow: var(--shadow); }
        .pagination { display:flex; gap:8px; justify-content:center; align-items:center; padding:12px; }
        .page-btn { display:inline-block; padding:8px 12px; border-radius:999px; border:1px solid var(--border); background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); text-decoration:none; color:#e8f0f7; transition: transform .18s ease, box-shadow .18s ease; }
        .page-btn:hover { transform: translateY(-1px); box-shadow: 0 12px 28px rgba(0,0,0,0.35); }
        .page-btn.active { background: linear-gradient(180deg, #213040 0%, #1a2633 100%); font-weight:700; }
        @media (max-width: 768px){ .dashboard{ margin-left:0; padding:16px; } .card-head{ padding:10px; } .search{ flex-basis:100%; } .filters{ flex-wrap:wrap; } .admin-table thead{ display:none; } .admin-table, .admin-table tbody, .admin-table tr, .admin-table td{ display:block; width:100%; } .admin-table tbody tr{ margin-bottom:12px; border:1px solid var(--border); border-radius:12px; padding:10px; } .admin-table td{ border:none; padding:8px 10px; } }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
 


<div class="dashboard">
    <div class="heading">
        <div class="heading-left">
            <div class="iconwrap"><i class="fa-solid fa-clipboard-list"></i></div>
            <div class="titlebox">
                <h2>Manage Bookings</h2>
                <p>Admin Control Panel</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-head">
            <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="searchInput" placeholder="Search bookings (customer, car, dates, status)"></div>
            <div class="filters">
                <select id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                </select>
                <input type="date" id="fromDate">
                <input type="date" id="toDate">
            </div>
        </div>

    <table class="admin-table" id="bookingsTable">
        <thead>
            <tr>
                <th><i class="fa-solid fa-hashtag"></i> #</th>
                <th><i class="fa-solid fa-user"></i> Customer</th>
                <th><i class="fa-solid fa-car-side"></i> Car</th>
                <th><i class="fa-regular fa-calendar"></i> Pickup</th>
                <th><i class="fa-regular fa-calendar-check"></i> Return</th>
                <th><i class="fa-solid fa-money-bill-wave"></i> Total Price</th>
                <th><i class="fa-solid fa-bolt"></i> Status</th>
                <th><i class="fa-solid fa-gear"></i> Action</th>
                <th><i class="fa-solid fa-message"></i> Message</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></td>
                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                <td><?php echo number_format((float)$row['total_price']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                <td class="actions">
                    <?php if ($row['status'] == 'pending') { ?>

                        <!-- APPROVE -->
                        <a href="process_booking_action.php?id=<?php echo $row['id']; ?>&action=approve" 
                           class="btn btn-sm btn-success"
                           onclick="return confirm('Approve this booking?');">
                           Approve
                        </a>

                        <!-- REJECT -->
                        <a href="process_booking_action.php?id=<?php echo $row['id']; ?>&action=reject" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Reject this booking?');">
                           Reject
                        </a>


                    <?php } else { ?>
                        -
                    <?php } ?>
                    </td>
                <td><?php echo isset($row['admin_message']) ? htmlspecialchars($row['admin_message']) : '-'; ?></td>
            </tr>
            <?php
            }
        } else {
            echo '<tr><td colspan="9" class="text-center">No bookings found.</td></tr>';
        }
        ?>
        </tbody>
    </table>
        <div class="pagination" id="pagination"></div>
    </div>

 <script>
 (function(){
    var table = document.getElementById('bookingsTable');
    var rows = table ? Array.prototype.slice.call(table.querySelectorAll('tbody tr')) : [];
    rows.forEach(function(row){ var cells=row.querySelectorAll('td'); if(cells.length>=9){ var s=cells[6]; var t=(s.textContent||'').trim().toLowerCase(); var cls='status-badge'; if(t==='approved') cls+=' status-approved'; else if(t==='pending') cls+=' status-pending'; else if(t==='rejected') cls+=' status-rejected'; s.innerHTML='<span class="'+cls+'">'+s.textContent.trim()+'</span>'; } });
    var searchInput=document.getElementById('searchInput');
    var statusFilter=document.getElementById('statusFilter');
    var fromDate=document.getElementById('fromDate');
    var toDate=document.getElementById('toDate');
    function matches(r){ var tds=r.querySelectorAll('td'); var text=(r.textContent||'').toLowerCase(); var q=(searchInput.value||'').toLowerCase(); if(q && text.indexOf(q)===-1) return false; var status=(statusFilter.value||'').toLowerCase(); if(status){ var st=(tds[6].textContent||'').toLowerCase(); if(st.indexOf(status)===-1) return false; } var start=tds[3] ? (tds[3].textContent||'') : ''; var end=tds[4] ? (tds[4].textContent||'') : ''; var fd=fromDate.value, td=toDate.value; if(fd && start < fd) return false; if(td && end > td) return false; return true; }
    var pageSize=10, current=1; var pagination=document.getElementById('pagination');
    function render(){ var filtered=rows.filter(matches); var total=Math.max(1, Math.ceil(filtered.length/pageSize)); if(current>total) current=total; rows.forEach(function(r){ r.style.display='none'; }); filtered.slice((current-1)*pageSize, current*pageSize).forEach(function(r){ r.style.display='table-row'; }); if(pagination){ var html=''; for(var i=1;i<=total;i++){ html+='<a href="#" class="page-btn '+(i===current?'active':'')+'" data-page="'+i+'">'+i+'</a>'; } pagination.innerHTML=html; pagination.querySelectorAll('a').forEach(function(a){ a.addEventListener('click', function(e){ e.preventDefault(); current=parseInt(a.getAttribute('data-page'))||1; render(); }); }); }
    }
    ['input','change'].forEach(function(ev){ if(searchInput) searchInput.addEventListener(ev, render); if(statusFilter) statusFilter.addEventListener(ev, render); if(fromDate) fromDate.addEventListener(ev, render); if(toDate) toDate.addEventListener(ev, render); });
    render();
 })();
 </script>
 <?php include 'footer.php'; ?>
</body>
</html>
