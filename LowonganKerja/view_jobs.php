                <?php
// Include database connection
include 'db.php';

// Fetch all jobs from the database
$sql = "SELECT * FROM jobs ORDER BY posted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
</head>
<body>
    <h1>Available Jobs</h1>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while ($job = $result->fetch_assoc()) {
                echo "<li>";
                echo "<h2><a href='job_detail.php?job_id=" . $job['job_id'] . "'>" . htmlspecialchars($job['job_title']) . "</a></h2>";
                echo "<p>Company: " . htmlspecialchars($job['company_name']) . "</p>";
                echo "<p>Location: " . htmlspecialchars($job['location']) . "</p>";
                echo "<p>Posted on: " . htmlspecialchars($job['posted_at']) . "</p>";
                echo "</li>";
            }
        } else {
            echo "<p>No jobs available at the moment. Please check back later.</p>";
        }
        ?>
    </ul>
</body>
</html>