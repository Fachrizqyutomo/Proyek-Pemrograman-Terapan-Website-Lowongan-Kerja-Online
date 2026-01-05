<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost","root","","job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

$user_id = $_SESSION['user_id']; // PENTING: user_id
$app_id  = $_GET['id'] ?? null;

if (!$app_id) {
    die("ID lamaran tidak valid");
}

$stmt = $conn->prepare("
    UPDATE applications
    SET status = 'Withdrawn'
    WHERE id = ? AND user_id = ? AND status = 'Pending'
");

$stmt->bind_param("ii", $app_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = "Lamaran berhasil ditarik.";
} else {
    $_SESSION['error'] = "Lamaran gagal ditarik atau sudah diproses.";
}

header("Location: profile.php");
exit;
