<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "job_portal_db");
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT a.status, j.job_title 
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     WHERE a.user_id = ? AND a.notification_seen = 0"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conn->query(
    "UPDATE applications 
     SET notification_seen = 1 
     WHERE user_id = $user_id"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<h2 style="text-align:center;">Notifikasi</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="job-card">
            Lamaran kamu untuk  
            <b><?= htmlspecialchars($row['job_title']); ?></b>  
            <br>Status:  
            <b><?= $row['status']; ?></b>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">Tidak ada notifikasi baru.</p>
<?php endif; ?>

</body>
</html>
