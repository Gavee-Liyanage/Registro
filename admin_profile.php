<?php

// Database connection parameters
$host = "localhost";
$username = "dbgavee";
$password = "1234";
$dbname = "registrodb";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo "<script>Swal.fire({
        title: 'Error',
        text: 'Connection failed: " . $conn->connect_error . "',
        icon: 'error',
        confirmButtonText: 'OK'
    });</script>";
    exit;
}

// Start session
session_start();

// Check if an alert message is set
if (isset($_SESSION['alertMessage'])) {
    $alert = $_SESSION['alertMessage'];
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: '" . $alert['title'] . "',
            text: '" . $alert['message'] . "',
            icon: '" . $alert['type'] . "',
            confirmButtonText: 'OK'
        });
    </script>";
    unset($_SESSION['alertMessage']); // Clear the session message after displaying it
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_Id'])) {
    // Admin not logged in - display message
    $loggedIn = false;
    $adminName = ""; // Set a default empty value
} else {
    $loggedIn = true;

    // Initialize variables
    $adminName = "Guest";
    $firstName = "";
    $lastName = "";
    $username = "";
    $email = "";
    $phone = "";

    // Fetch admin details from the database
    $adminId = $_SESSION['admin_Id']; // Get admin ID from session
    $query = "SELECT admin_name, admin_username, admin_contact, admin_email FROM admin_register WHERE admin_Id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $adminName = $row['admin_name'];
            $username = $row['admin_username'];
            $email = $row['admin_email'];
            $phone = $row['admin_contact'];

            // Split full name into first and last names
            $nameParts = explode(' ', $adminName, 2);
            $firstName = $nameParts[0] ?? "";
            $lastName = $nameParts[1] ?? "";
        }
        $stmt->close();
    }
}

// Initialize alert variables
$alertMessage = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and validate if it exists
    $firstName = isset($_POST['first-name']) ? $_POST['first-name'] : "";
    $lastName = isset($_POST['last-name']) ? $_POST['last-name'] : "";
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";
    $phone = isset($_POST['phone']) ? $_POST['phone'] : "";

    // Validate username and email
    if (empty($_POST['username']) || empty($_POST['email'])) {
        $_SESSION['alertMessage'] = [
            'title' => 'Error',
            'message' => 'Username and email are required.',
            'type' => 'error'
        ];
        header("Location: admin_profile.php");
        exit;
    
    


        // Check if username exists
        $checkQuery = "SELECT * FROM admin_register WHERE admin_username = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing admin details
            $updateQuery = "UPDATE admin_register SET admin_name = ?, admin_contact = ?, admin_email = ? WHERE admin_username = ?";
            $stmt = $conn->prepare($updateQuery);
            $fullName = $firstName . ' ' . $lastName;
            $stmt->bind_param("ssss", $fullName, $phone, $email, $username);
            if ($stmt->execute()) {
                $_SESSION['alertMessage'] = [
                    'title' => 'Success',
                    'message' => 'Details updated successfully.',
                    'type' => 'success'
                ];
            } else {
                $_SESSION['alertMessage'] = [
                    'title' => 'Error',
                    'message' => 'Error updating details.',
                    'type' => 'error'
                ];
            }
            
        } else {
            // Insert new admin details
            $insertQuery = "INSERT INTO admin_register (admin_name, admin_username, admin_contact, admin_email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $fullName = $firstName . ' ' . $lastName;
            $stmt->bind_param("ssss", $fullName, $username, $phone, $email);
            if ($stmt->execute()) {
                $_SESSION['alertMessage'] = [
                    'title' => 'Success',
                    'message' => 'New details added successfully.',
                    'type' => 'success'
                ];
            } else {
                $_SESSION['alertMessage'] = [
                    'title' => 'Error',
                    'message' => 'Error adding admin.',
                    'type' => 'error'
                ];
            }   
        }  
        $stmt->close();
        header("Location: admin_profile.php");
        exit;  
    } 

    $stmt->close();

}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['current-password'])) {
    // Retrieve form data for password change
    $currentPassword = isset($_POST['current-password']) ? $_POST['current-password'] : "";
    $newPassword = isset($_POST['new-password']) ? $_POST['new-password'] : "";
    $confirmPassword = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : "";

    // Fetch the current password hash from the database
    $query = "SELECT admin_pw FROM admin_register WHERE admin_Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPasswordHash = $row['admin_pw'];

        // Verify the current password
        if (!password_verify($currentPassword, $dbPasswordHash)) {
            $_SESSION['alertMessage'] = [
                'title' => 'Error',
                'message' => 'Do not match with the Current password.',
                'type' => 'error'
            ];
        }
        
        if ($newPassword !== $confirmPassword) {

            $_SESSION['alertMessage'] = [
                'title' => 'Error',
                'message' => 'New password and confirm password do not match.',
                'type' => 'error'
            ];
            
        }
        

        // Hash the new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password in the database
        $updateQuery = "UPDATE admin_register SET admin_pw = ? WHERE admin_Id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $newPasswordHash, $adminId);

        if ($updateStmt->execute()) {
            echo "<script>Swal.fire({
                title: 'Success',
                text: 'Password updated successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });</script>";
            header("Location: admin_profile.php"); // Replace 'success_page.php' with your desired page
            exit();
        } else {
            echo "<script>Swal.fire({
                title: 'Error',
                text: 'Error updating password.',
                icon: 'error',
                confirmButtonText: 'OK'
            });</script>";
        }
        

        $updateStmt->close();
    } else {
        echo "<script>Swal.fire({
            title: 'Error',
            text: 'Admin record not found.',
            icon: 'error',
            confirmButtonText: 'OK'
        });</script>";
    
    }

    $stmt->close();
}

