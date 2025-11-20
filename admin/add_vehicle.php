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

.admin-header { position: sticky; top:0; z-index:1000; background: linear-gradient(180deg, #16202b 0%, #121a22 100%); border-bottom: 1px solid var(--border); box-shadow: 0 6px 20px rgba(0,0,0,0.25); }
.admin-header .header-inner { display:flex; align-items:center; justify-content:space-between; padding:8px 14px; }
.admin-header h1 { margin:0; font-size:16px; letter-spacing:0.3px; }
.welcome-text { margin-right:14px; color: var(--muted); }
.logout-link { color: #fff; text-decoration:none; padding:8px 12px; border-radius:10px; background: linear-gradient(180deg, #213040 0%, #1a2633 100%); border:1px solid var(--border); transition: all .2s ease; }
.logout-link:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,0.28); }
.brand-logo { width:26px; height:26px; border-radius:6px; background: linear-gradient(180deg,#1f2a36,#16202b); border:1px solid var(--border); margin-right:10px; box-shadow: 0 6px 14px rgba(0,0,0,0.25); }
.sidebar-toggle { display:flex; flex-direction:column; gap:4px; margin-right:12px; border:none; background:transparent; cursor:pointer; padding:6px; border-radius:8px; }
.sidebar-toggle span { width:18px; height:2px; background:#fff; display:block; border-radius:2px; transition:transform .2s ease, opacity .2s ease; }

.admin-sidebar { position:fixed; inset:0 auto 0 0; width:240px; background: linear-gradient(180deg, #131b24 0%, #0f161d 100%); border-right:1px solid var(--border); box-shadow: 8px 0 24px rgba(0,0,0,0.25); padding:18px; overflow-y:auto; transform: translateX(0); transition: transform .25s ease; z-index:1200; }
body.sidebar-collapsed .admin-sidebar { transform: translateX(-100%); }
.sidebar-nav .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px; }
.nav-link { display:block; padding:12px 12px; border-radius:10px; color:#dbe7f1; text-decoration:none; transition: all .2s ease; }
.nav-link:hover { background: linear-gradient(180deg, #1b2633 0%, #15202b 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transform: translateX(2px); }

@media (min-width: 769px) { .admin-header { margin-left:240px; width: calc(100% - 240px); } body.sidebar-collapsed .admin-header { margin-left:0; width:100%; } }

.form-page { margin-left:240px; padding:20px; transition: margin-left .25s ease; }
body.sidebar-collapsed .form-page { margin-left:0; }
.page-title { margin:8px 0 16px; font-size:20px; letter-spacing:.3px; }

.form-card { max-width:780px; background: var(--card); border:1px solid var(--border); border-radius:18px; box-shadow: var(--shadow); padding:18px; animation: fadeUp .45s ease both; }
.form-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; }
.form-group { display:flex; flex-direction:column; gap:8px; }
.form-label { font-weight:600; font-size:13px; color:#d3e1ee; }
.input { width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#1a2430 0%, #121a22 100%); color: var(--text); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transition: box-shadow .2s ease, transform .1s ease, border-color .2s ease; }
.input:focus { outline:none; border-color:#2b7fb8; box-shadow: 0 0 0 3px rgba(52,152,219,0.18); }
.input[type="file"] { padding:10px; }

.image-preview { display:flex; align-items:center; gap:12px; padding:12px; border:1px dashed var(--border); border-radius:12px; background: rgba(255,255,255,0.02); }
.preview-thumb { width:120px; height:80px; object-fit:cover; border-radius:10px; border:1px solid var(--border); box-shadow: 0 6px 14px rgba(0,0,0,0.25); display:none; }
.preview-info { color: var(--muted); font-size:12px; }

.actions { display:flex; justify-content:flex-end; margin-top:14px; }
.btn-submit { display:inline-flex; align-items:center; gap:8px; background: linear-gradient(180deg, #2b7fb8 0%, #1f5e8c 100%); color:#fff; padding:10px 16px; border:none; border-radius:12px; cursor:pointer; box-shadow: var(--shadow); transition: transform .18s ease, box-shadow .18s ease; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,0.35); }

.form-alert { display:none; margin-bottom:12px; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#213040,#1a2633); color:#e6f0f7; }
.form-alert.error { border-color:#a93a2f; background: linear-gradient(180deg,#3b2020,#2b1616); color:#ffd7d3; }
.form-alert.success { border-color:#1f5e8c; background: linear-gradient(180deg,#1b2e40,#172433); color:#d7ecff; }

@keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
@media (max-width: 768px){ .form-page { margin-left:0; padding:16px; } .admin-sidebar { transform: translateX(-100%); } body.sidebar-open .admin-sidebar { transform: translateX(0); } .admin-header h1 { font-size:16px; } .form-grid { grid-template-columns: 1fr; } }
</style>

<div class="form-page">
    <h2 class="page-title">Add New Vehicle</h2>

    <form action="process_add_vehicle.php" method="POST" enctype="multipart/form-data" class="form-card" id="addVehicleForm" novalidate>
        <div class="form-alert" id="formAlert" role="alert"></div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" required class="input" placeholder="e.g., Toyota" aria-label="Brand">
            </div>
            <div class="form-group">
                <label class="form-label">Model</label>
                <input type="text" name="model" required class="input" placeholder="e.g., Corolla" aria-label="Model">
            </div>
            <div class="form-group">
                <label class="form-label">Year</label>
                <input type="number" name="year" required class="input" placeholder="e.g., 2022" aria-label="Year">
            </div>
            <div class="form-group">
                <label class="form-label">Price Per Day</label>
                <input type="number" step="0.01" name="price_per_day" required class="input" placeholder="e.g., 49.99" aria-label="Price per day">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="input" aria-label="Status">
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Vehicle Image</label>
                <input type="file" name="image" accept="image/*" required class="input" id="imageInput" aria-label="Vehicle image">
                <div class="image-preview" id="imagePreviewBox">
                    <img id="imagePreview" class="preview-thumb" alt="Preview">
                    <span class="preview-info">No image selected</span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn-submit">Add Vehicle</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('addVehicleForm');
    var alertBox = document.getElementById('formAlert');
    var imageInput = document.getElementById('imageInput');
    var imagePreview = document.getElementById('imagePreview');
    var imagePreviewBox = document.getElementById('imagePreviewBox');
    var toggle = document.getElementById('sidebarToggle');
    function isMobile(){ return window.matchMedia('(max-width: 768px)').matches; }

    function showAlert(type, message){
        alertBox.className = 'form-alert ' + (type || '');
        alertBox.textContent = message || '';
        alertBox.style.display = message ? 'block' : 'none';
    }

    if(imageInput){
        imageInput.addEventListener('change', function(e){
            var file = e.target.files && e.target.files[0];
            if(file){
                var reader = new FileReader();
                reader.onload = function(ev){
                    imagePreview.src = ev.target.result;
                    imagePreview.style.display = 'block';
                    imagePreviewBox.querySelector('.preview-info').textContent = file.name;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
                imagePreviewBox.querySelector('.preview-info').textContent = 'No image selected';
            }
        });
    }

    if(form){
        form.addEventListener('submit', function(e){
            if(!form.checkValidity()){
                e.preventDefault();
                showAlert('error', 'Please fill in all required fields correctly.');
                form.reportValidity();
                return false;
            }
            showAlert('', '');
        });
    }

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
