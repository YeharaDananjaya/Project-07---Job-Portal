<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Fetch jobs with their associated company names
$query = "SELECT j.job_title, j.description, j.location, j.salary, c.company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.company_id";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Jobs</title>
    <link rel="stylesheet" href="styles/job_view.css">
</head>
<body>
    <div class="job-container">
        <h2>Available Jobs</h2>

        <div class="job-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($row['job_title']); ?></h3>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                        <p><strong>Salary:</strong> $<?php echo htmlspecialchars($row['salary']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No jobs available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
