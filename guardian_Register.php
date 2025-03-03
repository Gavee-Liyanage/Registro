<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Applicant Registration Form</title>
    <link rel="stylesheet" href="Style_applicant.css">
</head>


<body id="b4">
    <div class="container_2">
        <div class="form_1-container">
            <h2> Applicant Register </h2>
            <form action="guardian_Register.php" method="POST">
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
                    <input type="text" name="username" placeholder="Username" required>
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
</body>
</html>


<?php

// Database connection parameters
$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$db = 'registrodb';

// Create connection
$con = mysqli_connect($host, $username, $password, $db);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
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

        // Insert the data into guardian_register table
        $sql = "INSERT INTO guardian_register (guardian_name, guardian_contact, guardian_email, guardian_username, guardian_pw) 
                VALUES ('$name', '$contact', '$email', '$username', '$hashed_password')";

        if (mysqli_query($con, $sql)) {
            // On successful registration, redirect to guardian_Login.php with a success message
            header("Location: guardian_login.php?message=Registration successful");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        echo "Passwords do not match!";
    }
}

// Close the connection
mysqli_close($con);
?>