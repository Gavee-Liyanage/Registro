<?php
session_start();  // Start the session
require_once __DIR__ ."/Mail.php";
// Database connection parameters
$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['applicant_id'])) {
    header("Location: guardian_login.php");
    exit();
}

// Initialize an array to hold messages
$messages = [];

// Handle AJAX request to fetch Grama Niladhari names based on AREA
if (isset($_GET['gn_area_name'])) {
    $gn_area_name = $conn->real_escape_string($_GET['gn_area_name']);
    $result = $conn->query("SELECT gn_name FROM grama_niladhari WHERE gn_area_name LIKE '$gn_area_name'");

    $gn_names = [];
    while ($row = $result->fetch_assoc()) {
        $gn_names[] = $row;
    }
    echo json_encode($gn_names);
    exit();
}

// Handle AJAX request to fetch Grama Niladhari names based on ID
if (isset($_GET['gn_area_id'])) {
    $gn_area_id = intval($_GET['gn_area_id']);
    $result = $conn->query("SELECT gn_name FROM grama_niladhari WHERE gn_area_id = $gn_area_id");

    $gn_names = [];
    while ($row = $result->fetch_assoc()) {
        $gn_names[] = $row;
    }

    echo json_encode($gn_names);
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $conn->real_escape_string($_POST['First_Name']);
    $last_name = $conn->real_escape_string($_POST['Last_Name']);
    $initial_name = $conn->real_escape_string($_POST['Surename']);
    $nic = $conn->real_escape_string($_POST['NIC']);
    $gender = $conn->real_escape_string($_POST['Gender']);
    $dob = $conn->real_escape_string($_POST['Date_of_Birth']);
    $religion = $conn->real_escape_string($_POST['Religion']);
    $address = $conn->real_escape_string($_POST['Address']);
    $city = $conn->real_escape_string($_POST['City']);
    $country = $conn->real_escape_string($_POST['Country']);
    $email = $conn->real_escape_string($_POST['Email']);
    $contact_no = $conn->real_escape_string($_POST['Contact']);
    $gn_area_id = $conn->real_escape_string($_POST['gnArea']);
    $gn_id = $conn->real_escape_string($_POST['gnName']);

    // Insert data into the 'applicant' table
    $sql = "INSERT INTO applicant(f_name, l_name, initial_name, nic, gender, dob, religion, address, city, country, email, contact_no) 
            VALUES ('$first_name', '$last_name', '$initial_name', '$nic', '$gender', '$dob', '$religion', '$address', '$city', '$country', '$email', '$contact_no')";

    if ($conn->query($sql) === TRUE) {
        // Get the inserted applicant_id
        $applicant_id = $conn->insert_id;

        // Store applicant_id in session for further use
        $_SESSION['applicant_id'] = $applicant_id;

        // Insert into many-to-many relationship table
        $sql_relation = "INSERT INTO applicant_gn_area (applicant_id, gn_area_id) VALUES ('$applicant_id', '$gn_area_id')";
        $conn->query($sql_relation);

        // File uploads
        $upload_dir = 'uploads/';
        
        // Helper function for file uploads
        function handleFileUpload($file, $upload_dir, $field_name) {
            global $messages;
            $file_path = $upload_dir . basename($file['name']);
            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
            
            if (is_uploaded_file($file['tmp_name'])) {
                if (!in_array($file['type'], $allowed_types)) {
                    $messages[] = "$field_name must be a PDF, JPG, or PNG.";
                } elseif ($file['size'] > 5000000) {
                    $messages[] = "$field_name is too large.";
                } elseif (!move_uploaded_file($file['tmp_name'], $file_path)) {
                    $messages[] = "Failed to upload $field_name.";
                } else {
                    $messages[] = "$field_name uploaded successfully.";
                    return $file_path;
                }
                

            } else {
                $messages[] = "Error uploading $field_name.";
            }
            return null;
        }

        $residential_doc = handleFileUpload($_FILES['residential_doc'], $upload_dir, "Residential Document");
        $water_bill = handleFileUpload($_FILES['water_bill'], $upload_dir, "Water Bill");
        $electricity_bill = handleFileUpload($_FILES['electricity_bill'], $upload_dir, "Electricity Bill");

        // Insert data into 'recidencial_doc' table
        if ($residential_doc && $water_bill && $electricity_bill) {
            $sql_docs = "INSERT INTO recidencial_doc (applicant_id, recideint_doc, water_bill, electrycity_bill) 
                         VALUES ('$applicant_id', '$residential_doc', '$water_bill', '$electricity_bill')";

            if ($conn->query($sql_docs) === TRUE) {
                $messages[] = "Documents uploaded and saved successfully!";
            
                // Fetch GN email and name based on applicant_id
$gn_sql = "SELECT gn_email, gn_name FROM grama_niladhari WHERE gn_area_id IN
(
    SELECT gn_area_id 
    FROM applicant_gn_area 
    WHERE applicant_id = '$applicant_id'
)";
$gn_result = $conn->query($gn_sql);

if ($gn_result->num_rows > 0) {
$gn_data = $gn_result->fetch_assoc();
$gn_email = $gn_data['gn_email'];
$gn_name = $gn_data['gn_name'];

// Pass values to JavaScript for sending email
$mail = new Mails();
$mail->sendDocumentUploadNotification($applicant_id);
} else {
$messages[] = "No GN email found for the applicant.";
}

            } else {
                $messages[] = "Error: " . $conn->error;
            }
        }
    } else {
        $messages[] = "Error: " . $conn->error;
    }

    // Handle spouse information
    if (!empty($_POST['sp_First_Name']) && !empty($_POST['sp_Last_Name'])) {
        $sp_first_name = $conn->real_escape_string($_POST['sp_First_Name']);
        $sp_last_name = $conn->real_escape_string($_POST['sp_Last_Name']);
        $sp_gender = !empty($_POST['sp_Gender']) ? $conn->real_escape_string($_POST['sp_Gender']) : NULL;
        $sp_nic = !empty($_POST['sp_NIC']) ? $conn->real_escape_string($_POST['sp_NIC']) : NULL;
        $sp_dob = !empty($_POST['sp_Date_of_Birth']) ? $conn->real_escape_string($_POST['sp_Date_of_Birth']) : NULL;
        $sp_email = !empty($_POST['sp_Email']) ? $conn->real_escape_string($_POST['sp_Email']) : NULL;
        $sp_mobile = !empty($_POST['sp_Contact']) ? $conn->real_escape_string($_POST['sp_Contact']) : NULL;

        $sql_spouse = "INSERT INTO spouse (sp_f_name, sp_l_name, sp_gender, sp_nic, sp_dob, sp_email, sp_mobile, applicant_id) 
                       VALUES ('$sp_first_name', '$sp_last_name', '$sp_gender', '$sp_nic', '$sp_dob', '$sp_email', '$sp_mobile', '$applicant_id')";

        if ($conn->query($sql_spouse) === TRUE) {
            $messages[] = "Spouse record inserted successfully!";
        } else {
            $messages[] = "Error inserting spouse record: " . $conn->error;
        }
    }
}




