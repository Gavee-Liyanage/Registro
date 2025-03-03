<?php
session_start();  // Start the session

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

// Redirect to login if guardian is not authenticated
if (!isset($_SESSION['applicant_id'])) {
    header("Location: guardian_login.php");
    exit();
}

// Get guardian's applicant_id from session
$applicant_id = $_SESSION['applicant_id'];
$panel_id = $_SESSION['panel_id'] ?? null; // Ensure panel_id is retrieved from session

// Initialize an array to hold messages
$messages = [];


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $first_name = $conn->real_escape_string($_POST['First_Name']);
    $last_name = $conn->real_escape_string($_POST['Last_Name']);
    $initial_name = $conn->real_escape_string($_POST['Surename']);
    $gender = $conn->real_escape_string($_POST['Gender']);
    $dob = $conn->real_escape_string($_POST['Date_of_Birth']);
    $religion = $conn->real_escape_string($_POST['Religion']);
    $address = $conn->real_escape_string($_POST['Address']);
    $city = 'Galle';  // Hardcoded value
    $country = 'Sri Lanka';  // Hardcoded value


    // Insert data into the 'child' table
    $sql_child = "INSERT INTO child (f_name, l_name, initial_name, gender, dob, religion, address, city, country , applicant_id) 
                  VALUES ('$first_name', '$last_name', '$initial_name', '$gender', '$dob', '$religion', '$address', '$city', '$country','$applicant_id')";



    if ($conn->query($sql_child) === TRUE) {
        $child_id = $conn->insert_id;  // Get inserted child ID
        $_SESSION['child_id'] = $child_id;  // Store child ID in session
        $messages[] = "Child data inserted successfully.";

     
        // Handle file upload
        if (isset($_FILES['dob_doc']) && $_FILES['dob_doc']['error'] === UPLOAD_ERR_OK) {
            
            $upload_dir = 'uploads/';
            $doc_path = $upload_dir . basename($_FILES['dob_doc']['name']);

            if ($_FILES['dob_doc']['size'] > 5000000) {
                $messages[] = "Birth Certificate document is too large.";
            } else {
                if (move_uploaded_file($_FILES['dob_doc']['tmp_name'], $doc_path)) {
                    $messages[] = "Birth Certificate uploaded successfully.";

                    // Update document path in the 'child' table
                    $sql_docs = "UPDATE child SET document_path = '$doc_path' WHERE child_id = '$child_id'";

                    if ($conn->query($sql_docs) !== TRUE) {
                        $messages[] = "Error updating document path: " . $conn->error;
                    }
                } else {
                    $messages[] = "Failed to upload Birth Certificate document.";
                }
            }
        } else {
            $messages[] = "No file uploaded or an upload error occurred.";
        }
    } else {
        $messages[] = "Error inserting child data: " . $conn->error;
    }   
    

        // Insert selected schools into the child_school table
        if (!empty($_POST['school']) && is_array($_POST['school'])) {
            foreach ($_POST['school'] as $school_id) {
                $school_id = $conn->real_escape_string($school_id);

            // Ensure that panel_id is used in the insert
            if ($panel_id) {
                $sql_child_school = "INSERT INTO child_school (child_id, school_id, panel_id) VALUES ('$child_id', '$school_id', '$panel_id')";
                if ($conn->query($sql_child_school) !== TRUE) {
                    $messages[] = "Error inserting school ID $school_id: " . $conn->error;
                }
            } else {
                $messages[] = "Panel ID is not set, cannot insert into child_school.";
            }
        } $messages[] = "Selected schools saved successfully.";
    } else {
        $messages[] = "No schools selected.";
    }
}



$sql = "SELECT school_id, school_name FROM school";
$result = $conn->query($sql);

$schools = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schools[] = $row;
    }
} else {
    echo "No schools found.";
}


// Close connection
$conn->close();

$schools_json = json_encode($schools); // Convert all schools to JSON
// Convert messages to JSON for debugging
$messages_json = json_encode($messages);
?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Application Form</title>
    <link rel="stylesheet" href="Style_applicant.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.js"></script>


</head>

