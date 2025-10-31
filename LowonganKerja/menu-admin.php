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
    <title>Admin - Job Portal</title>
    <link rel="stylesheet" href="menu-admin.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Job Portal Admin</h1>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#jobs">Jobs</a></li>
                    <li><a href="add-job.php">Add Job</a></li>
                    <li><a href="update-status.php">Update Lamaran</a></li>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="hero">
        <div class="container">
            <h2>Selamat Datang di Job Portal Admin</h2>
            <p>Anda dapat menambahkan lamaran kerja dan mengupdate status lamaran kerja di sini.</p>
        </div>
    </section>

    <section id="jobs">
    <div class="container">
        <h2>Available Jobs</h2>
        <div class="job-list">
            <!-- Tampilkan daftar lamaran kerja -->
            <?php
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

            // Query untuk menampilkan daftar lamaran kerja
            $sql = "SELECT * FROM jobs";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='job-card'>";
                    echo "<h3>" . $row['job_title'] . "</h3>";
                    echo "<p class='company'>" . $row['company'] . "</p>";
                    echo "<p class='location'>" . $row['location'] . "</p>";
                    echo "<p class='description'>" . $row['description'] . "</p>";
                    echo "<a href='update-status.php?job_id=" . $row['id'] . "' class='btn-update-lamaran'><i class='fa fa-edit'></i> Update Lamaran</a>";
                    echo "<a href='delete-job.php?job_id=" . $row['id'] . "' class='btn-delete-lamaran' onclick='return confirm(\"Are you sure you want to delete this job?\");'><i class='fa fa-trash'></i> Delete Job</a>";
                    echo "</div>";
                }
            } else {
                echo "Tidak ada lamaran kerja.";
            }

            $conn->close();
            ?>
        </div><br>
    </div>
</section>
    <footer>
        <div class="container">
            <p>&copy; 2025 Job Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
