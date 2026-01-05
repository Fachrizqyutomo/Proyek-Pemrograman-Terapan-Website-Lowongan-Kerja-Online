<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'hr') {
    header("Location: login-hr.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

$hr_id = $_SESSION['id'];
$error = '';

/* ================= UPDATE PROFILE ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);

    $getFoto = $conn->prepare("SELECT foto FROM users WHERE id=?");
    $getFoto->bind_param("i", $hr_id);
    $getFoto->execute();
    $old = $getFoto->get_result()->fetch_assoc();
    $foto = $old['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            $error = "Format foto harus JPG / PNG / WEBP";
        } else {
            $foto = "hr_" . uniqid() . "." . $ext;
            move_uploaded_file(
                $_FILES['foto']['tmp_name'],
                "assets/img/profile/" . $foto
            );
        }
    }

    if (!$error) {
        $update = $conn->prepare("UPDATE users SET name=?, foto=? WHERE id=?");
        $update->bind_param("ssi", $name, $foto, $hr_id);

        if ($update->execute()) {
            $_SESSION['success'] = "Profil berhasil diperbarui";
            header("Location: hr-profile.php");
            exit;
        } else {
            $error = "Gagal update profil";
        }
    }
}

/* ================= DATA HR ================= */
$stmt = $conn->prepare("SELECT name,email,company,foto FROM users WHERE id=?");
$stmt->bind_param("i", $hr_id);
$stmt->execute();
$hr = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil HR</title>

<link rel="stylesheet" href="assets/css/index.css">

<style>
/* ==== RESET GLOBAL IMAGE ==== */
.profile-avatar{
    display:flex;
    justify-content:center;
    align-items:center;
    margin-bottom:20px;
    width:100%;
}

.profile-avatar img{
    width:140px;
    height:140px;
    border-radius:50%;
    object-fit:cover;
    background:#fff;
    border:3px solid #ccc;

    /* 🔥 FIX IMAGE ISSUE */
    filter:none !important;
    opacity:1 !important;
    mix-blend-mode:normal !important;
}

/* ==== PROFILE CARD ==== */
.profile-box{
    max-width:520px;
    margin:50px auto;
    background:#ffffff;
    padding:35px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
    color:#000;
}

/* ==== TEXT FIX ==== */
.profile-box h2,
.profile-box label,
.profile-box p{
    color:#000 !important;
}

/* ==== INPUT ==== */
.profile-box input{
    width:100%;
    padding:10px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:6px;
    background:#fff;
    color:#000;
}

/* ==== BUTTON SAVE ==== */
.btn-save{
    width:100%;
    padding:12px;
    background:#4CAF50;
    color:#fff !important;
    border:none;
    border-radius:8px;
    font-size:15px;
    cursor:pointer;
}

/* ==== BUTTON CHANGE PASSWORD ==== */
.btn-password{
    display:block;
    text-align:center;
    margin-top:18px;
    padding:12px;
    background:#0d6efd;
    color:#fff !important; /* 🔥 FIX PUTIH */
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
}

.btn-password:hover{
    background:#0b5ed7;
}

/* ==== MESSAGE ==== */
.success-message{
    background:#d4edda;
    color:#155724;
    padding:10px;
    margin-bottom:15px;
    border-radius:6px;
}

.error-message{
    background:#f8d7da;
    color:#721c24;
    padding:10px;
    margin-bottom:15px;
    border-radius:6px;
}
</style>

</head>

<body>

<header class="navbar">
  <div class="container nav-flex">
    <h1 class="logo">HR Panel</h1>
    <nav>
      <a href="menu-hr.php">Dashboard</a>
      <a href="logout.php" class="btn-logout">Logout</a>
    </nav>
  </div>
</header>

<div class="profile-box">

<h2>Profil HR</h2>

<?php if ($error): ?>
<div class="error-message"><?= $error ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
<div class="success-message"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="profile-avatar">
<img src="assets/img/profile/<?= $hr['foto'] ?: 'default.png'; ?>?v=<?= time(); ?>">
</div>

<label>Nama HR</label>
<input type="text" name="name" value="<?= htmlspecialchars($hr['name']); ?>" required><br><br>

<label>Email</label>
<input type="email" value="<?= htmlspecialchars($hr['email']); ?>" disabled><br><br>

<label>Perusahaan</label>
<input type="text" value="<?= htmlspecialchars($hr['company']); ?>" disabled><br><br>

<label>Foto Profil</label>
<input type="file" name="foto" accept="image/*">

<br><br>
<button type="submit" class="btn-save">Simpan Perubahan</button>

</form>

<a href="hr-change-password.php" class="btn-password">
    🔒 Ganti Password
</a>

</div>

</body>
</html>
