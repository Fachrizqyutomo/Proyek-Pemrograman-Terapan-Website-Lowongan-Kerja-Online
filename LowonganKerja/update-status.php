<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login-admin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "job_portal_db");

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$id || !$status) {
    die("Parameter tidak valid");
}

/* =========================
   AMBIL USER_ID PELAMAR
========================= */
$qUser = $conn->prepare("
    SELECT user_id 
    FROM applications 
    WHERE id = ?
");
$qUser->bind_param("i", $id);
$qUser->execute();
$user = $qUser->get_result()->fetch_assoc();

if (!$user) {
    die("Pelamar tidak ditemukan");
}

$user_id = $user['user_id'];

/* =========================
   UPDATE STATUS LAMARAN
========================= */
$stmt = $conn->prepare("
    UPDATE applications 
    SET status = ? 
    WHERE id = ?
");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

/* =========================
   SIMPAN NOTIFIKASI
========================= */
$message = "Status lamaran Anda telah $status";

$notif = $conn->prepare("
    INSERT INTO notifications (user_id, message, is_read)
    VALUES (?, ?, 0)
");
$notif->bind_param("is", $user_id, $message);
$notif->execute();

/* =========================
   BALIK KE HALAMAN SEBELUMNYA
========================= */
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
