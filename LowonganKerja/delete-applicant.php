<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login-admin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "job_portal_db");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak valid");
}

$stmt = $conn->prepare("DELETE FROM applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
