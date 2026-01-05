<?php
session_start();

/* ===============================
   VALIDASI LOGIN & ROLE
================================ */
if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['admin','hr'])) {
    header("Location: login.php");
    exit;
}

/* ===============================
   VALIDASI ID
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID job tidak valid";
    header("Location: ".$_SESSION['role'] === 'admin' ? "menu-admin.php" : "menu-hr.php");
    exit;
}

$job_id = (int) $_GET['id'];
$role   = $_SESSION['role'];
$company = $_SESSION['company'] ?? null;

/* ===============================
   KONEKSI DB
================================ */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

/* ===============================
   AMBIL JOB + VALIDASI AKSES
================================ */
if ($role === 'admin') {

    // ADMIN → bebas hapus semua job
    $get = $conn->prepare("SELECT logo FROM jobs WHERE id=?");
    $get->bind_param("i", $job_id);

} else {

    // HR → hanya job company sendiri
    $get = $conn->prepare("SELECT logo FROM jobs WHERE id=? AND company=?");
    $get->bind_param("is", $job_id, $company);
}

$get->execute();
$result = $get->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Akses ditolak / job tidak ditemukan";
    header("Location: menu-hr.php");
    exit;
}

$row  = $result->fetch_assoc();
$logo = $row['logo'];

/* ===============================
   HAPUS JOB
================================ */
$del = $conn->prepare("DELETE FROM jobs WHERE id=?");
$del->bind_param("i", $job_id);

if ($del->execute()) {

    // Hapus file logo jika ada
    if ($logo && file_exists("assets/img/LogoPerusahaan/".$logo)) {
        unlink("assets/img/LogoPerusahaan/".$logo);
    }

    $_SESSION['success'] = "Lowongan berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus lowongan";
}

/* ===============================
   REDIRECT SESUAI ROLE
================================ */
if ($role === 'admin') {
    header("Location: menu-admin.php");
} else {
    header("Location: menu-hr.php");
}

exit;
