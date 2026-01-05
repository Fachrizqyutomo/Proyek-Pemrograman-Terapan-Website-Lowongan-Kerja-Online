<?php
session_start();

$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $company  = mysqli_real_escape_string($conn, $_POST['company']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {

        if ($password !== $confirm) {
            $error = "Password tidak cocok!";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "
                INSERT INTO users (email, password, role, company)
                VALUES ('$email', '$hash', 'hr', '$company')
            ";

            if ($conn->query($sql)) {
                $success = "Akun HR berhasil dibuat!";
            } else {
                $error = "Gagal membuat akun HR!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register HR | Job Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<style>
body {
    background: url('assets/img/bg/bg-login.jpg') no-repeat center center fixed;
    background-size: cover;
}
</style>

<body>

<div class="login-wrapper">
    <div class="login-card">

        <h2>Register HR</h2>
        <span>Buat akun HR perusahaan</span>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="📧 Email HR" required>
            <input type="text" name="company" placeholder="🏢 Nama Perusahaan" required>
            <input type="password" name="password" placeholder="🔒 Password" required>
            <input type="password" name="confirm_password" placeholder="🔁 Konfirmasi Password" required>

            <button type="submit">DAFTAR HR</button>
        </form>

        <div class="links">
            <a href="login-hr.php">← Login HR</a>
        </div>

    </div>
</div>

</body>
</html>
