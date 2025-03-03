<?php
session_start();

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
    $sql = "SELECT * FROM grama_niladhari WHERE gn_username = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['gn_pw'];
        $gn_area_id = $row['gn_area_id'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, create session variables
            $_SESSION['gn_username'] = $username;
            $_SESSION['gn_area_id'] = $gn_area_id;
            header("Location: grama_Niladhari.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['error_message'] = "Incorrect password";
            header("Location: GN_login.php");
            exit();
        }
    } else {
        // Username not found
        $_SESSION['error_message'] = "User not found";
        header("Location: GN_login.php");
        exit();
    }

    $stmt->close();
}

// Close connection
mysqli_close($con);
?>











<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Grama Niladhari Sign In Form </title>
    <meta http-equiv="refresh" content="120"/>
    <link rel="stylesheet" href="Style_GN.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="b1">
    <div class="Log_container">
        <div class="login_Form-container">
            <h2> Grama Niladhari Sign in </h2>
            <form action="GN_login.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-btn"> Login</button>
            </form>

            <div class="link">
              <span>Don't have an Account? </span><br>
              <a href="grama_register.php"> Sign Up </a>
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