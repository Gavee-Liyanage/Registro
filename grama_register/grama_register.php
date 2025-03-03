<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grama Niladhari Registration Form</title>
    <link rel="stylesheet" href="Style_GN.css">
    <meta http-equiv="refresh" content="60"/>
</head>

<body id="b1">
    <div class="container">
        <div class="form_1-container">
            <h2>Grama Niladhari Register</h2>
            <form action="grama_register.php" method="POST">
             
                <div class="input-group">
                    <select name="gnName" required>
                        <option value="">Select GN Name</option>
                        <?php foreach ($data as $row): ?>
                            <option value="<?php echo htmlspecialchars($row['gnName']); ?>">
                                <?php echo htmlspecialchars($row['gnName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="input-group">
                    <select name="gnArea" required>
                        <option value="">Select GN Area</option>
                        <?php foreach ($data as $row): ?>
                            <option value="<?php echo htmlspecialchars($row['gnArea']); ?>">
                                <?php echo htmlspecialchars($row['gnArea']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="input-group">
                    <input type="text" name="name" placeholder="Full name" required>
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
// Database connection
$host = 'localhost:3306'; // Replace with your database host
$username = 'dbgavee'; // Replace with your database username
$password = '1234'; // Replace with your database password
$dbname = 'registrodb'; // Replace with your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for dropdowns
$gnName = $conn->query("SELECT gn_name FROM grama_niladhari");
$gnArea = $conn->query("SELECT DISTINCT gn_area FROM grama_niladhari");

$data_gnName = [];
$data_gnArea = [];

if ($gnName->num_rows > 0) {
    while ($row = $gnName->fetch_assoc()) {
        $data_gnName[] = $row;
    }
}

if ($gnArea->num_rows > 0) {
    while ($row = $gnArea->fetch_assoc()) {
        $data_gnArea[] = $row;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $gnName = mysqli_real_escape_string($con, $_POST['gnName']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $gnArea = mysqli_real_escape_string($con, $_POST['gnArea']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);

    // Check password match
    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


    // Insert data into the grama_nildhari table without bind parameters
    $sql = "INSERT INTO grama_nildhari ( gn_name, gn_area, gn_username, gn_pw, gn_contact, gn_email) 
            VALUES ('$gnName', '$gnArea', '$username', '$hashedPassword', '$contact', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Grama Niladhari registered successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>



