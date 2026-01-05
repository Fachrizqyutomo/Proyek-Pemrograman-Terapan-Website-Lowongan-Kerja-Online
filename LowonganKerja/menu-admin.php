<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'hr') {
    header("Location: login-admin.php");
    exit;
}

/* =====================
   KONEKSI DATABASE
===================== */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

/* =====================
   DATA ADMIN (NAVBAR)
===================== */
$admin_id = $_SESSION['id'];

$adminQuery = $conn->prepare("SELECT name, email, foto FROM users WHERE id = ?");
$adminQuery->bind_param("i", $admin_id);
$adminQuery->execute();
$adminData = $adminQuery->get_result()->fetch_assoc();

$adminName  = $adminData['name'] ?? 'Admin';
$adminEmail = $adminData['email'];
$adminFoto = $adminData['foto']
    ? "assets/img/avatar/" . $adminData['foto']
    : "assets/img/avatar/default.png";

/* =====================
   DATA DASHBOARD
===================== */
if ($_SESSION['role'] === 'hr') {
    $company = $_SESSION['company'];

    $stmt = $conn->prepare("
        SELECT jobs.*,
        (SELECT COUNT(*) FROM applications WHERE applications.job_id = jobs.id) AS total_applicants
        FROM jobs
        WHERE company = ?
        ORDER BY jobs.id DESC
    ");
    $stmt->bind_param("s", $company);
    $stmt->execute();
    $jobs = $stmt->get_result();
} else {
    // ADMIN
    $jobs = $conn->query("
        SELECT jobs.*,
        (SELECT COUNT(*) FROM applications WHERE applications.job_id = jobs.id) AS total_applicants
        FROM jobs
        ORDER BY jobs.id DESC
    ");
}


$stmt = $conn->prepare("SELECT COUNT(*) total FROM jobs WHERE status='Open' AND company=?");
$stmt->bind_param("s", $company);
$stmt->execute();
$statJobs = $stmt->get_result()->fetch_assoc();
$company = $_SESSION['company'];

$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    WHERE jobs.company = ?
");
$stmt->bind_param("s", $company);
$stmt->execute();
$statApplicants = $stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | Job Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="navbar">
    <div class="container nav-flex">
        <h1 class="logo">Admin Panel</h1>

        <nav class="nav-right">
            <a href="add-job.php">Tambah Job</a>


            <button id="themeToggle" class="theme-toggle">🌙</button>

            <!-- ADMIN PROFILE -->
            <a href="admin-profile.php" class="admin-mini">
                <img src="<?= $adminFoto ?>" class="admin-foto">
                <span><?= htmlspecialchars($adminName); ?></span>
            </a>

            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>


<?php if (isset($_SESSION['success'])): ?>
    <div class="success-message container">
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-message container">
        <?= $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<section class="stats admin-stats">
  <div class="container stats-grid">

    <div class="stat-card">
      <span class="stat-icon">📄</span>
      <h3><?= $statJobs['total']; ?></h3>
      <p>Total Lowongan</p>
    </div>

    <div class="stat-card">
      <span class="stat-icon">👥</span>
      <h3><?= $statApplicants['total']; ?></h3>
      <p>Total Pelamar</p>
    </div>



  </div>
</section>


<section class="jobs">
    <div class="container">
        <h2 class="section-title">Daftar Lowongan</h2>

        <div class="job-wrapper">
            <div class="job-list">

                <?php if ($jobs->num_rows > 0): ?>
                    <?php while ($job = $jobs->fetch_assoc()): ?>
<div class="job-card admin-card">

    <div class="job-header">
        <img 
            src="assets/img/LogoPerusahaan/<?= $job['logo'] ?: 'default.png'; ?>" 
            class="job-logo"
        >

        <div class="job-info">
            <h3 class="job-title">
                <?= htmlspecialchars($job['job_title']); ?>

                <span class="status-badge <?= strtolower($job['status']); ?>">
                    <?= $job['status']; ?>
                </span>
            </h3>

            <p class="company">
                <?= htmlspecialchars($job['company']); ?>
                • <?= htmlspecialchars($job['location']); ?>
            </p>
        </div>
    </div>

    <div class="job-footer">

        <div class="pelamar-info">
            👥 <strong><?= $job['total_applicants']; ?></strong> Pelamar
        </div>

        <div class="job-action">
            <a href="edit-job.php?id=<?= $job['id']; ?>" class="btn-edit">
                ✏️  Edit
            </a>

            <a href="applicants.php?job_id=<?= $job['id']; ?>" class="btn-pelamar">
                📄 Pelamar
            </a>

            <a 
                href="delete-job.php?id=<?= $job['id']; ?>" 
                class="btn-hapus"
                onclick="return confirm('Yakin ingin menghapus lowongan ini?')"
            >
                🗑️ Hapus
            </a>
        </div>
</div>



                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center;">Belum ada lowongan.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 Job Portal Admin</p>
</footer>

</body>

<script>
const body = document.body;
const toggleTheme = document.getElementById('themeToggle');

// LOAD SAVED THEME (BERLAKU SEMUA PAGE)
if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark');
    if (toggleTheme) toggleTheme.innerText = '☀️';
}

// TOGGLE (KALO ADA BUTTON)
if (toggleTheme) {
    toggleTheme.onclick = () => {
        body.classList.toggle('dark');

        if (body.classList.contains('dark')) {
            localStorage.setItem('theme', 'dark');
            toggleTheme.innerText = '☀️';
        } else {
            localStorage.setItem('theme', 'light');
            toggleTheme.innerText = '🌙';
        }
    };
}
</script>

</html>
