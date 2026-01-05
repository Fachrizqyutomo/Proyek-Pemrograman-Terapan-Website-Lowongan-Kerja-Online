<?php
session_start();

/* =====================
   CEK LOGIN
===================== */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

/* =====================
   KONEKSI DB
===================== */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

/* =====================
   AMBIL USER ID DARI EMAIL
===================== */
$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$userId = $user['id'];

/* =====================
   CEK FILE
===================== */
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
    $_SESSION['error'] = "File tidak valid.";
    header("Location: profile.php");
    exit;
}

/* =====================
   UPLOAD FOTO
===================== */
$ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($ext, $allowed)) {
    $_SESSION['error'] = "Format foto harus JPG, PNG, atau WEBP.";
    header("Location: profile.php");
    exit;
}

$folder = "foto/";
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$namaBaru = uniqid("avatar_") . "." . $ext;
move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $namaBaru);

/* =====================
   UPDATE DATABASE
===================== */
$update = $conn->prepare("UPDATE users SET foto = ? WHERE id = ?");
$update->bind_param("si", $namaBaru, $userId);
$update->execute();

$_SESSION['success'] = "Foto profil berhasil diperbarui!";
header("Location: profile.php");
exit;
