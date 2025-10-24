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

// Proses tambah job
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Query untuk tambah job
    $sql = "INSERT INTO jobs (job_title, company, location, description) VALUES ('$job_title', '$company', '$location', '$description')";
    if ($conn->query($sql) === TRUE) {
        $success = "Job berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan job!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job - Job Portal</title>
    <link rel="stylesheet" href="add-job.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Job Portal</h1>
            <nav>
                <ul>
                    <li><a href="menu-admin.php">Home</a></li>
                    <li><a href="add-job.php">Add Job</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="add-job">
        <div class="container">
            <h2>Add Job</h2>
            <p>Tambahkan job baru di sini.</p>
            <form action="add-job.php" method="POST">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" required>
                <label for="company">Company:</label>
                <input type="text" id="company" name="company" required>
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
                <button type="submit">Add Job</button>
            </form>
            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Job Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>