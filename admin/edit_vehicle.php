<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("Vehicle ID missing.");
}
$id = $_GET['id'];

$query = "SELECT * FROM cars WHERE id = $id";
$result = mysqli_query($conn, $query);
$vehicle = mysqli_fetch_assoc($result);

if (!$vehicle) {
    die("Vehicle not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root { --bg:#0f141a; --surface:#111820; --card:#141c24; --text:#e8f0f7; --muted:#8aa0b4; --border:#1e2a36; --shadow:0 12px 30px rgba(0,0,0,0.25); --blue:#3498db; --green:#16a085; --red:#e74c3c; }
        * { box-sizing:border-box; }
        body { margin:0; background: linear-gradient(180deg, #0c1217 0%, #0f141a 100%); color: var(--text); font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

        .vehicle-page { max-width: 1000px; margin: 40px auto; padding: 0 16px; animation: fadeUp .45s ease both; }
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .page-title { margin:0; font-size:22px; letter-spacing:0.3px; }

        .form-card { background: var(--card); border:1px solid var(--border); border-radius:18px; box-shadow: var(--shadow); padding:18px; }
        .form-alert { display:none; margin-bottom:12px; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#213040,#1a2633); color:#e6f0f7; }
        .form-alert.error { border-color:#a93a2f; background: linear-gradient(180deg,#3b2020,#2b1616); color:#ffd7d3; }
        .form-alert.success { border-color:#1f5e8c; background: linear-gradient(180deg,#1b2e40,#172433); color:#d7ecff; }

        .form-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; }
        .form-group { display:flex; flex-direction:column; gap:8px; }
        .form-label { font-weight:600; font-size:13px; color:#d3e1ee; }
        .input, .select { width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background: linear-gradient(180deg,#1a2430 0%, #121a22 100%); color: var(--text); box-shadow: inset 0 1px 0 rgba(255,255,255,0.04); transition: box-shadow .2s ease, transform .1s ease, border-color .2s ease; }
        .input:focus, .select:focus { outline:none; border-color:#2b7fb8; box-shadow: 0 0 0 3px rgba(52,152,219,0.18); }

        .image-section { display:grid; grid-template-columns: 160px 1fr; gap:16px; align-items:start; margin-top:12px; }
        .current-image { background: rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:12px; padding:8px; text-align:center; }
        .current-image img { width:140px; height:100px; object-fit:cover; border-radius:10px; border:1px solid var(--border); box-shadow: 0 6px 14px rgba(0,0,0,0.25); }

        .upload-box { padding:12px; border:1px dashed var(--border); border-radius:12px; background: rgba(255,255,255,0.02); }
        .preview-thumb { width:160px; height:110px; object-fit:cover; border-radius:10px; border:1px solid var(--border); box-shadow: 0 6px 14px rgba(0,0,0,0.25); display:none; }
        .preview-info { color: var(--muted); font-size:12px; }

        .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:16px; }
        .btn { display:inline-flex; align-items:center; gap:8px; text-decoration:none; border-radius:12px; padding:10px 16px; border:1px solid var(--border); transition: all .18s ease; cursor:pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,0.35); }
        .btn-primary { background: linear-gradient(180deg, #2b7fb8 0%, #1f5e8c 100%); color:#fff; }
        .btn-secondary { background: linear-gradient(180deg, #203040 0%, #182534 100%); color:#dbe7f1; }

        @keyframes fadeUp { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
        @media (max-width: 768px){ .form-grid { grid-template-columns: 1fr; } .image-section { grid-template-columns: 1fr; } .page-title { font-size:20px; } }
    </style>
</head>
<body>

<div class="vehicle-page">
    <div class="page-header">
        <h2 class="page-title">Edit Vehicle</h2>
    </div>

    <div class="form-card">
        <div class="form-alert" id="formMsg" role="alert"></div>
        <form action="process_edit_vehicle.php" method="POST" enctype="multipart/form-data" id="editVehicleForm" novalidate>
            <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Plate Number</label>
                    <input type="text" name="plate_number" class="input" value="<?php echo $vehicle['plate_number']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="input" value="<?php echo $vehicle['model']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="input" value="<?php echo $vehicle['brand']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="input" value="<?php echo $vehicle['year']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="input" value="<?php echo $vehicle['capacity']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Transmission</label>
                    <input type="text" name="transmission" class="input" value="<?php echo $vehicle['transmission']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Fuel Type</label>
                    <input type="text" name="fuel_type" class="input" value="<?php echo $vehicle['fuel_type']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price Per Day</label>
                    <input type="number" name="price_per_day" class="input" value="<?php echo $vehicle['price_per_day']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="select">
                        <option value="active" <?php if($vehicle['status']=="active") echo "selected"; ?>>Active</option>
                        <option value="inactive" <?php if($vehicle['status']=="inactive") echo "selected"; ?>>Inactive</option>
                        <option value="maintenance" <?php if($vehicle['status']=="maintenance") echo "selected"; ?>>Maintenance</option>
                        <option value="booked" <?php if($vehicle['status']=="booked") echo "selected"; ?>>booked</option>
                    </select>
                </div>
            </div>

            <div class="image-section">
                <div class="current-image">
                    <div class="form-label">Current Image</div>
                    <img src="uploads/<?php echo $vehicle['image_url']; ?>" alt="Current image">
                </div>
                <div class="upload-box">
                    <div class="form-group">
                        <label class="form-label">Upload New Image</label>
                        <input type="file" name="image" id="newImageInput" class="input" accept="image/*">
                    </div>
                    <div class="form-group">
                        <img id="newImagePreview" class="preview-thumb" alt="New image preview">
                        <span class="preview-info">No image selected</span>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary" id="saveBtn">Update Vehicle</button>
                <a href="manage_vehicles.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('editVehicleForm');
    var msg = document.getElementById('formMsg');
    var preview = document.getElementById('newImagePreview');
    var previewInfo = document.querySelector('.preview-info');
    var imageInput = document.getElementById('newImageInput');

    function showMessage(type, text){
        msg.className = 'form-alert ' + (type || '');
        msg.textContent = text || '';
        msg.style.display = text ? 'block' : 'none';
    }

    function isPositiveNumber(v){
        var n = Number(v);
        return Number.isFinite(n) && n > 0;
    }

    if(imageInput){
        imageInput.addEventListener('change', function(e){
            var file = e.target.files && e.target.files[0];
            if(file){
                var reader = new FileReader();
                reader.onload = function(ev){
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    if(previewInfo) previewInfo.textContent = file.name;
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
                if(previewInfo) previewInfo.textContent = 'No image selected';
            }
        });
    }

    if(form){
        form.addEventListener('submit', function(e){
            var requiredFields = ['plate_number','model','brand','year','capacity','transmission','fuel_type','price_per_day'];
            for(var i=0;i<requiredFields.length;i++){
                var f = form.querySelector('[name="'+requiredFields[i]+'"]');
                if(!f || !String(f.value || '').trim()){
                    e.preventDefault();
                    showMessage('error','Please fill in all required fields.');
                    return false;
                }
            }
            if(!isPositiveNumber(form.year.value) || !isPositiveNumber(form.capacity.value) || !isPositiveNumber(form.price_per_day.value)){
                e.preventDefault();
                showMessage('error','Year, capacity, and price must be positive numbers.');
                return false;
            }
            showMessage('', '');
        });
    }
});
</script>

</body>
</html>
