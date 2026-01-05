<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['admin','hr'])) {
    header("Location: login.php");
    exit;
}

/* ================= DB ================= */
$conn = new mysqli("localhost", "root", "", "job_portal_db");
if ($conn->connect_error) die("Koneksi gagal");

$error = '';

/* ================= ADD JOB ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title        = trim($_POST['job_title']);
    $location     = trim($_POST['location']);
    $description  = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);

    /* === COMPANY === */
    $company = ($_SESSION['role'] === 'hr')
        ? $_SESSION['company']
        : trim($_POST['company']);

    /* ================= LOGO AUTO ================= */
    if ($_SESSION['role'] === 'hr') {

        // contoh: PT ABC → pt_abc.png
        $logoFile = strtolower(str_replace(' ', '_', $company)) . ".png";
        $logoPath = "assets/img/LogoPerusahaan/" . $logoFile;

        // kalau file logo ga ada → default
        $logo = file_exists($logoPath) ? $logoFile : "default.png";

    } else {

        // ADMIN BOLEH UPLOAD MANUAL
        if (!empty($_FILES['logo']['name'])) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logo = uniqid() . "." . $ext;
            move_uploaded_file(
                $_FILES['logo']['tmp_name'],
                "assets/img/LogoPerusahaan/" . $logo
            );
        } else {
            $logo = "default.png";
        }
    }

    /* ================= INSERT ================= */
    $stmt = $conn->prepare("
        INSERT INTO jobs 
        (job_title, company, location, logo, description, requirements, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Open')
    ");
    $stmt->bind_param(
        "ssssss",
        $title, $company, $location, $logo, $description, $requirements
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Lowongan berhasil ditambahkan";

        $redirect = ($_SESSION['role'] === 'admin')
            ? 'menu-admin.php'
            : 'menu-hr.php';

        header("Location: $redirect");
        exit;
    } else {
        $error = "Gagal menyimpan lowongan";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Add Job</title>
<link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

<header class="navbar">
    <div class="container nav-flex">
        <h1 class="logo"><?= $_SESSION['role']==='admin'?'Admin Panel':'HR Panel' ?></h1>
        <nav>
            <a href="<?= $_SESSION['role']==='admin'?'menu-admin.php':'menu-hr.php' ?>">Dashboard</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>

<section class="jobs">
<div class="container">

<h2 class="section-title">Tambah Lowongan</h2>

<?php if ($error): ?>
<div class="error-message"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="job-form">

<input type="text" name="job_title" placeholder="Judul Pekerjaan" required>

<?php if ($_SESSION['role'] === 'hr'): ?>
    <p><b>Perusahaan:</b> <?= $_SESSION['company']; ?></p>
<?php else: ?>
    <input type="text" name="company" placeholder="Nama Perusahaan" required>
    <input type="file" name="logo" accept="image/*">
<?php endif; ?>

<input type="text" name="location" placeholder="Lokasi" required>

<textarea name="description" placeholder="Deskripsi Pekerjaan" rows="5" required></textarea>
<textarea name="requirements" placeholder="Persyaratan" rows="5" required></textarea>

<button type="submit" class="btn-apply">Simpan Lowongan</button>

</form>

</div>
</section>

</body>
</html>
