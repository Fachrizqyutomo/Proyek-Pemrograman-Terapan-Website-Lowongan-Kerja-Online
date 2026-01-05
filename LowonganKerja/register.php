<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job_portal_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses register
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password)
                VALUES ('$name', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Job Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- PAKAI CSS LOGIN -->
    <link rel="stylesheet" href="assets/css/login.css">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;

            /* background sama persis login */
            background: url('assets/img/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;

            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <h2>Job Portal</h2>
        <span>Buat akun baru</span>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <input type="text" name="name" placeholder="👤 Nama Lengkap" required>
            <input type="email" name="email" placeholder="📧 Email" required>
            <input type="password" name="password" placeholder="🔒 Password" required>
            <input type="password" name="confirm_password" placeholder="🔒 Konfirmasi Password" required>

            <button type="submit">REGISTER</button>
        </form>

        <div class="links">
            <a href="login.php">Sudah punya akun? Login</a>
        </div>

    </div>
</div>

</body>
</html>