<body id="b3">

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
    

        <div class="child_container">
            <h1> Child Registration Form</h1>
            
            <form action="child.php" method="post" enctype="multipart/form-data">
                <div class="flex-container">
                    <div class="fname-field">
                    <label for="first_name">Full Name</label>
                        <input type="text" id="first-name" name="First_Name" placeholder="First Name" required>
                    </div>
                    <div class="lname-field">
                        <label for="last-name">&nbsp;</label> <!-- Empty label to align with first name label -->
                        <input type="text" id="name" name="Last_Name" placeholder="Last Name" required>
                    </div>
                </div>
                
                <label for="name_with_initials">Name with Initials</label>
                <input type="text" id="name_with_initials" name="Surename" placeholder=" Enter your full name with initials" required>

                <div class="flex-container">
                    <div class="gender-field">
                        <label for="gender">Gender</label>
                        <select id="gender" name="Gender" required>
                            <option value="" disabled selected>Select your gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="dob-field">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="Date_of_Birth" required>
                    </div>
                </div>
        
                <label for="religion">Religion </label>
                <input type="text" id="religion" name="Religion" placeholder="Enter Religion" required>

                <label for="address"> Permenant Address</label>
                <input type="text" id="address" name="Address" placeholder=" Enter Permenant address" required>

                
                <!-- Hardcoded City and Country -->
                <label for="city">City</label>
                <input type="text" id="city" name="City" value="Galle" readonly>

                <label for="country">Country</label>
                <input type="text" id="country" name="Country" value="Sri Lanka" readonly>

                <label>Select a School</label>
                <div id="school-container">
                    <div class="school-select-container">
                        <select name="school[]" class="school-select" required>
                            <option value="" disabled selected>Select a school</option>
                            <?php foreach ($schools as $school): ?>
                                <option value="<?php echo $school['school_id']; ?>">
                                    <?php echo htmlspecialchars($school['school_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-plus-circle" id="add-school-btn"></i>
                    </div>
                </div>


                <!-- File Upload Section -->
                <h2>Upload Required Documents</h2>

                <label for="dob_doc">Birthcertificate Document</label>
                <input type="file" id="dob_doc" name="dob_doc" accept=".pdf,.jpg,.jpeg,.png" required>
        

                <div class="flex-container">
                    <div class="resetBtn-field">
                        <button type="reset" class="resetBtn"> Reset </button>
                    </div>
                    <div class="submitBtn-field">
                        <button type="submit" class="submitBtn"> Submit </button>
                    </div>
                </div>                                              
            </form>
        </div> 
    </div>


<footer>
	<p>&copy; 2025 Govenment College Admission Form. All rights reserved.</p>
</footer>






<script>
   document.addEventListener('DOMContentLoaded', function () {
    const maxSchools = 6;
    let schoolContainer = document.getElementById('school-container');
    let addSchoolBtn = document.getElementById('add-school-btn');
    let schoolOptions = Array.from(document.querySelectorAll('.school-select option'));

    let filteredOptions = schoolOptions; // Store filtered options

    // Function to filter schools based on gender
    function filterSchoolsByGender(gender) {
        if (gender === 'female') {
            return schoolOptions.filter(option => {
                const value = parseInt(option.value, 10);
                return value >= 1 && value <= 6;
            });
        } else if (gender === 'male') {
            return schoolOptions.filter(option => {
                const value = parseInt(option.value, 10);
                return value >= 7 && value <= 12;
            });
        } else {
            return schoolOptions;
        }
    }

    // Event listener for gender change
    document.getElementById('gender').addEventListener('change', function (e) {
        const gender = e.target.value;
        filteredOptions = filterSchoolsByGender(gender);
        updateAvailableSchools();

        // Update all existing school selects with the filtered options
        document.querySelectorAll('.school-select').forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '';

            // Add the guide option "Select a school"
            let guideOption = document.createElement('option');
            guideOption.value = '';
            guideOption.textContent = 'Select a school';
            guideOption.disabled = true;
            guideOption.selected = true;
            select.appendChild(guideOption);

            filteredOptions.forEach(option => {
                const newOption = option.cloneNode(true);
                select.appendChild(newOption);
            });

            // Retain the currently selected value if it matches the filtered options
            if (currentValue && Array.from(select.options).some(opt => opt.value === currentValue)) {
                select.value = currentValue;
            } else {
                select.value = '';
            }
        });
    });

    addSchoolBtn.addEventListener('click', function () {
        let schoolSelects = document.querySelectorAll('.school-select');
        if (schoolSelects.length < maxSchools) {
            let newSchoolContainer = document.createElement('div');
            newSchoolContainer.classList.add('school-select-container');

            let newSchoolSelect = document.createElement('select');
            newSchoolSelect.name = 'school[]';
            newSchoolSelect.classList.add('school-select');
            newSchoolSelect.required = true;

            // Add the guide option "Select a school"
            let guideOption = document.createElement('option');
            guideOption.value = '';
            guideOption.textContent = 'Select a school';
            guideOption.disabled = true;
            guideOption.selected = true;
            newSchoolSelect.appendChild(guideOption);

            // Append filtered options to the new select element
            filteredOptions.forEach(option => {
                newSchoolSelect.appendChild(option.cloneNode(true));
            });

            newSchoolContainer.appendChild(newSchoolSelect);

            let deleteBtn = document.createElement('i');
            deleteBtn.classList.add('fas', 'fa-minus-circle');
            deleteBtn.style.cursor = 'pointer';
            deleteBtn.addEventListener('click', function () {
                newSchoolContainer.remove();
                updateAvailableSchools();
            });

            newSchoolContainer.appendChild(deleteBtn);
            schoolContainer.appendChild(newSchoolContainer);

            updateAvailableSchools();
        } else {
            alert("You can select up to " + maxSchools + " schools.");
        }
    });

    // Function to remove selected schools from other select boxes
    function updateAvailableSchools() {
        let selectedSchools = Array.from(document.querySelectorAll('.school-select')).map(select => select.value);

        document.querySelectorAll('.school-select').forEach(select => {
            Array.from(select.options).forEach(option => {
                if (selectedSchools.includes(option.value) && option.value !== select.value) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    // Event listener to update schools when one is selected
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('school-select')) {
            updateAvailableSchools();
        }
    });
});



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
</script>


</body>
</html>
