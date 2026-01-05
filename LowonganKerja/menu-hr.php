<?php
session_start();

/* =====================
   AUTH HR ONLY
===================== */
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'hr') {
    header("Location: login-hr.php");
    exit;
}

/* =====================
   DB CONNECTION
===================== */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

/* =====================
   HR DATA
===================== */
$hr_id   = $_SESSION['id'];
$company = $_SESSION['company'];

$stmt = $conn->prepare("SELECT name, foto FROM users WHERE id=?");
$stmt->bind_param("i", $hr_id);
$stmt->execute();
$hr = $stmt->get_result()->fetch_assoc();

$hrName = $hr['name'] ?? 'HR';

/* === FOTO PATH DISAMAKAN === */
$hrFoto = (!empty($hr['foto']) && file_exists("assets/img/profile/".$hr['foto']))
    ? $hr['foto']
    : 'default.png';

/* =====================
   STATISTICS
===================== */
$jobsTotal = $conn->query("
    SELECT COUNT(*) total FROM jobs WHERE company='$company'
")->fetch_assoc();

$appTotal = $conn->query("
    SELECT COUNT(*) total FROM applications a
    JOIN jobs j ON a.job_id=j.id
    WHERE j.company='$company'
")->fetch_assoc();

/* =====================
   JOB LIST
===================== */
$jobs = $conn->query("
    SELECT j.*,
    (SELECT COUNT(*) FROM applications WHERE job_id=j.id) total_applicants
    FROM jobs j
    WHERE j.company='$company'
    ORDER BY j.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>HR Panel</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/index.css">

<style>
/* ===== NAV HR ===== */
.admin-mini{
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
}
.admin-mini span{
    color:#222;
    font-weight:600;
}
body.dark .admin-mini span{
    color:#fff;
}
.admin-foto{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #0d6efd;
}

/* ===== JOB CARD OLD STYLE ===== */
.job-card{
    background:#fff;
    border-radius:12px;
    padding:20px;
    margin-bottom:18px;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
}
.job-header{
    display:flex;
    gap:15px;
    align-items:center;
}
.job-logo{
    width:55px;
    height:55px;
    object-fit:contain;
}
.job-info h3{
    margin:0;
}
.job-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:15px;
}
.job-action a{
    margin-left:10px;
    font-size:14px;
    text-decoration:none;
}
.btn-edit{color:#0d6efd;}
.btn-pelamar{color:#198754;}
.btn-hapus{color:#dc3545;}
</style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<header class="navbar">
    <div class="container nav-flex">

        <h1 class="logo">HR Panel</h1>

        <div class="nav-right">
            <a href="add-job.php" class="btn-primary">+ Tambah Lowongan</a>

            <a href="hr-profile.php" class="admin-mini">
                <img src="assets/img/profile/<?= $hrFoto ?>?v=<?= time() ?>" class="admin-foto">
                <span><?= htmlspecialchars($hrName) ?></span>
            </a>

            <a href="logout.php" class="btn-logout">Logout</a>
        </div>

    </div>
</header>


<!-- ===== STATS ===== -->
<section class="stats">
    <div class="container stats-grid">
        <div class="stat-card">
            <h3><?= $jobsTotal['total'] ?></h3>
            <p>Total Lowongan</p>
        </div>
        <div class="stat-card">
            <h3><?= $appTotal['total'] ?></h3>
            <p>Total Pelamar</p>
        </div>
    </div>
</section>

<!-- ===== JOB LIST ===== -->
<section class="jobs"> <div class="container"> <h2 class="section-title">Lowongan Perusahaan <?= htmlspecialchars($company); ?></h2> <div class="job-wrapper"> <div class="job-list"> <?php if ($jobs->num_rows > 0): ?> <?php while ($job = $jobs->fetch_assoc()): ?> <div class="job-card admin-card"> <div class="job-header"> <img src="assets/img/LogoPerusahaan/<?= $job['logo'] ?: 'default.png'; ?>" class="job-logo" > <div class="job-info"> <h3 class="job-title"> <?= htmlspecialchars($job['job_title']); ?> <span class="status-badge <?= strtolower($job['status']); ?>"> <?= $job['status']; ?> </span> </h3> <p class="company"> <?= htmlspecialchars($job['company']); ?> • <?= htmlspecialchars($job['location']); ?> </p> </div> </div> <div class="job-footer"> <div class="pelamar-info"> 👥 <strong><?= $job['total_applicants']; ?></strong> Pelamar </div> <div class="job-action"> <a href="edit-job.php?id=<?= $job['id']; ?>" class="btn-edit">✏️ Edit</a> <a href="applicants.php?job_id=<?= $job['id']; ?>" class="btn-pelamar">📄 Pelamar</a> <a href="delete-job.php?id=<?= $job['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus lowongan ini?')"> 🗑️ Hapus </a> </div> </div> </div> <?php endwhile; ?> <?php else: ?> <p style="text-align:center;">Belum ada lowongan.</p> <?php endif; ?> </div> </div> </div> </section>
</body>
</html>