// Check if the applicant ID is in session
if (!isset($_SESSION['applicant_id'])) {
    die("Applicant not logged in.");
}


// Close connection
$conn->close();

// Convert messages to JSON
$messages_json = json_encode($messages);
?>










<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian Application Page</title>
    <link rel="stylesheet" href="Style_guardian.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />	
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

</head>


<body id="b2">
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="about.php"> About </a>

            <div class="dropdown">
                <button class="dropbtn"> Admission  <i class="fa-solid fa-angle-down" style="color: #4f555f;"></i> </button>
                <div class="dropdown-content">
                    <a href="guardian_form2.php"> Guardian Form </a>
                    <a href="child.php"> Child Form </a>
                </div>
            </div>

            <a href="contact.php"> Contact </a>
        </div>

        <div class="right-nav">
                <a class="Registro"> Registro </a>
        </div>
    </div>
            


    <div class="guardian-container">
        <h1>Application Form Of Applicant</h1>

        <form action="guardian_form2.php" method="post" enctype="multipart/form-data">
           <div class="flex-container">
                <div class="fname-field">
                <label for="name">Full Name</label>
                    <input type="text" id="first-name" name="First_Name" placeholder="First Name" required>
                </div>
                <div class="lname-field">
                    <label for="last-name">&nbsp;</label> <!-- Empty label to align with first name label -->
                    <input type="text" id="name" name="Last_Name" placeholder="Last Name" required>
                </div>
            </div>

            <label for="name">Name with Initials</label>
            <input type="text" id="name" name="Surename" placeholder=" Enter your full name with initials" required>

            <div class="flex-container">
                <div class="gender-field">
                    <label for="gender">Gender</label>
                    <select id="gender" name="Gender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="dob-field">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="Date_of_Birth" required>
                </div>
            </div>
    
            <label for="religion">Religion </label>
            <input type="text" id="religion" name="Religion" placeholder="Enter Religion" required>

            <br><label for="address"> Permenant Address</label>
            <input type="text" id="address" name="Address" placeholder=" Enter Permenant address" required>

            
            <!-- Hardcoded City and Country -->
            <label for="city">City</label>
            <input type="text" id="city" name="City" value="Galle" readonly>

            <label for="country">Country</label>
            <input type="text" id="country" name="Country" value="Sri Lanka" readonly>
     
            <label for="text">National Identity Card Number </label>
            <input type="text" id="nic" name="NIC" placeholder="Enter NIC here" required>

            <label for="email"> email Address </label>
            <input type="text" id="email" name="Email" placeholder="Enter Email Address" required>

            <label for="phone"> Telephone Number </label>
            <input type="text" id="contact" name="Contact" placeholder="Enter Telephone number here" required>


            <!-- File Upload Section -->
            <h2>Upload Required Documents</h2>

            <label for="residential_doc">Residential Document</label>
            <input type="file" id="residential_doc" name="residential_doc" accept=".pdf,.jpg,.jpeg,.png" required>

            <label for="water_bill">Water Bill</label>
            <input type="file" id="water_bill" name="water_bill" accept=".pdf,.jpg,.jpeg,.png" required>

            <label for="electricity_bill">Electricity Bill</label>
            <input type="file" id="electricity_bill" name="electricity_bill" accept=".pdf,.jpg,.jpeg,.png" required>


            <!-- Grama Niladhari Area selection -->
            <h2>Grama Niladhari Area Information</h2>

            <div class="flex-container">
                <div class="area-field">
                    <label for="gnArea">Grama Niladhari Area</label>
                    <select id="gnArea" name="gnArea" onchange="fetchGramaNiladhari(this.value)" required>
                    
                        <option value="">Select an Area</option>
                        <option value="1"> Fort</option>
                        <option value="2"> Richmond Hill</option>
                        <option value="3"> Kandewatta </option>
                        <option value="4"> Chinagarden </option>
                        <option value="5"> Minuwangoda </option>
                        <option value="6"> Dangedara East </option>
                        <option value="7"> Kongaha </option>
                        <option value="8"> Magalle </option>
                        <option value="9"> Makuluwa </option>
                        <option value="10"> Gintota </option>
                    </select>
                </div>

                <div class="GN-field">
                    <label for="gnName">Grama Niladhari Name</label>
                    <input type="text" id="gnName" name="gnName" placeholder="Name of the Grama Niladhari" readonly required>
                </div>
            </div>
               

            <button type="reset" class="resetBtn"> Reset </button>


            <!-- Spouse info-->
            <h2>Spouse Information(Optional)</h2>

            <div class="flex-container">
                <div class="fname-field">
                <label for="name">Full Name</label>
                    <input type="text" id="sp_first-name" name="sp_First_Name" placeholder="First Name" required>
                </div>
                <div class="lname-field">
                    <label for="last-name">&nbsp;</label> <!-- Empty label to align with first name label -->
                    <input type="text" id="sp_name" name="sp_Last_Name" placeholder="Last Name" required>
                </div>
            </div>

            <div class="flex-container">
                <div class="gender-field">
                    <label for="gender">Gender</label>
                    <select id="gender" name="sp_Gender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="dob-field">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="sp_dob" name="sp_Date_of_Birth" required>
                </div>
            </div>

            <label for="text">National Identity Card Number </label>
            <input type="text" id="sp_nic" name="sp_NIC" placeholder="Enter NIC here" required>

            <label for="email"> email Address </label>
            <input type="text" id="sp_email" name="sp_Email" placeholder="Enter Email Address" required>

            <label for="phone"> Telephone Number </label>
            <input type="text" id="sp_contact" name="sp_Contact" placeholder="Enter Telephone number here" required>


           

            <div class="flex-container">
                <div class="resetBtn-field">
                    <button type="reset" class="resetBtn"> Reset </button>
                </div>
                <div class="submitBtn-field">
                    <button type="submit" class="submitBtn"> Submit </button>
                </div>
            </div>

        </form>



        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Fetch the PHP messages as JSON
                const messages = <?php echo $messages_json; ?>;

                if (messages.length > 0) {
                    // Display messages using SweetAlert2
                    Swal.fire({
                        title: 'Success!',
                        html: messages.join('<br>'),
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            });

            function fetchGramaNiladhari(areaId) {
                if (areaId) {
                    fetch(`guardian_form2.php?gn_area_id=${areaId}`)
                        .then(response => response.json())
                        .then(data => {
                            const gnNameField = document.getElementById("gnName");
                            if (data.length > 0) {
                                gnNameField.value = data[0].gn_name;

                                // Notify user of successful data fetch
                                Swal.fire({
                                    title: 'Success!',
                                    text: `Grama Niladhari Name: ${data[0].gn_name}`,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                gnNameField.value = "No names found";

                                // Notify user that no data was found
                                Swal.fire({
                                    title: 'No Data',
                                    text: 'No Grama Niladhari names found for the selected area.',
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching Grama Niladhari names:', error);

                            // Notify user of error
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to fetch Grama Niladhari names. Please try again later.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                } else {
                    document.getElementById("gnName").value = "";

                    // Notify user to select an area
                    Swal.fire({
                        title: 'Warning',
                        text: 'Please select an area first.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            }

            
            (function(){
                emailjs.init("qf9nc2kBWCuNMQvw4"); // Replace with your EmailJS Public Key
            })();

            function sendEmailToGN(gnEmail, gnName, applicantId) {
                var params = {
                    gn_email: gnEmail,
                    applicant_id: applicantId,
                    gn_name: gnName
                };

                emailjs.send("service_rax0e79", "template_iu8aje6", params)
                    .then(function(response) {
                        console.log("Email Sent Successfully", response.status, response.text);
                        alert("Email sent successfully to " + gnEmail);
                    }, function(error) {
                        console.log("Email Sending Failed", error);
                        alert("Failed to send email.");
                    });
            }
        </script>


<footer>
	<p>&copy; 2025 Govenment College Admission Form. All rights reserved.</p>
</footer>


</body>
</html>
