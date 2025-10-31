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

// Proses update status job
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = mysqli_real_escape_string($conn, $_POST['job_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Query untuk update status job
    $sql = "UPDATE jobs SET description = '$status' WHERE id = '$job_id'";
    if ($conn->query($sql) === TRUE) {
        // Update status di tabel applications
        $update_applications_sql = "UPDATE applications SET status = '$status' WHERE job_id = '$job_id'";
        $conn->query($update_applications_sql);
        
        $success = "Status job berhasil diupdate!";
    } else {
        $error = "Gagal update status job!";
        echo "Error: " . $conn->error;
    }
}

// Query untuk menampilkan daftar job
$sql = "SELECT id, job_title, company, location, description FROM jobs";
$result = $conn->query($sql);

// Fetch applicants related to the job
$job_id = isset($_POST['job_id']) ? mysqli_real_escape_string($conn, $_POST['job_id']) : 0;
$applicants_sql = "SELECT job_id, full_name, status FROM applications WHERE job_id = '$job_id'";
$applicants_result = $conn->query($applicants_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Job - Job Portal</title>
    <link rel="stylesheet" href="update-status.css">
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
            <h2>Update Status Job</h2>
            <p>Update status job di sini.</p>
            <form action="update-status.php" method="POST">
                <label for="job_id">Job ID:</label>
                <input type="text" id="job_id" name="job_id" required>
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Accepted">Accepted</option>
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

    <section id="job-list">
        <div class="container">
            <h2>Daftar Job</h2>
            <table>
                <tr>
                    <th>Job ID</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Description</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['job_title']; ?></td>
                            <td><?php echo $row['company']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </table>
        </div>
    </section>

    <section id="applicant-list">
        <div class="container">
            <h2>Daftar Pelamar</h2>
            <table>
                <tr>
                    <th>Applicant ID</th>
                    <th>Applicant Name</th>
                    <th>Status</th>
                </tr>
                <?php if ($applicants_result->num_rows > 0): ?>
                    <?php while ($applicant = $applicants_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $applicant['job_id']; ?></td>
                            <td><?php echo $applicant['full_name']; ?></td>
                            <td><?php echo $applicant['status']; ?></td>
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
