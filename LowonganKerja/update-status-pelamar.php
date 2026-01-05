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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status - Job Portal</title>
    <link rel="stylesheet" href="assets/css/update-status.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Job Portal</h1>
            <nav>
                <ul>
                    <li><a href="menu-admin.php">Home</a></li>
                    <li><a href="add-job.php">Add Job</a></li>
                    <li><a href="update-status.php">Update Status</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="update-status">
        <div class="container">
            <h2>Update Status</h2>
            <p>Update status lamaran kerja di sini.</p>
            <form action="update-status.php" method="POST">
                <label for="job_id">Job ID:</label>
                <input type="text" id="job_id" name="job_id" required>
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Job Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>