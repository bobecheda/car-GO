<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

if(!isset($_GET['id'])) {
    die("Customer ID missing.");
}

$customer_id = (int)$_GET['id'];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_sql = "UPDATE users SET name='$name', email='$email', phone='$phone', status='$status' WHERE id=$customer_id";
    if(mysqli_query($conn, $update_sql)) {
        header("Location: manage_customers.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch customer details
$sql = "SELECT * FROM users WHERE id=$customer_id LIMIT 1";
$res = mysqli_query($conn, $sql);
if(mysqli_num_rows($res) == 0){
    die("Customer not found.");
}
$customer = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <style>
        :root { --bg:#0f141a; --surface:#111820; --card:#141c24; --text:#e8f0f7; --muted:#8aa0b4; --border:#1e2a36; --shadow:0 12px 30px rgba(0,0,0,0.25); --blue:#3498db; --green:#16a085; --red:#e74c3c; }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg, #0c1217 0%, #0f141a 100%); color: var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

        .customer-page { max-width: 960px; margin: 40px auto; padding: 0 16px; animation: fadeUp .45s ease both; }
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .page-title { margin:0; font-size:22px; letter-spacing:0.3px; }
        .status-badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); margin-left:10px; }
        .status-active { background: rgba(22,160,133,0.15); color:#bff2e6; }
        .status-suspended { background: rgba(231,76,60,0.15); color:#ffd7d3; }

        .form-card { background: var(--card); border:1px solid var(--border); border-radius:18px; box-shadow: var(--shadow); padding:18px; }
        .form-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; }
        .form-group { display:flex; flex-direction:column; gap:8px; }
        .form-label { font-weight:600; font-size:13px; color:#d3e1ee; }
        .input { width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#1a2430 0%, #121a22 100%); color: var(--text); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transition: box-shadow .2s ease, transform .1s ease, border-color .2s ease; }
        .input:focus { outline:none; border-color:#2b7fb8; box-shadow: 0 0 0 3px rgba(52,152,219,0.18); }
        .select { width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#1a2430 0%, #121a22 100%); color: var(--text); }

        .form-alert { display:none; margin-bottom:12px; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#213040,#1a2633); color:#e6f0f7; }
        .form-alert.error { border-color:#a93a2f; background: linear-gradient(180deg,#3b2020,#2b1616); color:#ffd7d3; }
        .form-alert.success { border-color:#1f5e8c; background: linear-gradient(180deg,#1b2e40,#172433); color:#d7ecff; }

        .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:14px; }
        .btn { display:inline-flex; align-items:center; gap:8px; text-decoration:none; border-radius:12px; padding:10px 16px; border:1px solid var(--border); transition: all .18s ease; cursor:pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,0.35); }
        .btn-primary { background: linear-gradient(180deg, #2b7fb8 0%, #1f5e8c 100%); color:#fff; }
        .btn-secondary { background: linear-gradient(180deg, #203040 0%, #182534 100%); color:#dbe7f1; }

        @keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
        @media (max-width: 768px){ .form-grid { grid-template-columns: 1fr; } .page-title { font-size:20px; } }
    </style>
</head>
<body>
    <div class="customer-page">
        <div class="page-header">
            <h2 class="page-title">Edit Customer
                <span class="status-badge <?php echo ($customer['status']==='active') ? 'status-active' : 'status-suspended'; ?>">Status: <?php echo htmlspecialchars($customer['status']); ?></span>
            </h2>
        </div>

        <div class="form-card">
            <div class="form-alert" id="formMsg" role="alert"></div>
            <form method="POST" id="editCustomerForm" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="input" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="input" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="input" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="select" id="statusSelect">
                            <option value="active" <?php if($customer['status']=='active') echo 'selected'; ?>>Active</option>
                            <option value="suspended" <?php if($customer['status']=='suspended') echo 'selected'; ?>>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary" id="saveBtn">Update Customer</button>
                    <a href="manage_customers.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var form = document.getElementById('editCustomerForm');
            var msg = document.getElementById('formMsg');
            var statusSelect = document.getElementById('statusSelect');
            var saveBtn = document.getElementById('saveBtn');

            function showMessage(type, text){
                msg.className = 'form-alert ' + (type || '');
                msg.textContent = text || '';
                msg.style.display = text ? 'block' : 'none';
            }

            function isValidEmail(email){
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            if(statusSelect){
                statusSelect.addEventListener('change', function(){
                    var val = statusSelect.value;
                    var statusBadge = document.querySelector('.status-badge');
                    if(statusBadge){
                        statusBadge.textContent = 'Status: ' + val;
                        statusBadge.classList.toggle('status-active', val==='active');
                        statusBadge.classList.toggle('status-suspended', val==='suspended');
                    }
                    showMessage('success', 'Status set to ' + val + '.');
                });
            }

            if(form){
                form.addEventListener('submit', function(e){
                    var name = form.querySelector('input[name="name"]').value.trim();
                    var email = form.querySelector('input[name="email"]').value.trim();
                    if(!name || !email){
                        e.preventDefault();
                        showMessage('error', 'Please fill in required fields.');
                        return false;
                    }
                    if(!isValidEmail(email)){
                        e.preventDefault();
                        showMessage('error', 'Please enter a valid email address.');
                        return false;
                    }
                    showMessage('', '');
                });
            }
        });
    </script>
</body>
</html>
