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

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query untuk memeriksa email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verifikasi password
if (password_verify($password, $row['password'])) {
    // Login berhasil
    $_SESSION['loggedin'] = true;
    $_SESSION['user_id'] = $row['id']; // ⬅️ PENTING
    $_SESSION['email'] = $row['email'];
    header("Location: loading.php");
    exit;
}
 else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Job Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/LOWONGANKERJA/assets/css/login.css">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;

            /* BACKGROUND GAMBAR */
            background: url('assets/img/bg/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;

            /* biar login tetap di tengah */
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <h2>Job Portal</h2>
        <span>Login untuk melanjutkan</span>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="📧 Email" required>
            <input type="password" name="password" placeholder="🔒 Password" required>
            <button type="submit">LOGIN</button>
        </form>

        <div class="links">
            <a href="register.php">Daftar Akun</a>
            <a href="login-hr.php" class="admin">Login HR</a>
        </div>

    </div>
</div>

</body>
</html>


