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

// Ambil job_id dari URL
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Query untuk menghapus lamaran kerja
    $sql = "DELETE FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        // Redirect kembali ke menu-admin.php setelah penghapusan
        header("Location: menu-admin.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
