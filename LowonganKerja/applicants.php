<?php
session_start();

if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['admin','hr'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['job_id'])) {
    die("Job ID tidak valid");
}

$dashboard = ($_SESSION['role'] === 'admin')
    ? 'menu-admin.php'
    : 'menu-hr.php';

$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) {
    die("Koneksi gagal");
}

$job_id = (int) $_GET['job_id'];

if ($_SESSION['role'] === 'admin') {

    $stmt = $conn->prepare("
        SELECT a.*, u.name, u.email
        FROM applications a
        JOIN users u ON a.user_id = u.id
        WHERE a.job_id = ?
        ORDER BY a.applied_at DESC
    ");
    $stmt->bind_param("i", $job_id);

} else {

    $stmt = $conn->prepare("
        SELECT a.*, u.name, u.email
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN jobs j ON a.job_id = j.id
        WHERE a.job_id = ? AND j.company = ?
        ORDER BY a.applied_at DESC
    ");
    $stmt->bind_param("is", $job_id, $_SESSION['company']);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pelamar</title>
    <link rel="stylesheet" href="assets/css/admin-pelamar.css">
</head>
<body>

<div class="container">

<div class="top-bar">
    <a href="<?= $dashboard ?>" class="btn-back">← Kembali ke Dashboard</a>
</div>

<h2 style="text-align:center;">Daftar Pelamar</h2>

<table class="table">
<tr>
    <th>Nama</th>
    <th>Email</th>
    <th>Telepon</th>
    <th>CV</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['full_name']); ?></td>
    <td><?= htmlspecialchars($row['email']); ?></td>
    <td><?= htmlspecialchars($row['phone']); ?></td>
    <td>
        <a href="<?= htmlspecialchars($row['resume']); ?>" target="_blank">Lihat</a>
    </td>
    <td>
        <span class="status <?= strtolower($row['status']); ?>">
            <?= $row['status']; ?>
        </span>
    </td>
    <td class="action">

<?php if ($row['status'] === 'Pending'): ?>

<a href="update-status.php?id=<?= $row['id']; ?>&status=Accepted"
   class="btn btn-accept"
   onclick="return confirm('Terima pelamar ini?')">
   Accept
</a>

<a href="update-status.php?id=<?= $row['id']; ?>&status=Rejected"
   class="btn btn-reject"
   onclick="return confirm('Tolak pelamar ini?')">
   Reject
</a>

<?php else: ?>
<span class="btn btn-disabled"><?= $row['status']; ?></span>
<?php endif; ?>

<a href="delete-applicant.php?id=<?= $row['id']; ?>"
   class="btn btn-delete"
   onclick="return confirm('Hapus pelamar ini?')">
   Hapus
</a>

    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="6" style="text-align:center;">Belum ada pelamar</td>
</tr>
<?php endif; ?>

</table>

</div>
</body>
</html>
