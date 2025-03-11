<?php

session_start();

require_once __DIR__ ."/Mail.php";

$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume the logged-in GN area ID is stored in a session variable

if (!isset($_SESSION['panel_id'])) {
    header("Location: panel_login.php");
    exit;
}
$loggedInPanelId = $_SESSION['panel_id'] ?? null;

$messages = [];



// ✅ Fetch both school_id and school name in a single query
$loggedInSchoolId = null;
$loggedInSchoolName = "Unknown";
if ($loggedInPanelId) {
    $schoolQuery = "SELECT school_id, school FROM panel_member WHERE panel_id = ?"; 
    $stmt = $conn->prepare($schoolQuery);
    $stmt->bind_param("i", $loggedInPanelId);
    $stmt->execute();
    $stmt->bind_result($SchoolId, $SchoolName);
    if ($stmt->fetch()) {
        $loggedInSchoolId = $SchoolId; 
        $loggedInSchoolName = $SchoolName; 
    }
    $stmt->close();
}


$sql = "
    SELECT 
        c.child_id, 
        c.initial_name, 
        c.gender, 
        COALESCE(a.approval_status, 0) AS approval_status, 
        COALESCE(cs.scl_approval, 0) AS scl_approval 
    FROM 
        child c
    LEFT JOIN 
        applicant a ON c.applicant_id = a.applicant_id
    LEFT JOIN 
        child_school cs ON c.child_id = cs.child_id
    LEFT JOIN 
        school s ON cs.school_id = s.school_id
    WHERE 
        cs.school_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loggedInPanelId);
$stmt->execute();
$result = $stmt->get_result();


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


// Handle AJAX request to update approval status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['child_id'], $_POST['scl_approval'], $_POST['school_id'])) {
        $childId = intval($_POST['child_id']);
        $sclApproval = intval($_POST['scl_approval']);
        $schoolId = intval($_POST['school_id']);
        $panelId = $_SESSION['panel_id'];

        if (!in_array($sclApproval, [1, 2])) {
            echo json_encode(["error" => "Invalid approval status value."]);
            exit;
        }

        $query = "UPDATE child_school SET scl_approval = ?, panel_id = ? WHERE child_id = ? AND school_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("iiii", $sclApproval, $panelId, $childId, $schoolId);
            if ($stmt->execute()) {

                // Fetch the applicant_id associated with the child
                $sqlApplicant = "SELECT applicant_id FROM child WHERE child_id = ?";
                $stmtApplicant = $conn->prepare($sqlApplicant);
                $stmtApplicant->bind_param("i", $childId);
                $stmtApplicant->execute();
                $resultApplicant = $stmtApplicant->get_result();

                if ($rowApplicant = $resultApplicant->fetch_assoc()) {
                    $applicant_id = $rowApplicant['applicant_id'];

                    // Send email notification to the applicant
                    $mail = new Mails();
                    $mail->sendSchoolSelectionNotification(); // Call the method to send emails
                }

                echo json_encode(["message" => "Approval status updated successfully!", "scl_approval" => $sclApproval]);
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
        .welcome-box {
            background-color: #add3ff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 30px;
            width: 90%;
            margin-left: 70px;
        }

        .welcome-text {
            color: #333;
        }

        .welcome-text h1 {
            font-size: 70px; 
            font-family:Georgia, 'Times New Roman', Times, serif;
            -webkit-text-fill-color: transparent; 
            -webkit-background-clip: text;       
            background-image: linear-gradient(315deg, #4834d4 0%, #0c0c0c 74%);

        }

        .welcome-text h5 {
            margin: 5px 0;
            color: #333;
            font-size: 20px; 
            font-family:Georgia, 'Times New Roman', Times, serif;
            margin-left: 10px;
        }

        .welcome-image {
            height: 150px;
            width: auto;
            border-radius: 8px;
            margin-left: 600px;
            margin-right: 10px;
        }


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

    
    <div class="container1">
        <div class="welcome-box">
            <div class="welcome-text">
                <h1>Welcome</h1>
                <h5>Panel Member of <strong><?php echo htmlspecialchars($loggedInSchoolName); ?></strong></h5> 
            </div>
            <img src="image/pm.jpeg" alt="GN Area" class="welcome-image" />
        </div>
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
                                <button class="btn btn-success approve-btn" 
                                        data-child-id="<?php echo $row['child_id']; ?>" 
                                        data-school-id="<?php echo htmlspecialchars($loggedInSchoolId); ?>" 
                                        <?php echo ($row['scl_approval'] == 1) ? 'disabled' : ''; ?>>Approve</button>
                                <button class="btn btn-danger reject-btn" 
                                        data-child-id="<?php echo $row['child_id']; ?>" 
                                        data-school-id="<?php echo htmlspecialchars($loggedInSchoolId); ?>" 
                                        <?php echo ($row['scl_approval'] == 2) ? 'disabled' : ''; ?>>Reject</button>
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
            const childId = button.getAttribute('data-child-id');
            const schoolId = button.getAttribute('data-school-id');
            updateApproval(childId, schoolId, 1, button);
        });
    });

    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', () => {
            const childId = button.getAttribute('data-child-id');
            const schoolId = button.getAttribute('data-school-id');
            updateApproval(childId, schoolId, 2, button);
        });
    });
});

function updateApproval(childId, schoolId, sclApproval, button) {
    const formData = new FormData();
    formData.append('child_id', childId);
    formData.append('school_id', schoolId);
    formData.append('scl_approval', sclApproval);

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
                const approveButton = document.querySelector(`.approve-btn[data-child-id="${childId}"]`);
                const rejectButton = document.querySelector(`.reject-btn[data-child-id="${childId}"]`);

                if (sclApproval === 1) {
                    approveButton.disabled = true;
                    rejectButton.disabled = false;
                } else if (sclApproval === 2) {
                    approveButton.disabled = false;
                    rejectButton.disabled = true;
                }
            });
        } else {
            Swal.fire({ title: 'Error', text: data.error || 'An error occurred.', icon: 'error' });
        }
    })
    .catch(error => console.error('Error:', error));
}
    

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