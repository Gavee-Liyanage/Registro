<?php

session_start();
if (!isset($_SESSION['panel_id'])) {
    header("Location: panel_login.php");
    exit;
}

$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize an array to hold messages
$welcome =["Please select your school"];
$messages = [];

// Default SQL query
$sql = "
    SELECT 
        c.child_id, c.initial_name, c.gender, 
        COALESCE(a.approval_status) AS approval_status
    FROM 
        child c
    LEFT JOIN 
        applicant a ON c.applicant_id = a.applicant_id
    LEFT JOIN 
        child_school cs ON c.child_id = cs.child_id
    LEFT JOIN 
        school s ON cs.school_id = s.school_id";

// Check if school filter is applied
$sclAreaFilter = isset($_GET['school_id']) ? $_GET['school_id'] : null;

if ($sclAreaFilter) {
    // Add WHERE clause to filter by school and GROUP BY child_id
    $sql .= " WHERE cs.school_id = ? GROUP BY c.child_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sclAreaFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default query with GROUP BY to remove duplicates
    $sql .= " GROUP BY c.child_id";
    $result = $conn->query($sql);
}

// Fetch all schools for filtering
$sclQuery = "SELECT school_id, school_name FROM school";
$sclResult = $conn->query($sclQuery);

if ($sclResult) {
    $messages[] = "Schools fetched successfully.";
} else {
    $messages[] = "Error fetching schools: " . $conn->error;
}

// Handle AJAX request to update approval status
if (isset($_GET['child_id']) && isset($_POST['approval_status'])) {
    $applicant_id = $_GET['child_id'];
    $approval_status = $_GET['approval_status'];

    $updateQuery = "SELECT applicant SET approval_status = ? WHERE applicant_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $approval_status, $applicant_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $messages[] = "Approval status updated successfully!";
    } else {
        $messages[] = "Failed to update approval status!";
    }
    $stmt->close();
    echo json_encode($messages);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['child_id'], $_POST['scl_approval'], $_POST['school_id'])) {
        $childId = intval($_POST['child_id']);
        $sclApproval = intval($_POST['scl_approval']);
        $schoolId = intval($_POST['school_id']);
        $panelId = $_SESSION['panel_id'];

        // Update query
        $query = "UPDATE child_school SET scl_approval = ?, panel_id = ? WHERE child_id = ? AND school_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("iiii", $sclApproval, $panelId, $childId, $schoolId);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Approval status updated successfully!"]);
            } else {
                echo json_encode(["error" => "Failed to update approval status: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Failed to prepare the statement: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Missing required parameters."]);
    }

    $conn->close();
    exit;
}


$conn->close();

$welcome_json = json_encode($welcome);

$messages_json = json_encode($messages);

?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Member Page</title>
    <link rel="stylesheet" href="Style_panel.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.js"></script>

    <head>
    <!-- Other head content -->
    <style>
        /* CSS for Popup window */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        /* Popup window */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 30px;
            width: 400px;
            height: 485px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            overflow-y: auto;
        }

        /* Close button */
        .popup .close {
            position: absolute;
            top: 15px;
            right: 17px;
            font-size: 40px;
            cursor: pointer;
        }

        .popup .close:hover {
            color: crimson;
        }

        /* Form elements */
        .popup h2 {
            margin-bottom: 30px;
            font-size: 20px;
            text-align: center;
            margin-top: 30px;
            font: 800;
            color:mediumblue;
           font-family: Georgia, 'Times New Roman', Times, serif;
        }

        .popup ul {
            list-style-type: none;
            padding: 20px;
            gap: 10px;
        }

        .popup li {
            margin-bottom: 10px;
            gap: 10px;
        }

        .popup button {
            width: 90%;
            padding: 10px;
            background-color: crimson;
            margin-top: 10px;
            margin-left: 20px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: darkred;
        }

        /* CSS for overlay (show when popup is visible) */
        .overlay.show {
            display: block;
        }
    </style>
</head>

