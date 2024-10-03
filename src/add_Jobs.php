<?php
// Start the session
session_start();

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Initialize variables for success and error messages
$success = '';
$error = '';

// Handle job addition logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $job_title = filter_input(INPUT_POST, 'job_title', FILTER_SANITIZE_STRING);
    $job_description = filter_input(INPUT_POST, 'job_description', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_VALIDATE_FLOAT);
    $requirements = filter_input(INPUT_POST, 'requirements', FILTER_SANITIZE_STRING);
    $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);

    // Insert new job into the database
    $query = "INSERT INTO jobs (job_title, job_description, location, salary, requirements, company) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssiss", $job_title, $job_description, $location, $salary, $requirements, $company);

    if ($stmt->execute()) {
        // Job added successfully
        $success = "Job added successfully.";
    } else {
        $error = "Failed to add job. Please try again.";
    }
}
?>

<?php
// Include sidebar
include("navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job - Company</title>
    <link rel="stylesheet" href="styles/addJobs.css"> <!-- External CSS for Add Job Form -->
    <script>
        function validateForm() {
            const jobTitle = document.getElementById('job_title').value;
            const jobDescription = document.getElementById('job_description').value;
            const location = document.getElementById('location').value;
            const salary = document.getElementById('salary').value;
            const requirements = document.getElementById('requirements').value;

            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach((elem) => {
                elem.innerText = '';
            });

            // Validate Job Title
            if (jobTitle.trim() === '') {
                document.getElementById('job_title-error').innerText = 'Job title is required.';
                isValid = false;
            }

            // Validate Job Description
            if (jobDescription.trim() === '') {
                document.getElementById('job_description-error').innerText = 'Job description is required.';
                isValid = false;
            }

            // Validate Location
            if (location.trim() === '') {
                document.getElementById('location-error').innerText = 'Location is required.';
                isValid = false;
            }

            // Validate Salary
            if (isNaN(salary) || salary <= 0) {
                document.getElementById('salary-error').innerText = 'Enter a valid salary.';
                isValid = false;
            }

            // Validate Requirements
            if (requirements.trim() === '') {
                document.getElementById('requirements-error').innerText = 'Requirements are required.';
                isValid = false;
            }

            return isValid; // Return the overall validity
        }
    </script>
</head>
<body>
    <div class="add-job-container">
        <h2>Add New Job</h2>

        <!-- Display success or error message -->
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="add-job-form" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" placeholder="Enter job title" required>
                <span class="error-message" id="job_title-error"></span>
            </div>
            <div class="form-group">
                <label for="job_description">Job Description:</label>
                <textarea id="job_description" name="job_description" placeholder="Enter job description" required></textarea>
                <span class="error-message" id="job_description-error"></span>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" placeholder="Enter location" required>
                <span class="error-message" id="location-error"></span>
            </div>
            <div class="form-group">
                <label for="salary">Salary:</label>
                <input type="number" id="salary" name="salary" placeholder="Enter salary" required>
                <span class="error-message" id="salary-error"></span>
            </div>
            <div class="form-group">
                <label for="requirements">Requirements:</label>
                <textarea id="requirements" name="requirements" placeholder="Enter job requirements" required></textarea>
                <span class="error-message" id="requirements-error"></span>
            </div>
            <div class="form-group">
                <label for="company">Company:</label>
                <input type="text" id="company" name="company" placeholder="Enter company name">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit">Add Job</button>
            </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
