<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login-admin.php"); exit;
}

$conn = new mysqli("localhost","root","","job_portal_db");

$id = $_SESSION['id'];
$q = $conn->prepare("SELECT name,email,foto,created_at FROM users WHERE id=?");
$q->bind_param("i",$id);
$q->execute();
$admin = $q->get_result()->fetch_assoc();

$foto = $admin['foto']
    ? "assets/img/avatar/".$admin['foto']
    : "assets/img/avatar/default.png";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<title>Profil Admin</title>
<link rel="stylesheet" href="assets/css/index.css">
</head>
<body class="admin-profile-page">
<div class="profile-container">

<div class="profile-glass">

    <div class="profile-header">
        <div class="avatar-ring">
            <img src="<?= $foto ?>" class="profile-avatar">
        </div>
<h2 class="profile-name"><?= htmlspecialchars($admin['name']) ?></h2>
<p class="profile-email"><?= htmlspecialchars($admin['email']) ?></p>

</div>

    <div class="profile-stats">
        <div class="stat">
            <span class="label">Role</span>
            <span class="value">Administrator</span>
        </div>
        <div class="stat">
            <span class="label">Bergabung</span>
            <span class="value"><?= date('d M Y',strtotime($admin['created_at'])) ?></span>
        </div>
    </div>

    <div class="profile-actions">
        <a href="edit-admin.php" class="action-btn edit">✏️ Edit Profil</a>
        <a href="change-password.php" class="action-btn password">🔒 Ganti Password</a>
        <a href="upload-foto.php" class="action-btn upload">📤 Upload Foto</a>
    </div>

    <a href="menu-admin.php" class="back-btn">← Kembali ke Dashboard</a>

</div>

</div>

</body>
</html>