<body id="b2">
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="about.php"> About </a>
            <a href="contact.php"> Contact </a>
        </div>
    
        <div class="right-nav">
                <a href='#' class="Registro"> Registro </a>
        </div>
    </div>

    <!-- School Filter -->
    <div class="container mt-5">
        <form method="GET" action="">
            <label for="school_id">Filter by School:</label>
            <select name="school_id" id="school_id" class="form-control">
                <option value="">--Select School--</option>
                <?php if ($sclResult && $sclResult->num_rows > 0): ?>
                    <?php while ($sclRow = $sclResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($sclRow['school_id']); ?>" 
                            <?php echo ($sclAreaFilter == $sclRow['school_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sclRow['school_name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-3">Filter</button>
        </form>
    </div>

    <!-- Child Data Table -->
    <div class="container mt-5">
        <table class="table table-bordered" id="childTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Gender</th>
                    <th>Form Document</th>
                    <th>Birth Certificate Document</th>
                    <th>Grama Niladhari Status</th>
                    <th>School Selected Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['child_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['initial_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><a href="generate_pdf.php?child_id=<?php echo htmlspecialchars($row['child_id']); ?>">View Document</a></td>
                            <td><a href="view_birthcertificate.php?child_id=<?php echo htmlspecialchars($row['child_id']); ?>">View Files</a></td>
                            

                            <td>
                                <?php if ($row['approval_status'] == 1): ?>
                                    <span   class="btn btn-success btn-sm">Approved</span>
                                <?php elseif ($row['approval_status'] == 0): ?>
                                    <span   class="btn btn-primary btn-sm">Pending</span>
                                    <?php elseif ($row['approval_status'] == 2): ?>
                                    <span   class="btn btn-danger btn-sm">Rejected</span>
                                <?php endif; ?>
                            </td>
                            

                            <td>
                                <a href="javascript:void(0);" onclick="showPopup(<?php echo $row['child_id']; ?>)">View Selected Schools</a>
                            </td>

                            <td>
                                <button class="btn btn-success btn-sm approve-btn" data-child-id="<?php echo htmlspecialchars($row['child_id']); ?>">Approve</button>
                                <button class="btn btn-danger btn-sm reject-btn" data-child-id="<?php echo htmlspecialchars($row['child_id']); ?>">Reject</button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
    </div>

    <!-- Popup Modal -->
<div id="popupOverlay" class="overlay">
    <div class="popup">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Selected Schools for Child</h2>
        <div id="popupContent">
            <!-- The content will be loaded here dynamically via AJAX -->
        </div>
        <button onclick="closePopup()">Close</button>
    </div>
</div>


    

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', () => {
                const schoolId = document.getElementById('school_id').value; // Get the selected school ID
                if (!schoolId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Please select your school from the dropdown',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return; // Prevent further execution if no school is selected
                }
                const childId = button.getAttribute('data-child-id');
                updateApproval(childId, 1); // 1 for approval
            });
        });

        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', () => {
                const schoolId = document.getElementById('school_id').value; // Get the selected school ID
                if (!schoolId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Please select your school from the dropdown',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return; // Prevent further execution if no school is selected
                }
                const childId = button.getAttribute('data-child-id');
                updateApproval(childId, 2); // 2 for rejection
            });
        });
    });

    function updateApproval(childId, sclApproval) {
    const formData = new FormData();
    formData.append('child_id', childId);
    formData.append('scl_approval', sclApproval);

    const schoolId = document.getElementById('school_id').value;
    if (!schoolId) {
        Swal.fire({
            title: 'Error',
            text: 'Please select your school from the dropdown',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    formData.append('school_id', schoolId);

    fetch('panel_member.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire({
                    title: 'Success',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload(); // Reload the page to reflect changes
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'An error occurred.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'There was an issue updating the approval status',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}

    document.addEventListener('DOMContentLoaded', function() {
            // Fetch the PHP welcome message as JSON
            const welcome = <?php echo $welcome_json; ?>;

            // Fetch the PHP success message as JSON
            const messages = <?php echo $messages_json; ?>;

            // Show welcome message when no school is selected
            if (!<?php echo json_encode($sclAreaFilter); ?>) {
                Swal.fire({
                    title: 'Welcome!',
                    html: welcome.join('<br>'),
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }

            // Listen for changes in school selection and show success message
            const schoolSelectElement = document.querySelector('#school_id');
            schoolSelectElement.addEventListener('change', function() {
                const selectedSchool = schoolSelectElement.value;
                if (selectedSchool && messages && messages.length > 0) {
                    // Show success message when user selects a school
                    Swal.fire({
                        title: 'Success!',
                        html: messages.join('<br>'),
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            });
    });

        // Function to show the popup and fetch schools for the selected child
        function showPopup(childId) {
            // Make an AJAX request to fetch selected schools
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "view_selected_schools.php?child_id=" + childId, true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    // Show the popup with the response data
                    document.getElementById('popupContent').innerHTML = xhr.responseText;
                    document.getElementById("popupOverlay").classList.add("show");
                } else {
                    alert("Failed to load data.");
                }
            };
            xhr.send();
        }

        // Function to close the popup
        function closePopup() {
            document.getElementById("popupOverlay").classList.remove("show");
        }


</script>

</body>
</html>