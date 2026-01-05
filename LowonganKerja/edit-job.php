<?php
session_start();

/* === AUTH === */
if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['admin','hr'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

$error = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$role = $_SESSION['role'];
$company = $_SESSION['company'] ?? '';

/* === AMBIL JOB SESUAI ROLE === */
if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE id=?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE id=? AND company=?");
    $stmt->bind_param("is", $id, $company);
}

$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();

if (!$job) {
    die("Akses ditolak atau job tidak ditemukan");
}

/* === UPDATE JOB === */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title        = trim($_POST['job_title']);
    $location     = trim($_POST['location']);
    $description  = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $status       = $_POST['status'];

    $company = ($role === 'admin') ? trim($_POST['company']) : $_SESSION['company'];

    if (!$title || !$location || !$description || !$requirements) {
        $error = "Semua field wajib diisi!";
    } else {

        $logo = $job['logo'];

        if (!empty($_FILES['logo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $allowed)) {
                $error = "Format logo tidak valid";
            } else {
                $logo = uniqid("logo_") . "." . $ext;
                move_uploaded_file(
                    $_FILES['logo']['tmp_name'],
                    "assets/img/LogoPerusahaan/" . $logo
                );
            }
        }

        if (!$error) {
            $update = $conn->prepare("
                UPDATE jobs SET
                    job_title=?, company=?, location=?, logo=?,
                    description=?, requirements=?, status=?
                WHERE id=?
            ");

            $update->bind_param(
                "sssssssi",
                $title, $company, $location, $logo,
                $description, $requirements, $status, $id
            );

            if ($update->execute()) {
                header("Location: " . ($role === 'admin' ? "menu-admin.php" : "menu-hr.php"));
                exit;
            } else {
                $error = "Gagal memperbarui job";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Job</title>
<link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

<header class="navbar">
    <div class="container nav-flex">
        <h1 class="logo"><?= $role === 'admin' ? 'Admin Panel' : 'HR Panel' ?></h1>
        <nav>
            <a href="<?= $role === 'admin' ? 'menu-admin.php' : 'menu-hr.php' ?>">Dashboard</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>

<section class="jobs">
<div class="container">

<h2 class="section-title">Edit Lowongan</h2>

<?php if ($error): ?>
<div class="error-message"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="job-form">

<label>Judul Pekerjaan</label>
<input type="text" name="job_title" value="<?= htmlspecialchars($job['job_title']) ?>" required>

<?php if ($role === 'admin'): ?>
<label>Perusahaan</label>
<input type="text" name="company" value="<?= htmlspecialchars($job['company']) ?>" required>
<?php else: ?>
<p><b>Perusahaan:</b> <?= htmlspecialchars($job['company']) ?></p>
<?php endif; ?>

<label>Lokasi</label>
<input type="text" name="location" value="<?= htmlspecialchars($job['location']) ?>" required>

<label>Status</label>
<select name="status">
    <option value="Open"   <?= $job['status']=='Open'?'selected':'' ?>>Open</option>
    <option value="Closed" <?= $job['status']=='Closed'?'selected':'' ?>>Closed</option>
</select>

<label>Logo (opsional)</label>
<input type="file" name="logo">

<label>Deskripsi</label>
<textarea name="description" rows="5" required><?= htmlspecialchars($job['description']) ?></textarea>

<label>Persyaratan</label>
<textarea name="requirements" rows="5" required><?= htmlspecialchars($job['requirements']) ?></textarea>

<button type="submit" class="btn-apply">Update Job</button>

</form>

</div>
</section>

</body>
</html>
