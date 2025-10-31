<?php
// Mulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit;
}

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

// Ambil data pengguna dari database
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
} else {
    $error = "Terjadi kesalahan: " . $conn->error;
}

// Upload foto
if (isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];
    $nama_foto = $foto['name'];
    $tmp_foto = $foto['tmp_name'];
    $ukuran_foto = $foto['size'];
    $tipe_foto = $foto['type'];

    // Periksa tipe foto
    if ($tipe_foto == 'image/jpeg' || $tipe_foto == 'image/png') {
        // Upload foto ke direktori
        $direktori = 'foto/';
        $nama_foto_baru = $email . '_' . $nama_foto;
        $path_foto = $direktori . $nama_foto_baru;

        if (move_uploaded_file($tmp_foto, $path_foto)) {
            // Update foto di database
            $sql = "UPDATE users SET foto = '$nama_foto_baru' WHERE email = '$email'";
            $conn->query($sql);

            // Kembali ke menu profile
            header("Location: profile.php");
            exit;
        } else {
            // Tampilkan pesan gagal
            echo "Foto gagal diupload!";
        }
    } else {
        // Tampilkan pesan gagal
        echo "Tipe foto tidak didukung!";
    }
}

$conn->close();
?>