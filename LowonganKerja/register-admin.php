<?php
session_start();

// ==================
// KONEKSI DATABASE
// ==================
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// ==================
// PROSES REGISTER ADMIN
// ==================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email            = mysqli_real_escape_string($conn, $_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek email
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");

    if ($check->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {

        if ($password !== $confirm_password) {
            $error = "Password tidak cocok!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (email, password, role)
                    VALUES ('$email', '$hashed_password', 'admin')";

            if ($conn->query($sql)) {
                $success = "Akun admin berhasil dibuat!";
            } else {
                $error = "Gagal membuat akun admin!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin | Job Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
    <style>
        body {


            /* BACKGROUND GAMBAR */
            background: url('assets/img/bg/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;

        }
    </style>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <h2>Register Admin</h2>
        <span>Buat akun administrator</span>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="📧 Email Admin" required>
            <input type="password" name="password" placeholder="🔒 Password" required>
            <input type="password" name="confirm_password" placeholder="🔁 Konfirmasi Password" required>
            <button type="submit">DAFTAR ADMIN</button>
        </form>

        <div class="links">
            <a href="login-admin.php">← Login Admin</a>
        </div>

    </div>
</div>

</body>
</html>
