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
   SESSION DATA
===================== */
$userEmail = $_SESSION['email'] ?? '';
$user_id   = $_SESSION['user_id'] ?? 0;

/* =====================
   CEK ID JOB
===================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Job ID tidak valid.");
}
$job_id = (int) $_GET['id'];

/* =====================
   KONEKSI DATABASE
===================== */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

/* =====================
   QUERY JOB
===================== */
$stmt = $conn->prepare("
    SELECT job_title, company, location, description, requirements 
    FROM jobs 
    WHERE id = ? AND status = 'Open'
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Lowongan tidak ditemukan atau sudah ditutup.");
}

$job = $result->fetch_assoc();

/* =====================
   CEK SUDAH MELAMAR?
===================== */
$alreadyApplied = false;

if ($user_id > 0) {
    $cek = $conn->prepare("
        SELECT id FROM applications 
        WHERE job_id = ? AND user_id = ?
    ");
    $cek->bind_param("ii", $job_id, $user_id);
    $cek->execute();
    $alreadyApplied = $cek->get_result()->num_rows > 0;
}

/* =====================
   SET VARIABEL
===================== */
$job_title    = $job['job_title'];
$company      = $job['company'];
$location     = $job['location'];
$description  = $job['description'];
$requirements = $job['requirements'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['job_title']); ?></title>
    <link rel="stylesheet" href="/LowonganKerja/assets/css/job_detail.css">
</head>
<body>
<?php if (isset($_GET['success'])): ?>
    <div class="alert-success">
        ✅ Lamaran berhasil dikirim. Silakan tunggu proses seleksi.
    </div>
<?php endif; ?>
<div class="job-wrapper">

    <div class="job-card">

        <h1 class="job-title"><?= htmlspecialchars($job_title); ?></h1>

        <div class="job-meta">
            <span class="company">🏢 <?= htmlspecialchars($company); ?></span>
            <span class="location">📍 <?= htmlspecialchars($location); ?></span>
        </div>

        <div class="job-section">
            <h3>Deskripsi Pekerjaan</h3>
            <p><?= nl2br(htmlspecialchars($description)); ?></p>
        </div>

        <div class="job-section">
            <h3>Persyaratan</h3>
            <ul>
                <?php
                foreach (explode("\n", $requirements) as $req) {
                    if (trim($req) !== '') {
                        echo "<li>" . htmlspecialchars($req) . "</li>";
                    }
                }
                ?>
            </ul>
        </div>

    </div>

    <div class="apply-card">
        <h2>Lamar Pekerjaan</h2>

        <form action="submit_application.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?= $job_id ?>">

            <label>Nama Lengkap</label>
            <input type="text" name="full_name" required>

            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($userEmail) ?>" readonly>

            <label>No. Telepon</label>
            <input type="text" name="phone" required>

            <label>Upload CV</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx" required>

            <?php if ($alreadyApplied): ?>
                <button disabled style="background:#aaa;cursor:not-allowed;">
                    Sudah Melamar
                </button>
            <?php else: ?>
                <button type="submit">Kirim Lamaran</button>
            <?php endif; ?>
        </form>
    </div>

    <a href="index.php">← Kembali</a>

</div>

</body>
</html>
