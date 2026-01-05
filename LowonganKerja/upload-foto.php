<!DOCTYPE html>
<html>
<head>
<title>Upload Foto Profil</title>
<link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<div class="admin-form-wrapper">
<div class="admin-form-card">

<h2>📤 Upload Foto Profil</h2>
<p class="form-desc">
Gunakan foto formal (JPG / PNG) agar terlihat profesional
</p>

<form method="POST" action="process-admin.php" enctype="multipart/form-data">
<input type="hidden" name="action" value="foto">

<div class="upload-box">
<input type="file" name="foto" accept="image/*" required>
<p class="upload-hint">Ukuran maksimal 2MB</p>
</div>

<div class="form-action">
<button class="btn-primary">Upload Foto</button>
<a href="admin-profile.php" class="btn-secondary">Batal</a>
</div>
</form>

</div>
</div>

</body>
</html>
