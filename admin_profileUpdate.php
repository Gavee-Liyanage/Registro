<?php
// Start the session
session_start();

// Database connection parameters
$host = "localhost";
$username = "dbgavee";
$password = "1234";
$dbname = "registrodb";

// Create a database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $adminName = $_POST['first-name']; // Assuming you're mapping 'first-name' to `admin_name`
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Split full name into first and last names
    $nameParts = explode(' ', $adminName, 2);
    $firstName = $nameParts[0] ?? "";
    $lastName = $nameParts[1] ?? "";

    // Get the logged-in admin's ID (assumes the admin ID is stored in session)
    $adminId = $_SESSION['admin_Id'];

    // Validate inputs
    if (empty($adminName) || empty($username) || empty($email) || empty($phone)) {
        $_SESSION['alertMessage'] = [
            'title' => 'Error',
            'message' => 'All fields are required.',
            'type' => 'error'
        ];
        header('Location: admin_profile.php');
        exit();
    }

    // Update the admin's information in the database
    $sql = "UPDATE admin_register SET 
                admin_name = ?, 
                admin_username = ?, 
                admin_email = ?, 
                admin_contact = ? 
            WHERE admin_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $adminName, $username, $email, $phone, $adminId);

    if ($stmt->execute()) {
        // Success message
        $_SESSION['alertMessage'] = [
            'title' => 'Success',
            'message' => 'Account information updated successfully.',
            'type' => 'success'
        ];
    } else {
        // Error message
        $_SESSION['alertMessage'] = [
            'title' => 'Error',
            'message' => 'Failed to update account information. Please try again.',
            'type' => 'error'
        ];
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the profile page
    header('Location: admin_profile.php');
    exit();
} else {
    // If the page is accessed without form submission, redirect to the profile page
    header('Location: admin_profile.php');
    exit();
}
?>
