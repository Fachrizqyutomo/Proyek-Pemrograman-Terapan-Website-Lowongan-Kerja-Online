<?php
session_start();
$conn = new mysqli("localhost","root","","job_portal_db");

$email = $_SESSION['email'];

$q = $conn->prepare("SELECT id FROM users WHERE email=?");
$q->bind_param("s",$email);
$q->execute();
$user = $q->get_result()->fetch_assoc();

$conn->query("
    UPDATE notifications 
    SET is_read = 1 
    WHERE user_id = {$user['id']}
");