$conn->close();
?>











<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>

  <link rel="stylesheet" href="Style_profile.css">

  <style>
    /* CSS for POP up window */
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    /* Popup window */
    .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        padding: 30px;
        width: 375px;
        height: 400px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1001;
    }

    /* Close button */
    .popup .close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 30px;
        cursor: pointer;

    }

    .popup .close:hover{
        color: crimson;
    }

    /* Form elements */
    .popup h2 {
        margin-bottom: 40px;
        font-size: 20px;
        text-align: center;
        margin-top: 20px;
    }

    .popup input {
        width: 100%;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .popup button {
        width: 100%;
        padding: 15px;
        background-color: #007bff;
        margin-top: 10px;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .popup button:hover {
        background-color: #0056b3;
    }
  </style>
</head>

<body>
    <div class="container">
        <?php if (!$loggedIn): ?>
        <div class="not-logged-in">
            <h1>You are not logged in. Please log in to access this page.</h1>
            <a href="admin_Login.php" class="login-link">Go to Login Page</a>
        </div>
        <?php else: ?>


        <aside class="sidebar">
            <div class="profile">
                <h1>REGISTRO</h1>
                <h2 class="name"><?php echo htmlspecialchars($adminName); ?></h2>
            </div>


            <nav class="menu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="admin.php">Dashboard</a></li>
                    <li><a href="admin_profile.php" class="active">Account Settings</a></li>
                    <li><a href="#" id="changePasswordLink">Change Password</a></li>
                </ul>
                </nav>
            <div class="sign-out">
                <a href="admin_signOut.php">Sign Out</a>
            </div>
        </aside>


        <main class="content">
            <h1>Account Settings</h1>
            <form class="account-form" method="POST" action="admin_profileUpdate.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="first-name">First name</label>
                    <input type="text" id="first-name" name="first-name" value="<?php echo htmlspecialchars($firstName); ?>">
                </div>
                <div class="form-group">
                    <label for="last-name">Last name</label>
                    <input type="text" id="last-name" name="last-name" value="<?php echo htmlspecialchars($lastName); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
            </div>
            <div class="form-buttons">
                <button type="submit" class="save-button">Save</button>
                <button type="button" class="cancel-button">Cancel</button>
            </div>
            </form>
        </main>

        <?php endif; ?>


        <!-- Change Password Popup -->
        <div class="overlay" id="popup-overlay">
            <div class="popup">
                <span class="close" id="popup-close">&times;</span>
                <h2>Change Password</h2>
                <form id="changePasswordForm" method="POST" action="admin_profile.php">
                <input type="password" id="currentPassword" name="current-password" placeholder="Current Password" required>
                <input type="password" id="newPassword" name="new-password" placeholder="New Password" required>
                <input type="password" id="confirmPassword" name="confirm-password" placeholder="Confirm Password" required>
                <button type="submit">Update Password</button>
                </form>
            </div>
        </div>
    </div>




<script>

    
    <?php if ($alertMessage): ?>
      Swal.fire({
        title: '<?php echo $alertMessage["title"]; ?>',
        text: '<?php echo $alertMessage["message"]; ?>',
        icon: '<?php echo $alertMessage["type"]; ?>'
      });
    <?php endif; ?>



        // Get the popup elements
        const popupOverlay = document.getElementById('popup-overlay');
        const popupClose = document.getElementById('popup-close');
        const changePasswordLink = document.getElementById('changePasswordLink');

        // Open the popup
        changePasswordLink.onclick = function() {
            popupOverlay.style.display = 'block';
        }

        // Close the popup
        popupClose.onclick = function() {
            popupOverlay.style.display = 'none';
            window.location.href = 'admin_profile.php'; // Redirect to admin_profile.php
        }

        // Close the popup if clicked outside of it
        window.onclick = function(event) {
            if (event.target == popupOverlay) {
                popupOverlay.style.display = 'none';
                window.location.href = 'admin_profile.php';
            }
        }
</script>

</body>
</html>
