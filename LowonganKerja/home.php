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
    <title>Home - Job Portal</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Indonesia Berkarir Menuju Indonesia Emas</h1>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#jobs">Jobs</a></li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) { ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="login.php">Logout</a></li>
                    
                <?php } ?>
                </ul>
            </nav>
        </div>
    </header>

    <section id="hero">
        <div class="container">
            <h2>Cari Pekerjaan Impianmu</h2>
            <p>Temukan Ribuan Peluang Pekerjaan dan Lanjutkan Karirmu.</p>
            <form action="#" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search jobs...">
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <section id="jobs">
        <div class="container">
            <h2>Available Jobs</h2>
            <div class="job-list">
            <div class="job-card">
                    <h3>Software Developer</h3>
                    <p class="company">Perusahaan Teknologi</p>
                    <p class="location">Remote</p>
                    <p class="description">Kami mencari pengembang perangkat lunak yang terampil untuk bergabung dengan tim kami.</p>
                    <a href="job_detail.php?job_id=1" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Marketing Spesialist</h3>
                    <p class="company">Perusahaan Pemasaran</p>
                    <p class="location">Jakarta, Indonesia</p>
                    <p class="description">Bergabunglah dengan tim pemasaran kami dan bantu kami mengembangkan merek kami.</p>
                    <a href="job_detail.php?job_id=2" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Data Analyst</h3>
                    <p class="company">Perusahaan Analitik</p>
                    <p class="location">Bandung, Indonesia</p>
                    <p class="description">Kami membutuhkan analis data untuk membantu kami membuat keputusan berbasis data.</p>
                    <a href="job_detail.php?job_id=3" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Graphic Designer</h3>
                    <p class="company">Studio Kreatif</p>
                    <p class="location">Surabaya, Indonesia</p>
                    <p class="description">Kami mencari desainer grafis kreatif untuk membuat desain yang menarik.</p>
                    <a href="job_detail.php?job_id=4" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Project Manager</h3>
                    <p class="company">Perusahaan Konsultan</p>
                    <p class="location">Yogyakarta, Indonesia</p>
                    <p class="description">Kami membutuhkan manajer proyek untuk memimpin dan mengelola proyek-proyek kami.</p>
                    <a href="job_detail.php?job_id=5" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Cyber Security Specialist</h3>
                    <p class="company">Perusahaan Teknologi</p>
                    <p class="location">Remote</p>
                    <p class="description">Kami mencari ahli keamanan siber untuk melindungi sistem dan data kami.</p>
                    <a href="job_detail.php?job_id=6" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Content Writer</h3>
                    <p class="company">Media Online</p>
                    <p class="location">Bali, Indonesia</p>
                    <p class="description">Kami mencari penulis konten yang kreatif untuk menulis artikel dan konten menarik.</p>
                    <a href="job_detail.php?job_id=7" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Customer Service</h3>
                    <p class="company">Perusahaan Retail</p>
                    <p class="location">Medan, Indonesia</p>
                    <p class="description">Kami mencari staf customer service yang ramah dan komunikatif untuk melayani pelanggan.</p>
                    <a href="job_detail.php?job_id=8" class="btn-apply">Lamar Sekarang</a>
                </div>
                <div class="job-card">
                    <h3>Accountant</h3>
                    <p class="company">Perusahaan Keuangan</p>
                    <p class="location">Semarang, Indonesia</p>
                    <p class="description">Kami membutuhkan akuntan untuk mengelola keuangan dan laporan keuangan perusahaan.</p>
                    <a href="job_detail.php?job_id=9" class="btn-apply">Lamar Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Job Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>