<?php
session_start();

/* =====================
   CEK LOGIN
===================== */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

/* =====================
   KONEKSI DB
===================== */
$conn = new mysqli("localhost","root","","job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

/* =====================
   AMBIL USER ID DARI EMAIL
===================== */
$email = $_SESSION['email'];

$stmtUser = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();

$userId = $user['id'];
$hashedPassword = $user['password'];

$message = "";

/* =====================
   PROSES GANTI PASSWORD
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $message = "Password baru tidak sama";
    } elseif (!password_verify($old, $hashedPassword)) {
        $message = "Password lama salah";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $up = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $up->bind_param("si",$hash,$userId);
        $up->execute();

        $message = "Password berhasil diubah";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ganti Password</title>
<link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

<div class="change-pass-wrapper">
<div class="change-pass-card">

    <h2>🔒 Ganti Password</h2>
    <p class="sub">Jaga keamanan akun kamu</p>

    <?php if($message): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="password" name="old_password" placeholder="Password Lama" required>
        <input type="password" name="new_password" placeholder="Password Baru" required>
        <input type="password" name="confirm_password" placeholder="Ulangi Password Baru" required>

        <button type="submit">Simpan Password</button>
    </form>

    <a href="admin-profile.php" class="back-link">← Kembali ke Profile</a>

</div>
</div>

</body>
</html>
