<?php
// Database connection
$host = 'localhost:3306'; 
$username = 'dbgavee'; 
$password = '1234'; 
$dbname = 'registrodb';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX request to fetch Panel Member names and usernames based on Panel ID
if (isset($_GET['gn_area_id'])) {
    $gn_area_id = $conn->real_escape_string($_GET['gn_area_id']); // Get panel_id from the GET request
    $result = $conn->query("SELECT gn_name, gn_username FROM grama_niladhari WHERE gn_area_id = '$gn_area_id'");



    $gn_names = [];
    while ($row = $result->fetch_assoc()) {
        $gn_names[] = [

            'gn_name' => $row['gn_name'],
            'username' => $row['gn_username']
        ];
    }

    echo json_encode($gn_names);
    exit();
}


// Handle form submission for updating panel member data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $gn_area_id = $conn->real_escape_string($_POST['gn_area_id']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if passwords match
    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the data in the panel_member table
        $sql = "UPDATE  grama_niladhari
                SET gn_contact = '$contact', gn_email = '$email', gn_pw = '$hashed_password'
                WHERE gn_area_id = '$gn_area_id'";

        if ($conn->query($sql)) {
            header("Location: GN_login.php?message=Details updated successfully");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Passwords do not match!";
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
    <title>Grama Niladhari Registration Form</title>
    <link rel="stylesheet" href="Style_GN.css">
    <meta http-equiv="refresh" content="120"/>
</head>


<body id="b1">
    <div class="container">
        <div class="form_1-container">
            <h2>Grama Niladhari Register</h2>
            <form action="grama_register.php" method="POST">

            <input type="hidden" id="gn_area_id" name="gn_area_id" required> <!-- Hidden input for panel_id -->

            <div class="input-group">
                    <select id="gnArea" name="gnArea" onchange="fetchGramaNiladhari(this.value)" required>
                        <option value="">Select an Area</option>
                        <option value="1">Fort</option>
                        <option value="2">Richmond Hill</option>
                        <option value="3">Kandewatta</option>
                        <option value="4">Chinagarden</option>
                        <option value="5">Minuwangoda</option>
                        <option value="6">Dangedara East</option>
                        <option value="7">Kongaha</option>
                        <option value="8">Magalle</option>
                        <option value="9">Makuluwa</option>
                        <option value="10">Gintota</option>
                    </select>
                </div>

                <div class="input-group">
                    <input type="text" id="gnName" name="gnName" placeholder="Name of the Grama Niladhari" readonly required>
                </div>
                <div class="input-group">
                    <input type="text" name="contact" placeholder="Contact Number" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                                
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" readonly required>
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

    <script>
     function fetchGramaNiladhari(areaId) {
        if (areaId === "") {

        document.getElementById('gnName').value = "";
        document.getElementById('username').value = ""; // Clear username
        document.getElementById('gn_area_id').value = ""; // Clear panel_id
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "grama_register.php?gn_area_id=" + encodeURIComponent(areaId), true);
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                var response = JSON.parse(this.responseText);
                if (response.length > 0) {
                    document.getElementById('gnName').value = response[0].gn_name; // Use the first name found
                    document.getElementById('username').value = response[0].username; // Set the username
                    document.getElementById('gn_area_id').value = areaId; // Set panel_id
                } else {
                    document.getElementById('gnName').value = "No panel member found";
                    document.getElementById('username').value = ""; // Clear username
                    document.getElementById('gn_area_id').value = ""; // Clear panel_id
                }
            } catch (e) {
                console.error("Error parsing panel member response: ", e);
                document.getElementById('gnName').value = "Error loading data";
                document.getElementById('username').value = "";
            }
        }
    };
    xhr.send();
}


    </script>
</body>
</html>