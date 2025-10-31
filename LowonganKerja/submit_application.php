<?php
// Mulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit;
}

// Proses pengiriman lamaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $job_id = $_POST['job_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $resume = $_FILES['resume']['name']; // Ambil nama file

    // Koneksi ke database
    $servername = "localhost";
    $username = "root"; // Ganti dengan username database Anda
    $password = ""; // Ganti dengan password database Anda
    $dbname = "job_portal_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Simpan data lamaran ke database
    $user_id = $_SESSION['id']; // Ambil ID pengguna dari session
    $sql = "INSERT INTO applications (job_id, user_id, full_name, email, phone, resume) 
            VALUES ('$job_id', '$user_id', '$full_name', '$email', '$phone', '$resume')";

    if ($conn->query($sql) === TRUE) {
        echo "Lamaran Anda telah berhasil dikirim!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Terkirim - Job Portal</title>
    <link rel="stylesheet" href="submit_application.css">
</head>
<body>
    <div class="container">
        <h1>Lamaran Terkirim</h1>
        <div class="success-message">
            <p>Lamaran Anda telah berhasil dikirim!</p>
            <a href="home.php" class="btn-back">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>