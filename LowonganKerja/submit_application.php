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
   KONEKSI DATABASE
===================== */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

/* =====================
   AMBIL DATA (AMAN)
===================== */
$job_id    = $_POST['job_id'] ?? 0;
$full_name = $_POST['full_name'] ?? '';
$phone     = $_POST['phone'] ?? '';

$user_id = $_SESSION['user_id'] ?? 0;
$email   = $_SESSION['email'] ?? '';

if ($job_id == 0 || $user_id == 0) {
    die("Data lamaran tidak lengkap.");
}

/* =====================
   CEK SUDAH MELAMAR
===================== */
$cek = $conn->prepare("
    SELECT id FROM applications 
    WHERE job_id = ? AND user_id = ?
");
$cek->bind_param("ii", $job_id, $user_id);
$cek->execute();

if ($cek->get_result()->num_rows > 0) {
    die("Anda sudah melamar pekerjaan ini.");
}

/* =====================
   UPLOAD RESUME
===================== */
if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== 0) {
    die("Upload CV gagal.");
}

$folder = "uploads/";
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
$filename = time() . "_" . $user_id . "." . $ext;
$path = $folder . $filename;

move_uploaded_file($_FILES['resume']['tmp_name'], $path);

/* =====================
   SIMPAN DATABASE
===================== */
$stmt = $conn->prepare("
    INSERT INTO applications 
    (job_id, user_id, full_name, email, phone, resume)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iissss",
    $job_id,
    $user_id,
    $full_name,
    $email,
    $phone,
    $path
);

$stmt->execute();
$conn->close();

/* =====================
   REDIRECT
===================== */
header("Location: job_detail.php?id=$job_id&success=1");
exit;

$notif = $conn->prepare("
    INSERT INTO notifications (user_id, message)
    VALUES (?, ?)
");
$msg = "Lamaran untuk posisi $job_title berhasil dikirim";
$notif->bind_param("is", $user_id, $msg);
$notif->execute();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Terkirim - Job Portal</title>
    <link rel="stylesheet" href="assets/css/submit_application.css">
</head>
<body>

<div class="success-wrapper">

    <div class="success-card">
        <div class="icon-success">✔</div>

        <h1>Lamaran Berhasil Dikirim</h1>
        <p>
            Terima kasih telah melamar.  
            Data lamaran Anda sudah kami terima dan akan segera diproses.
        </p>

        <div class="action-btn">
            <a href="index.php">Kembali ke Beranda</a>
            <a href="profile.php" class="secondary">Lihat Status Lamaran</a>
        </div>
    </div>

</div>

</body>

</html>