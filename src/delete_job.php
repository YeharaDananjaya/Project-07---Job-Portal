<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Check if job ID is provided
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    // Prepare and execute delete query
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $delete_query = "DELETE FROM jobs WHERE job_id = ?";
        $stmt = $con->prepare($delete_query);
        $stmt->bind_param("i", $job_id);

        if ($stmt->execute()) {
            header("Location: view_jobs_admin.php"); // Redirect back to the job list
            exit;
        } else {
            echo "Error deleting job: " . $con->error;
        }
    }
} else {
    echo "No job ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="styles/job_view.css">
</head>
<body>
    <div class="job-container">
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete this job?</p>
        <form action="" method="POST">
            <button type="submit" class="button delete-button">Yes, Delete Job</button>
            <a href="view_jobs.php" class="button cancel-button">Cancel</a>
        </form>
    </div>
</body>
</html>
