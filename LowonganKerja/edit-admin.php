<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') exit;

$conn = new mysqli("localhost","root","","job_portal_db");
$id = $_SESSION['id'];
$data = $conn->query("SELECT name,email FROM users WHERE id=$id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profil</title>
<link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<div class="admin-form-wrapper">
<div class="admin-form-card">

<h2>✏️ Edit Profil Admin</h2>
<p class="form-desc">Perbarui identitas admin yang ditampilkan di sistem</p>

<form method="POST" action="process-admin.php">
<input type="hidden" name="action" value="edit">

<label>Nama Lengkap</label>
<input name="name" value="<?= htmlspecialchars($data['name']) ?>" required>

<label>Email</label>
<input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>

<div class="form-action">
<button class="btn-primary">Simpan Perubahan</button>
<a href="admin-profile.php" class="btn-secondary">Batal</a>
</div>
</form>

</div>
</div>

</body>
</html>
