<?php
session_start();
$conn = new mysqli("localhost","root","","job_portal_db");
$id = $_SESSION['id'];

if ($_POST['action'] === 'edit') {
    $stmt = $conn->prepare("UPDATE users SET name=?,email=? WHERE id=?");
    $stmt->bind_param("ssi",$_POST['name'],$_POST['email'],$id);
    $stmt->execute();
}

if ($_POST['action'] === 'password') {
    $old = $_POST['old'];
    $new = password_hash($_POST['new'], PASSWORD_DEFAULT);

    $q = $conn->query("SELECT password FROM users WHERE id=$id")->fetch_assoc();
    if (password_verify($old,$q['password'])) {
        $conn->query("UPDATE users SET password='$new' WHERE id=$id");
    }
}

if ($_POST['action'] === 'foto') {
    $file = $_FILES['foto'];
    $name = time()."_".$file['name'];
    move_uploaded_file($file['tmp_name'], "assets/img/avatar/".$name);
    $conn->query("UPDATE users SET foto='$name' WHERE id=$id");
}

header("Location: admin-profile.php");
