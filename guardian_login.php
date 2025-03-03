<?php
session_start();


$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$db = 'registrodb';

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
    $sql = "SELECT * FROM guardian_register WHERE guardian_username = '$username'";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        $_SESSION['error'] = "Database query failed: " . mysqli_error($con);
        header("Location: guardian_login.php");
        exit();
    }

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['guardian_pw'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, create a session
            $_SESSION['guardian_username'] = $username;
            $_SESSION['applicant_id'] = $row['applicant_id']; // Store applicant_id in session

            $_SESSION['success'] = "Login successful!"; 
            header("Location: guardian_form2.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['error'] = "Incorrect password";
            header("Location: guardian_login.php");
            exit();
        }
    } else {
        // Username not found
        $_SESSION['error'] = "User not found";
        header("Location: guardian_login.php");
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
    <title> Applicant Sign In Form </title>
    <link rel="stylesheet" href="Style_panel.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
</head>

<body id="b1">
    <div class="Log_container">
        <div class="login_Form-container">
            <h2> Applicant Sign in </h2>

            <form action="guardian_login.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-btn"> Login</button>
            </form>

            <div class="link">
              <span>Don't  have  an  Account ? </span></br>
              <a href="guardian_Register.php"> Sign Up </a>
            </div>

        </div>
        <div class="illustration">
            <img src="image/log_1.jpg" alt="Illustration">
        </div>

    </div>

    
    <?php if (isset($_SESSION['error'])): ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "<?php echo addslashes(htmlspecialchars($_SESSION['error'])); ?>"
                    });
                </script>
                <?php unset($_SESSION['error']); // Clear the error message ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "<?php echo addslashes(htmlspecialchars($_SESSION['success'])); ?>"
                    });
                </script>
                <?php unset($_SESSION['success']); // Clear the success message ?>
            <?php endif; ?>

</body>
</html>



