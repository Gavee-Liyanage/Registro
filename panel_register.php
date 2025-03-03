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
if (isset($_GET['panel_id'])) {
    $panel_id = $conn->real_escape_string($_GET['panel_id']); // Get panel_id from the GET request
    $result = $conn->query("SELECT member_name, panel_username FROM panel_member WHERE panel_id = '$panel_id'");

    $member_data = [];
    while ($row = $result->fetch_assoc()) {
        $member_data[] = [
            'member_name' => $row['member_name'],
            'username' => $row['panel_username']
        ];
    }

    echo json_encode($member_data);
    exit();
}


// Handle form submission for updating panel member data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $panel_id = $conn->real_escape_string($_POST['panel_id']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);
   
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if passwords match
    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the data in the panel_member table
        $sql = "UPDATE panel_member 
                SET panel_contact = '$contact', panel_email = '$email', panel_pw = '$hashed_password'
                WHERE panel_id = '$panel_id'";

        if ($conn->query($sql)) {
            header("Location: panel_login.php?message=Details updated successfully");
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
    <title> Panel Member Registration Form</title>
    <link rel="stylesheet" href="Style_panel.css">
    <meta http-equiv="refresh" content="120"/>
</head>

<body id="b1">
    <div class="container">
        <div class="form_1-container">
            <h2>Panel Member Register</h2>
            <form action="panel_register.php" method="POST">

            <input type="hidden" id="panel_id" name="panel_id" required> <!-- Hidden input for panel_id -->

            <div class="input-group">
                    <select id="school" name="school" onchange="fetchPanelMember(this.value)" required>
                        <option value="">Select Your School</option>
                        <option value="1">Southland college</option>
                        <option value="2">Sangamitte College</option>
                        <option value="3">Anula Devi Balika Vidyalaya</option>
                        <option value="4">Convent Girls college</option>
                        <option value="5">Rippon Girls College</option>
                        <option value="6">Janadipathi Balika Vidyalaya</option>
                        <option value="7">Richmond college</option>
                        <option value="8">Mahinda College</option>
                        <option value="9">Vidyaloka College</option>
                        <option value="10">Aloysius College</option>
                        <option value="11">Allsaint college</option>
                        <option value="12">Siridhamma Vidyalaya</option>
                    </select>
                </div>
                <div class="input-group">
                    <input type="text" id="panelName" name="panelName" placeholder="Name of the Panel Member" readonly required>
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
     function fetchPanelMember(panelId) {
    if (panelId === "") {
        document.getElementById('panelName').value = "";
        document.getElementById('username').value = ""; // Clear username
        document.getElementById('panel_id').value = ""; // Clear panel_id
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "panel_register.php?panel_id=" + encodeURIComponent(panelId), true);
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                var response = JSON.parse(this.responseText);
                if (response.length > 0) {
                    document.getElementById('panelName').value = response[0].member_name; // Use the first name found
                    document.getElementById('username').value = response[0].username; // Set the username
                    document.getElementById('panel_id').value = panelId; // Set panel_id
                } else {
                    document.getElementById('panelName').value = "No panel member found";
                    document.getElementById('username').value = ""; // Clear username
                    document.getElementById('panel_id').value = ""; // Clear panel_id
                }
            } catch (e) {
                console.error("Error parsing panel member response: ", e);
                document.getElementById('panelName').value = "Error loading data";
                document.getElementById('username').value = "";
            }
        }
    };
    xhr.send();
}


    </script>
</body>
</html>