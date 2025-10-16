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
    if (isset($row['foto'])) {
        $foto = $row['foto'];
    } else {
        $foto = '';
    }
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
    <title>Profil - Job Portal</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profil-container">
        <h2>Profil</h2>
        <div class="profil-info">
            <img src="foto/<?php echo $foto; ?>" alt="Foto Profil ">
            <h3><?php echo $name; ?></h3>
            <p>Email: <?php echo $email; ?></p>
            <form action="upload_foto.php" method="post" enctype="multipart/form-data">
                <input type="file" name="foto" required>
                <button type="submit">Upload Foto</button>
            </form>
        </div>
        <div class="daftar-lamaran">
    <h3>Daftar Lamaran Pekerjaan</h3>
    <ul>
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

        // Ambil data lamaran pekerjaan dari database
        $sql = "SELECT * FROM applications WHERE user_id = '".$_SESSION['id']."'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<h4>Pekerjaan: " . $row['job_id'] . "</h4>";
                echo "<p>Nama: " . $row['full_name'] . "</p>";
                echo "<p>Email: " . $row['email'] . "</p>";
                echo "<p>Telepon: " . $row['phone'] . "</p>";
                echo "<p>Resume: " . $row['resume'] . "</p>";
                echo "<p>Status: <strong>" . $row['status'] . "</strong></p>"; // Tampilkan status
                echo "</li>";
            }
        } else {
            echo "<p>Tidak ada lamaran pekerjaan.</p>";
        }

        $conn->close();
        ?>
    </ul>
</div>
    </div>
    <button type="button" class="btn btn-primary" onclick="location.href='home.php'">Kembali ke Home</button>
</div>
</body>
</html>