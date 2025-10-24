<?php
// Include database connection
include 'db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    echo "<p>You must be logged in as an employer to post a job. <a href='login.php'>Login here</a>.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_title = $conn->real_escape_string($_POST['job_title']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $posted_by = $_SESSION['user_id'];

    $sql = "INSERT INTO jobs (job_title, company_name, description, location, salary, posted_by) 
            VALUES ('$job_title', '$company_name', '$description', '$location', '$salary', '$posted_by')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Job posted successfully! <a href='index.php'>View jobs</a>.</p>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job</title>
</head>
<body>
    <h1>Post a Job</h1>
    <form method="POST" action="">
        <label for="job_title">Job Title:</label>
        <input type="text" id="job_title" name="job_title" required><br>

        <label for="company_name">Company Name:</label>
        <input type="text" id="company_name" name="company_name" required><br>

        <label for="description">Job Description:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required><br>

        <label for="salary">Salary:</label>
        <input type="text" id="salary" name="salary"><br>

        <button type="submit">Post Job</button>
    </form>
</body>
</html>