<?php
// Include database connection
include('db.php');

// Include the navigation bar
include('navbar.php');

// Initialize variables
$message = '';

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $feedback_id = intval($_GET['id']);
    $deleteSql = "DELETE FROM feedbacks WHERE feedback_id = $feedback_id";
    
    if (mysqli_query($con, $deleteSql)) {
        $message = "Feedback deleted successfully.";
    } else {
        $message = "Error deleting feedback: " . mysqli_error($con);
    }
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $rating = intval($_POST['rating']);
    $comments = mysqli_real_escape_string($con, $_POST['comments']);
    
    $updateSql = "UPDATE feedbacks SET rating = $rating, comments = '$comments' WHERE feedback_id = $feedback_id";

    if (mysqli_query($con, $updateSql)) {
        $message = "Feedback updated successfully.";
    } else {
        $message = "Error updating feedback: " . mysqli_error($con);
    }
}

// SQL query to retrieve feedback data along with the related user, company, and job names
$sql = "SELECT f.feedback_id, u.name AS username, c.company_name, j.job_title, f.rating, f.comments, f.created_at
        FROM feedbacks f
        JOIN users u ON f.user_id = u.id
        JOIN companies c ON f.company_id = c.company_id
        JOIN jobs j ON f.job_id = j.job_id";  // Join with jobs table

$result = mysqli_query($con, $sql);

// Check for query execution errors
if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedbacks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .card h2 {
            margin-top: 0;
        }
        .rating {
            font-size: 1.2em;
            color: gold;
        }
        .actions {
            position: absolute;
            right: 20px;
            top: 20px;
        }
        .actions button, .actions a {
            margin-left: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .actions button:hover, .actions a:hover {
            background-color: #0056b3;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #ffffff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Feedbacks</h1>

        <?php if ($message): ?>
            <div style="margin-bottom: 20px; color: green;"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php
        // Check if any feedbacks exist
        if (mysqli_num_rows($result) > 0) {
            // Loop through each feedback record and display it in the card
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='card'>";
                echo "<h2>" . htmlspecialchars($row['username']) . " - " . htmlspecialchars($row['company_name']) . " - " . htmlspecialchars($row['job_title']) . "</h2>";
                echo "<div class='rating'>Rating: " . str_repeat('<i class="fas fa-star"></i>', $row['rating']) . str_repeat('<i class="far fa-star"></i>', 5 - $row['rating']) . "</div>";
                echo "<p>" . htmlspecialchars($row['comments']) . "</p>";
                echo "<p><small>Posted on: " . $row['created_at'] . "</small></p>";
                // Edit and Delete actions
                echo "<div class='actions'>
                        <button class='editBtn' data-id='" . $row['feedback_id'] . "' data-username='" . htmlspecialchars($row['username']) . "' data-company='" . htmlspecialchars($row['company_name']) . "' data-job='" . htmlspecialchars($row['job_title']) . "' data-rating='" . $row['rating'] . "' data-comment='" . htmlspecialchars($row['comments']) . "'>Edit</button>
                        <a href='?action=delete&id=" . $row['feedback_id'] . "' onclick=\"return confirm('Are you sure you want to delete this feedback?');\">Delete</a>
                      </div>";
                echo "</div>";
            }
        } else {
            echo "<p>No feedbacks found.</p>";
        }
        ?>
    </div>

    <!-- Modal Structure -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Edit Feedback</h2>
            <form id="editForm" method="POST">
                <input type="hidden" id="feedback_id" name="feedback_id">
                <div style="margin-bottom: 15px;">
                    <label for="username" style="font-weight: bold;">User Name:</label>
                    <input type="text" id="username" name="username" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;" required readonly>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="company" style="font-weight: bold;">Company Name:</label>
                    <input type="text" id="company" name="company" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;" required readonly>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="job" style="font-weight: bold;">Job Title:</label>
                    <input type="text" id="job" name="job" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;" required readonly>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="rating" style="font-weight: bold;">Rating:</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="comments" style="font-weight: bold;">Comments:</label>
                    <textarea id="comments" name="comments" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;" required></textarea>
                </div>
                <button type="submit" style="padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Update Feedback</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal elements
        var modal = document.getElementById("myModal");
        var editBtns = document.querySelectorAll(".editBtn");
        var closeModal = document.getElementById("closeModal");

        // Open modal and fill in the values
        editBtns.forEach(function(btn) {
            btn.onclick = function() {
                modal.style.display = "block";
                document.getElementById("feedback_id").value = this.dataset.id;
                document.getElementById("username").value = this.dataset.username;
                document.getElementById("company").value = this.dataset.company;
                document.getElementById("job").value = this.dataset.job;
                document.getElementById("rating").value = this.dataset.rating;
                document.getElementById("comments").value = this.dataset.comment;
            }
        });

        // Close modal
        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
