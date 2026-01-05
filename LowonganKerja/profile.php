<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

$email = $_SESSION['email'];

/* ================= AMBIL USER ID ================= */
$stmtUser = $conn->prepare("SELECT id, name, foto FROM users WHERE email = ?");
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();

$userId = $user['id'];


$filter = $_GET['status'] ?? 'all';

/* ================= USER ================= */
$stmt = $conn->prepare("SELECT name, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ================= STATISTIK LAMARAN ================= */
$statSql = "
SELECT 
    COUNT(*) AS total,
    SUM(status='Pending') AS pending,
    SUM(status='Accepted') AS accepted,
    SUM(status='Rejected') AS rejected
FROM applications
WHERE user_id = ?
";
$statStmt = $conn->prepare($statSql);
$statStmt->bind_param("i", $userId);
$statStmt->execute();
$stats = $statStmt->get_result()->fetch_assoc();


/* ================= APPLICATIONS ================= */
$sql = "
SELECT 
    a.id,
    a.job_id,
    a.status,
    a.resume,
    a.applied_at,
    j.job_title,
    j.company,
    j.logo,
    j.location
FROM applications a
JOIN jobs j ON a.job_id = j.id
WHERE a.user_id = ?
";


if ($filter !== 'all') {
    $sql .= " AND a.status = ?";
}

$sql .= " ORDER BY a.applied_at DESC";

$stmt2 = $conn->prepare($sql);

if ($filter !== 'all') {
    $stmt2->bind_param("is", $userId, $filter);
} else {
    $stmt2->bind_param("i", $userId);
}

$stmt2->execute();
$apps = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Saya</title>
<link rel="stylesheet" href="assets/css/profile.css">
<link rel="stylesheet" href="assets/css/profile-framer.css">
</head>
<body>

<div class="profile-container">

    <!-- PROFILE KIRI -->
    <div class="profile-card">
        <img src="foto/<?= $user['foto'] ?: 'default.png'; ?>" class="avatar">

        <h2><?= htmlspecialchars($user['name']); ?></h2>
        <p><?= htmlspecialchars($email); ?></p>

        <form action="upload_foto.php" method="post" enctype="multipart/form-data">
            <input type="file" name="foto" required>
            <button type="submit">Upload Foto</button>
        </form>

        <a href="change-password.php" class="back-home">
            🔒 Ganti Password
        </a>

        <a href="index.php" class="back-home">
            ← Kembali ke Home
        </a>
    </div>

    <!-- KONTEN KANAN -->
    <div class="profile-content">

        <!-- STATISTIK -->
        <div class="stats-bar">
<div class="stat total">
    <span class="count" data-count="<?= $stats['total']; ?>">0</span>
    <small>Total</small>
</div>

<div class="stat pending">
    <span class="count" data-count="<?= $stats['pending']; ?>">0</span>
    <small>Pending</small>
</div>

<div class="stat accepted">
    <span class="count" data-count="<?= $stats['accepted']; ?>">0</span>
    <small>Accepted</small>
</div>

<div class="stat rejected">
    <span class="count" data-count="<?= $stats['rejected']; ?>">0</span>
    <small>Rejected</small>
</div>

        </div>

        <!-- APPLICATIONS -->
        <div class="applications-wrapper">

            <div class="apps-header">
                <h3>Lamaran Saya</h3>

                <form method="GET" class="filter-form">
                    <select name="status" onchange="this.form.submit()">
                        <option value="all" <?= $filter=='all'?'selected':'' ?>>Semua</option>
                        <option value="Pending" <?= $filter=='Pending'?'selected':'' ?>>Pending</option>
                        <option value="Accepted" <?= $filter=='Accepted'?'selected':'' ?>>Accepted</option>
                        <option value="Rejected" <?= $filter=='Rejected'?'selected':'' ?>>Rejected</option>
                    </select>
                </form>
            </div>

            <?php if ($apps->num_rows > 0): ?>
                <?php while ($row = $apps->fetch_assoc()): ?>
                    <div class="application-card">

                        <span class="status <?= strtolower($row['status']); ?>">
                            <?= $row['status']; ?>
                        </span>

                        <div class="job-head">
                            <img src="assets/img/LogoPerusahaan/<?= $row['logo']; ?>">
                            <div>
                                <h4><?= $row['job_title']; ?></h4>
                                <span><?= $row['company']; ?> • <?= $row['location']; ?></span>
                            </div>
                        </div>

                        <div class="card-actions">
                            <a href="job_detail.php?id=<?= $row['job_id']; ?>" class="btn-detail">
                                Lihat Detail
                            </a>

                            <a href="<?= $row['resume']; ?>" class="btn-cv" download>
                                Download CV
                            </a>

                            <?php if ($row['status'] === 'Pending'): ?>
                                <a href="withdraw_application.php?id=<?= $row['id']; ?>"
                                   class="btn-withdraw"
                                   onclick="return confirm('Yakin ingin menarik lamaran ini?')">
                                   🔄 Withdraw
                                </a>
                            <?php endif; ?>
                        </div>

                        <p class="apply-date">
                            Dilamar: <?= date('d M Y', strtotime($row['applied_at'])); ?>
                        </p>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty">Tidak ada lamaran.</p>
            <?php endif; ?>

        </div>

    </div>

</div>
<script>
document.querySelectorAll('.count').forEach(el=>{
    const target = +el.dataset.count;
    let current = 0;
    const step = Math.ceil(target / 30);

    const interval = setInterval(()=>{
        current += step;
        if(current >= target){
            el.textContent = target;
            clearInterval(interval);
        }else{
            el.textContent = current;
        }
    }, 20);
});
</script>

</body>

</html>
