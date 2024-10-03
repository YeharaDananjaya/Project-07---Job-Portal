<?php
// Start the session
session_start();

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Initialize variables for success and error messages
$success = '';
$error = '';

// Handle company addition logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout']))  {
    // Sanitize and validate inputs
    $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);

    // Check if email already exists
    $checkQuery = "SELECT * FROM companies WHERE email = ?";
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already exists.";
    } else {
        // Insert new company into the database
        $query = "INSERT INTO companies (company_name, email, address, contact_number) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssss", $company_name, $email, $address, $contact_number);

        if ($stmt->execute()) {
            // Company added successfully
            $success = "Company added successfully.";
        } else {
            $error = "Failed to add company. Please try again.";
        }
    }
}
?>

<?php
// Include sidebar
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company - Admin Panel</title>
    <link rel="stylesheet" href="styles/addCompany.css"> <!-- External CSS for Add Company Form -->
    <script>
        function validateForm() {
            const companyName = document.getElementById('company_name').value;
            const email = document.getElementById('email').value;
            const address = document.getElementById('address').value;
            const contactNumber = document.getElementById('contact_number').value;

            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach((elem) => {
                elem.innerText = '';
            });

            // Validate Company Name
            if (companyName.trim() === '') {
                document.getElementById('company_name-error').innerText = 'Company name is required.';
                isValid = false;
            }

            // Validate Email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email-error').innerText = 'Invalid email format.';
                isValid = false;
            }

            // Validate Address
            if (address.trim() === '') {
                document.getElementById('address-error').innerText = 'Address is required.';
                isValid = false;
            }

            // Validate Contact Number
            if (contactNumber.trim() === '') {
                document.getElementById('contact_number-error').innerText = 'Contact number is required.';
                isValid = false;
            }

            return isValid; // Return the overall validity
        }
    </script>
</head>
<body>
    <div class="add-company-container">
        <h2>Add New Company</h2>

        <!-- Display success or error message -->
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="add-company-form" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" placeholder="Enter company name" required>
                <span class="error-message" id="company_name-error"></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required>
                <span class="error-message" id="email-error"></span>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" placeholder="Enter address" required></textarea>
                <span class="error-message" id="address-error"></span>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" placeholder="Enter contact number" required>
                <span class="error-message" id="contact_number-error"></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit">Add Company</button>
            </div>
        </form>
    </div>
</body>
</html>
