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

// Proses update status lamaran pekerjaan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_lamaran = mysqli_real_escape_string($conn, $_POST['id_lamaran']);
    $status_lamaran = mysqli_real_escape_string($conn, $_POST['status_lamaran']);

    // Query untuk update status lamaran pekerjaan
    $sql = "UPDATE lamaran_pekerjaan SET status_lamaran = '$status_lamaran' WHERE id_lamaran = '$id_lamaran'";
    if ($conn->query($sql) === TRUE) {
        $success = "Status lamaran pekerjaan berhasil diupdate!";
    } else {
        $error = "Gagal update status lamaran pekerjaan!";
        echo "Error: " . $conn->error;
    }
}

// Query untuk menampilkan daftar lamaran pekerjaan
$sql = "SELECT * FROM lamaran_pekerjaan";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Lamaran Pekerjaan - Job Portal</title>
    <link rel="stylesheet" href="update-status-lamaran.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Job Portal</h1>
            <nav>
                <ul>
                    <li><a href="menu-admin.php">Home</a></li>
                    <li><a href="add-job.php">Add Job</a></li>
                    <li><a href="update-status-lamaran.php">Update Status Lamaran</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="update-status-lamaran">
        <div class="container">
            <h2>Update Status Lamaran Pekerjaan</h2>
            <p>Update status lamaran pekerjaan di sini.</p>
            <form action="update-status-lamaran.php" method="POST">
                <label for="id_lamaran">ID Lamaran:</label>
                <input type="text" id="id_lamaran" name="id_lamaran" required>
                <label for="status_lamaran">Status Lamaran:</label>
                <select id="status_lamaran" name="status_lamaran" required>
                    <option value="Diterima">Diterima</option>
                    <option value="Ditolak">Ditolak</option>
                    <option value="Proses">Proses</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    </section>

    <section id="daftar-lamaran">
        <div class="container">
            <h2>Daftar Lamaran Pekerjaan</h2>
            <table>
                <tr>
                    <th>ID Lamaran</th>
                    <th>Nama Pelamar</th>
                    <th>Posisi</th>
                    <th>Status Lamaran</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_lamaran']; ?></td>
                            <td><?php echo $row['nama_pelamar']; ?></td <td><?php echo $row['posisi']; ?></td>
                            <td><?php echo $row['status_lamaran']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </table>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Job Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>