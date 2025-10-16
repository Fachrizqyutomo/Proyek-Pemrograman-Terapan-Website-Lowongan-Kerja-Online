<?php
// Konfigurasi database
$host = "localhost"; // Host server (biasanya "localhost")
$user = "root";      // Username database (default di XAMPP adalah "root")
$password = "";      // Password database (kosong secara default di XAMPP)
$dbname = "job_portal_db"; // Nama database Anda

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    echo "Koneksi berhasil ke database " . $dbname;
}
?>