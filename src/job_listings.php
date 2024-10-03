<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Initialize search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch jobs with their associated company names, filtered by search query
$query = "SELECT j.job_id, j.job_title, j.description, j.location, j.salary, c.company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.company_id 
          WHERE j.job_title LIKE ? OR c.company_name LIKE ? OR j.location LIKE ?";
$stmt = $con->prepare($query);
$search_term = "%" . $search_query . "%";
$stmt->bind_param("sss", $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include("navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs</title>
    <link rel="stylesheet" href="styles/clientJobView.css">
</head>
<body>
    <div class="job-container">
        <h2>Available Jobs</h2>

            <div class ="middle_section">

                    <!-- Search Form -->
                    <form class="search-form" action="" method="GET">
                        <input type="text" name="search" placeholder="Search by job title, company, or location" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="search-button">Search</button>
                    </form>

                    <h4>*If You want to apply for these jobs, You have to navigate to the Application Tab</h4>

            </div> 

        <div class="job-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($row['job_title']); ?></h3>
                        <div class ="line"></div>
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
<?php include("footer.php"); ?>
</body>
</html>
