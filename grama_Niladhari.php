<?php

$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


session_start();
$loggedInGnAreaId = isset($_SESSION['gn_area_id']) ? $_SESSION['gn_area_id'] : null;

$messages = [];


// Fetch the GN area name from the database
$loggedInGnAreaName = "Unknown";
if ($loggedInGnAreaId) {
    $gnQuery = "SELECT gn_area FROM grama_niladhari WHERE gn_area_id = ?";
    $stmt = $conn->prepare($gnQuery);
    $stmt->bind_param("i", $loggedInGnAreaId);
    $stmt->execute();
    $stmt->bind_result($gnAreaName);
    if ($stmt->fetch()) {
        $loggedInGnAreaName = $gnAreaName;
    }
    $stmt->close();
}

// Fetch all applicants for the logged-in GN area
$sql = "SELECT a.applicant_id, a.initial_name, a.gender, a.address, a.nic, a.approval_status
        FROM applicant a
        JOIN applicant_gn_area ag ON a.applicant_id = ag.applicant_id
        WHERE ag.gn_area_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loggedInGnAreaId);
$stmt->execute();
$result = $stmt->get_result();


//Handle AJAX request to update guardian approval status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['applicant_id'], $_POST['approval_status'])) {
        // Sanitize inputs
        $applicant_id = intval($_POST['applicant_id']);
        $approval_status = intval($_POST['approval_status']);

        // **Ensure valid approval status (e.g., 1 for Approve, 2 for Reject)**
        if (!in_array($approval_status, [1, 2])) {
            echo json_encode(["error" => "Invalid approval status value."]);
            exit;
        }

        // Update query
        $query = "UPDATE applicant SET approval_status = ? WHERE applicant_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
           
            $stmt->bind_param("ii", $approval_status, $applicant_id);

            // Execute the query
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(["message" => "Approval status updated successfully!"]);
                } else {
                    echo json_encode(["error" => "No rows affected. Check if the applicant ID exists."]);
                }
            } else {
                echo json_encode(["error" => "Failed to execute the statement: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Failed to prepare the statement: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Missing required parameters (applicant_id or approval_status)."]);
    }

    $conn->close();
    exit;
}


$conn->close();

$messages_json = json_encode($messages);
?>













<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grama Niladhari Home Page</title>
    <link rel="stylesheet" href="Style_GN.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.js"></script>
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
            margin-left: 50px;
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

<!-- Display logged-in GN Area -->
<div class="container1" id="c1">
    <div class="welcome-box">
        <div class="welcome-text">
            <h1>Welcome</h1>
            <h5>Grama Niladhari of <strong><?php echo htmlspecialchars($loggedInGnAreaName); ?></strong></h5>
        </div>
        <img src="image/gn_page.jpg" alt="GN Area" class="welcome-image" />
    </div>
</div>

<!-- Table displaying applicants for the logged-in GN area -->
<div class="container mt-5">
    <table class="table table-bordered" id="childTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name of the applicant</th>
                <th>Gender</th>
                <th>Address</th>
                <th>NIC</th>
                <th>Residential Document</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['applicant_id']; ?></td>
                        <td><?php echo $row['initial_name']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['nic']; ?></td>
                        <td>
                            <a href="view_Residential.php?applicant_id=<?php echo $row['applicant_id']; ?>">
                                View Files
                            </a>
                        </td>
                        <td>
                            <button class="btn btn-success approve-btn" data-applicant_id="<?php echo $row['applicant_id']; ?>" 
                                    <?php echo ($row['approval_status'] == 1) ? 'disabled' : ''; ?>>Approve</button>
                            <button class="btn btn-danger reject-btn" data-applicant_id="<?php echo $row['applicant_id']; ?>" 
                                    <?php echo ($row['approval_status'] == 2) ? 'disabled' : ''; ?>>Reject</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No applicants available for your GN area.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<script>

// event listeners to the Approve and Reject buttons.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', () => {
            const applicantId = button.getAttribute('data-applicant_id');
            updateApproval(applicantId, 1); // 1 for approval
        });
    });

    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', () => {
            const applicantId = button.getAttribute('data-applicant_id');
            updateApproval(applicantId, 2); // 2 for rejection
        });
    });
});


function updateApproval(applicantId, approvalStatus) {
    const formData = new FormData();
    formData.append('applicant_id', applicantId);
    formData.append('approval_status', approvalStatus);

    fetch('grama_Niladhari.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
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
            console.error('Error during fetch:', error);
            Swal.fire({
                title: 'Error',
                text: 'There was an issue updating the approval status. Please check the console for details.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}
</script>

</body>
</html>
