<?php

$host = "localhost:3306";
$username = "dbgavee";
$password = "1234";
$db = "registrodb";

$con = mysqli_connect($host, $username, $password, $db);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the latest admin_id
$result = mysqli_query($con, "SELECT MAX(admin_Id) AS last_id FROM admin_register");
$row = mysqli_fetch_assoc($result);
$next_id = $row['last_id'] + 1; // Increment the admin_id for the next user
$auto_username = "AD/R-0" . $next_id;

$message = "";
$message_type = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);

    // Check if passwords match
    if ($password === $confirm_password) {
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO admin_register (admin_name, admin_contact, admin_email, admin_username, admin_pw) 
                VALUES ('$name', '$contact', '$email', '$username', '$hashed_password')";

        if (mysqli_query($con, $sql)) {
            $message = "Registration successful";
            $message_type = "success";
        } else {
            $message = "Error: " . mysqli_error($con);
            $message_type = "error";
        }
    } else {
        $message = "Passwords do not match!";
        $message_type = "error";
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
    <title> Admin Registration Form</title>
    <meta http-equiv="refresh" content="120"/>
    <link rel="stylesheet" href="Style_admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="b1">
    <div class="container">
        <div class="form_1-container">
            <h2>Admin Register</h2>
            <form action="" method="POST">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Name" required>
                </div>
                <div class="input-group">
                    <input type="text" name="contact" placeholder="Contact Number" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Mail" required>
                </div>
                <div class="input-group">
                    <input type="text" name="username" value="<?php echo $auto_username; ?>" readonly required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <button type="submit" class="register-btn">REGISTER</button>
            </form>
        </div>
        <div class="illustration">
            <img src="image/regi_1 .jpg" alt="Illustration">
        </div>
    </div>

    <?php if (!empty($message)) : ?>
    <script>
        Swal.fire({
            icon: '<?php echo $message_type; ?>',
            title: '<?php echo $message_type === "success" ? "Success!" : "Error!"; ?>',
            text: '<?php echo $message; ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            if ('<?php echo $message_type; ?>' === 'success') {
                window.location = 'admin_Login.php';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
