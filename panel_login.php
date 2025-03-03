<?php
session_start();

// Database connection
$host = "localhost:3306";
$username = "dbgavee";
$password = "1234";
$db = "registrodb";

$con = mysqli_connect($host, $username, $password, $db);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Fetch user data from database
    $sql = "SELECT * FROM panel_member WHERE panel_username = '$username'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['panel_pw'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, create a session
            $_SESSION['panel_username'] = $username;
            $_SESSION['panel_id'] = $row['panel_id']; // Store panel_id in session
            header("Location: panel_member.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['error_message'] = "Incorrect password";
            header("Location: panel_login.php"); 
            exit();
        }
    } else {
        // Username not found
        $_SESSION['error_message'] = "User not found";
        header("Location: panel_login.php"); 
        exit();
    }
}


mysqli_close($con);
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Panel Member Sign In Form </title>
    <link rel="stylesheet" href="Style_panel.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="b1">
    <div class="Log_container">
        <div class="login_Form-container">
            <h2> Sign in - Panel Member </h2>

            <form action="panel_login.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-btn"> Login</button>
            </form>

            <div class="link">
                <span>Don't have an Account?</span><br>
                <a href="panel_register.php">Sign Up</a>
            </div>

        </div>
        <div class="illustration">
            <img src="image/log_1.jpg" alt="Illustration">
        </div>
    </div>

  
    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "<?php echo addslashes(htmlspecialchars($_SESSION['error_message'])); ?>"
            }).then(() => {
                // Clear the error message after showing it
                <?php unset($_SESSION['error_message']); ?>
            });
        </script>
    <?php endif; ?>

</body>
</html>








