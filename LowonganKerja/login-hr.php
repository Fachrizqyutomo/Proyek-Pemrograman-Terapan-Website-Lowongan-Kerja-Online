<?php
session_start();

$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='hr'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['loggedin'] = true;
            $_SESSION['id']       = $row['id'];
            $_SESSION['email']    = $row['email'];
            $_SESSION['role']     = 'hr';
            $_SESSION['company']  = $row['company'];

            header("Location: menu-hr.php");
            exit;
        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Akun HR tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login HR | Job Portal</title>

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

        <h2>HR Panel</h2>
        <span>Login sebagai HR</span>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="📧 Email HR" required>
            <input type="password" name="password" placeholder="🔒 Password" required>
            <button type="submit">LOGIN HR</button>
        </form>

        <div class="links">
            <a href="register-hr.php" class="admin">+ Buat Akun HR</a>
            <a href="login.php">← Login User</a>
        </div>

    </div>
</div>

</body>
</html>
