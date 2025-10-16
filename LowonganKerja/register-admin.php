<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root"; // Default username XAMPP
$password = ""; // Default password XAMPP (kosong)
$dbname = "job_portal_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses daftar akun admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Query untuk memeriksa email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {
        if ($password == $confirm_password) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Query untuk daftar akun admin
            $sql = "INSERT INTO users (email, password, role) VALUES ('$email', '$hashed_password', 'admin')";            if ($conn->query($sql) === TRUE) {
                $success = "Akun admin berhasil dibuat!";
            } else {
                $error = "Gagal membuat akun admin!";
            }
        } else {
            $error = "Password tidak cocok!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Admin - Job Portal</title>
    <link rel="stylesheet" href="register.css">
 <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="register-container">
        <h2>Daftar Akun Admin</h2>
        <p>Buat akun admin untuk mengelola job portal.</p>
        
        <!-- Tampilkan pesan error jika ada -->
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Tampilkan pesan sukses jika ada -->
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form class="register-form" action="register-admin.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login-admin.php">Login di sini</a></p>
    </div>
</body>
</html>