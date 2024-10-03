<?php
// Include database connection
include('db.php');

// Include the navigation bar
include('navbar.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = 1; // Replace this with the actual user ID
    $company_id = $_POST['company_id'];
    $job_id = $_POST['job_id'];
    $cover_letter = mysqli_real_escape_string($con, $_POST['cover_letter']);
    $status = 'pending'; // Default status

    // Handle file upload
    $target_dir = "uploads/"; // Directory where files will be uploaded
    $target_file = $target_dir . basename($_FILES["resume_file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($_FILES["resume_file"]["size"] > 500000) {
        echo "<div class='alert alert-danger'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "<div class='alert alert-danger'>Sorry, only PDF, DOC & DOCX files are allowed.</div>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<div class='alert alert-danger'>Sorry, your file was not uploaded.</div>";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file)) {
            // Insert application data into the database
            $query = "INSERT INTO applications (user_id, job_id, company_id, cover_letter, resume_file, status) VALUES ('$user_id', '$job_id', '$company_id', '$cover_letter', '$target_file', '$status')";
            if (mysqli_query($con, $query)) {
                echo "<div class='alert alert-success'>Application submitted successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . mysqli_error($con) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
        }
    }
}

// Fetch company names
$companies = mysqli_query($con, "SELECT company_id, company_name FROM companies");

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#company').change(function() {
                var company_id = $(this).val();
                if (company_id) {
                    $.ajax({
                        type: 'POST',
                        url: 'fetch_jobs.php', // URL to the script that fetches jobs
                        data: {company_id: company_id},
                        success: function(response) {
                            $('#job').html(response);
                        }
                    });
                } else {
                    $('#job').html('<option value="">Select a job</option>');
                }
            });
        });
    </script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s;
        }
        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Job Application Form</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="company">Select Company:</label>
                <select id="company" name="company_id" class="form-control" required>
                    <option value="">Select a company</option>
                    <?php while ($row = mysqli_fetch_assoc($companies)) {
                        echo "<option value='{$row['company_id']}'>{$row['company_name']}</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="job">Select Job:</label>
                <select id="job" name="job_id" class="form-control" required>
                    <option value="">Select a job</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cover_letter">Cover Letter:</label>
                <textarea id="cover_letter" name="cover_letter" class="form-control" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="resume">Upload Resume:</label>
                <input type="file" id="resume" name="resume_file" class="form-control-file" required>
                <small class="form-text text-muted">Allowed formats: PDF, DOC, DOCX (max size: 500KB)</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit Application</button>
        </form>
    </div>
    <?php
    // Include the footer
    include('footer.php');
    ?>
</body>
</html>
