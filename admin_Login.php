<?php
// Start session
session_start();

// Database connection
$host = "localhost";
$username = "dbgavee";
$password = "1234";
$dbname = "registrodb";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is already logged in
if (isset($_SESSION['admin_Id'])) {
    header("Location: admin.php"); // Redirect
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fetch user data from the database
    $sql = "SELECT admin_Id, admin_pw FROM admin_register WHERE admin_username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['admin_pw'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_Id'] = $row['admin_Id'];
            $_SESSION['admin_username'] = $username;
            header("Location: admin.php");
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "User not found!";
    }
}

$conn->close();
?>












<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In Form</title>
    <meta http-equiv="refresh" content="120"/>
    <link rel="stylesheet" href="Style_admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="b1">
    <div class="Log_container">
        <div class="login_Form-container">
            <h2>Sign in as Admin</h2>
            <form action="admin_Login.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>

            <div class="link">
                <span>Don't have an account?</span><br>
                <a href="admin_Register.php">Sign Up</a>
            </div>
        </div>
        <div class="illustration">
            <img src="image/log_1.jpg" alt="Illustration">
        </div>
    </div>
    
    <?php if (!empty($error_message)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "<?php echo addslashes(htmlspecialchars($error_message)); ?>"
            });
        </script>
    <?php endif; ?>
</body>
</html>
