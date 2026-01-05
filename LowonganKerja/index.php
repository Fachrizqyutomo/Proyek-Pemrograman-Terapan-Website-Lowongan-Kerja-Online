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
   KONEKSI DATABASE
===================== */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

/* =====================
   AMBIL DATA USER (ID + NAMA)
===================== */
$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$user_id = $user['id'];
$name    = $user['name'];

/* =====================
   HITUNG NOTIF BELUM DIBACA
===================== */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM notifications 
    WHERE user_id = ? AND is_read = 0
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifCount = $stmt->get_result()->fetch_assoc();

/* =====================
   AMBIL LIST NOTIFIKASI
===================== */
$stmt = $conn->prepare("
    SELECT id, message, created_at, is_read
    FROM notifications 
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$qNotif = $stmt->get_result();

$recommend = $conn->query("
    SELECT id, job_title, company, location 
    FROM jobs 
    WHERE status = 'Open'
    ORDER BY RAND()
    LIMIT 3
");

$statJobs = $conn->query("SELECT COUNT(*) total FROM jobs WHERE status='Open'")->fetch_assoc();
$statCompany = $conn->query("SELECT COUNT(DISTINCT company) total FROM jobs")->fetch_assoc();
$statApplicants = $conn->query("SELECT COUNT(*) total FROM applications")->fetch_assoc();

?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Home | Job Portal</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/LOWONGANKERJA/assets/css/index.css">
    </head>
    <body class="user-jobs">

<header class="navbar">
    <div class="container nav-flex">

        <h1 class="logo">Job Portal</h1>

        <nav class="nav-right">

            <a href="#home">Home</a>
            <a href="#jobs">Jobs</a>

<div class="notif-wrapper">
    <div class="notif-icon" id="notifToggle">
        🔔
        <?php if ($notifCount['total'] > 0): ?>
            <span class="notif-badge"><?= $notifCount['total']; ?></span>
        <?php endif; ?>
    </div>

    <div class="notif-dropdown" id="notifDropdown">
        <h4>Notifikasi</h4>

        <?php if ($qNotif->num_rows > 0): ?>
<?php while ($n = $qNotif->fetch_assoc()): ?>
    <div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?>">
        <p><?= htmlspecialchars($n['message']); ?></p>
        <span><?= date('d M Y H:i', strtotime($n['created_at'])); ?></span>
    </div>
<?php endwhile; ?>

        <?php else: ?>
            <div class="notif-empty">Tidak ada notifikasi</div>
        <?php endif; ?>

        <a href="notifications.php" class="notif-view">Lihat Semua</a>
    </div>
</div>



            <a href="profile.php">Profile</a>
            <button id="themeToggle" class="theme-toggle">
    🌙
</button>

            <a href="logout.php" class="btn-logout">Logout</a>

        </nav>

    </div>
</header>


    <section id="home" class="hero">
        <div class="hero-content">
            <h2>Indonesia Berkarir</h2>
            <p>Temukan pekerjaan impian dan wujudkan masa depan karirmu</p>
        </div>
    </section>


<section class="stats">
    <div class="container stats-grid">

        <div class="stat-card">
            <span class="stat-icon">📄</span>
<h3><?= $statJobs['total']; ?>+</h3>
            <p>Lowongan Aktif</p>
        </div>

        <div class="stat-card">
            <span class="stat-icon">🏢</span>
<h3><?= $statCompany['total']; ?></h3>
            <p>Perusahaan</p>
        </div>

        <div class="stat-card">
            <span class="stat-icon">👥</span>
<h3><?= $statApplicants['total']; ?>+</h3>
            <p>Pelamar</p>
        </div>

    </div>
</section>



<section class="benefit">
    <div class="container benefit-grid">

        <div class="benefit-card">
            ⚡
            <h3>Proses Cepat</h3>
            <p>Lamar pekerjaan tanpa ribet dan transparan.</p>
        </div>

        <div class="benefit-card">
            🏢
            <h3>Perusahaan Terpercaya</h3>
            <p>Lowongan dari perusahaan yang telah diverifikasi.</p>
        </div>

        <div class="benefit-card">
            📄
            <h3>Status Real-Time</h3>
            <p>Pantau status lamaran langsung dari dashboard.</p>
        </div>

    </div>
</section>



<section class="highlight-jobs">
    <div class="container">
        <h2 class="section-title">🔥 Rekomendasi Untuk Kamu</h2>

        <div class="highlight-grid">

            <?php while ($job = $recommend->fetch_assoc()): ?>
                <a 
                    href="job_detail.php?id=<?= $job['id']; ?>" 
                    class="highlight-card"
                >
                    <h3><?= htmlspecialchars($job['job_title']); ?></h3>
                    <p><?= htmlspecialchars($job['company']); ?></p>
                    <span>📍 <?= htmlspecialchars($job['location']); ?></span>
                </a>
            <?php endwhile; ?>

        </div>
    </div>
</section>



<section id="jobs" class="jobs">
    <div class="container">
        <h2 class="section-title">Lowongan Tersedia</h2>

        <div class="job-wrapper">
            <div class="job-list">

                <?php
                if ($conn->connect_error) {
                    die("Koneksi gagal");
                }

                $result = $conn->query("SELECT * FROM jobs WHERE status='Open' ORDER BY id DESC");
                while ($job = $result->fetch_assoc()) {
                ?>
                    <div class="job-card">
                        <div class="job-header">
                            <img src="/LOWONGANKERJA/assets/img/LogoPerusahaan/<?= $job['logo']; ?>" alt="logo">
                            <div class="job-info">
                                <h3><?= $job['job_title']; ?></h3>
                                <p class="company"><?= $job['company']; ?></p>
                            </div>
                        </div>

                        <p class="location">📍 <?= $job['location']; ?></p>

                        <a href="job_detail.php?id=<?= $job['id']; ?>" class="btn-apply">
                            Lamar Sekarang
                        </a>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</section>


    <footer class="footer">
        <p>&copy; 2025 Job Portal</p>
    </footer>
<script>

const toggle = document.getElementById('notifToggle');
const dropdown = document.getElementById('notifDropdown');
const badge = document.querySelector('.notif-badge');

toggle.onclick = () => {
    dropdown.classList.toggle('active');

    if (badge) {
        badge.style.display = 'none';
        fetch('mark-notifications-read.php');
    }
};

document.addEventListener('click', e => {
    if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
    }
});


const toggleTheme = document.getElementById('themeToggle');
const body = document.body;

if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark');
    toggleTheme.innerText = '☀️';
}

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
</script>


    </body>
    </html>
