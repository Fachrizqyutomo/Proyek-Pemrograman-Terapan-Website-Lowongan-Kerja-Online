<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'hr') {
    header("Location: login-hr.php");
    exit;
}

/* ================= DB ================= */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

$hr_id = $_SESSION['id'];
$error = '';
$success = '';

/* ================= HANDLE SUBMIT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!$old || !$new || !$confirm) {
        $error = "Semua field wajib diisi";
    } elseif ($new !== $confirm) {
        $error = "Password baru & konfirmasi tidak cocok";
    } elseif (strlen($new) < 6) {
        $error = "Password minimal 6 karakter";
    } else {

        // ambil password lama
        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param("i", $hr_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!password_verify($old, $user['password'])) {
            $error = "Password lama salah";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $update->bind_param("si", $hash, $hr_id);

            if ($update->execute()) {
                $success = "Password berhasil diubah";
            } else {
                $error = "Gagal mengubah password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ganti Password HR</title>
<link rel="stylesheet" href="assets/css/index.css">

<style>
.pass-box{
    max-width:420px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
    color:#000;
}
.pass-box h2{
    text-align:center;
    margin-bottom:20px;
    color:#000;
}
.pass-box label{
    font-weight:600;
    margin-top:15px;
    display:block;
}
.pass-box input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border:1px solid #ccc;
    border-radius:6px;
}
.btn-save{
    width:100%;
    padding:12px;
    background:#0d6efd;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:15px;
    cursor:pointer;
    margin-top:20px;
}
.btn-save:hover{
    background:#0b5ed7;
}
.btn-back{
    display:block;
    text-align:center;
    margin-top:15px;
    color:#0d6efd;
    text-decoration:none;
    font-weight:600;
}
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
      <a href="hr-profile.php">Profile</a>
      <a href="logout.php" class="btn-logout">Logout</a>
    </nav>
  </div>
</header>

<div class="pass-box">

<h2>Ganti Password</h2>

<?php if ($error): ?>
<div class="error-message"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="success-message"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<label>Password Lama</label>
<input type="password" name="old_password" required>

<label>Password Baru</label>
<input type="password" name="new_password" required>

<label>Konfirmasi Password Baru</label>
<input type="password" name="confirm_password" required>

<button class="btn-save">Simpan Password</button>

</form>

<a href="hr-profile.php" class="btn-back">← Kembali ke Profil</a>

</div>

</body>
</html>
