<?php
session_start(); 


$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gnName = $_POST['gn_name'];
    $gnArea = $_POST['gn_area'];
    $gnAreaId = $_POST['gn_area_id'];
    $gnUsername = $_POST['gn_username'];
    $gnContact = $_POST['gn_contact'];
    $gnEmail = $_POST['gn_email'];

    
    $query = "INSERT INTO grama_niladhari (gn_name, gn_area, gn_area_id, gn_username, gn_pw, gn_contact) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssisss", $gnName, $gnArea, $gnAreaId, $gnUsername, $gnContact, $gnEmail);

    if ($stmt->execute()) {
        $_SESSION['alert_type'] = 'success';
        $_SESSION['alert_message'] = 'Grama Niladhari added successfully.';
        header("Location: add_gramaniladhari.php"); 
        exit;
    } else {
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Error adding Grama Niladhari: ' . $stmt->error;
        header("Location: add_gramaniladhari.php");
        exit;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grama Niladhari</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .box {
            background-color: #add3ff;
            padding: 30px;
            border-radius: 10px;
            color: black;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .box .form-control {
            background-color: #ffffff;
            color: #000000;
        }

        .h1 {
            text-align: center;
            font-family: 'Trebuchet MS', Arial, sans-serif;
            font-size: 2.5rem;
            color: #333;
        }

        label {
            font-weight: 600;
        }

        .box .btn-primary {
            background-color: #dc2a2a;
            border-color: #ae2222;
        }

        .box .btn-primary:hover {
            background-color: #b50c0c;
            text-decoration: none;
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="box">
        <h2 class="h1 mb-4">Add Grama Niladhari</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="gn_name">Name</label>
                <input type="text" class="form-control" id="gn_name" name="gn_name" required>
            </div>
            <div class="form-group">
                <label for="gn_area">Area</label>
                <input type="text" class="form-control" id="gn_area" name="gn_area" required>
            </div>
            <div class="form-group">
                <label for="gn_area_id">Area ID</label>
                <input type="number" class="form-control" id="gn_area_id" name="gn_area_id" required>
            </div>
            <div class="form-group">
                <label for="gn_username">Username</label>
                <input type="text" class="form-control" id="gn_username" name="gn_username" required>
            </div>
            <div class="form-group">
                <label for="gn_contact">Contact</label>
                <input type="text" class="form-control" id="gn_contact" name="gn_contact" required>
            </div>
            <div class="form-group">
                <label for="gn_email">Email</label>
                <input type="email" class="form-control" id="gn_email" name="gn_email" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Add Grama Niladhari</button>
                <a href="view_gramaniladhari.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (!empty($_SESSION['alert_type']) && !empty($_SESSION['alert_message'])): ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['alert_type']; ?>',
            title: '<?php echo ucfirst($_SESSION['alert_type']); ?>',
            text: '<?php echo $_SESSION['alert_message']; ?>',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['alert_type'], $_SESSION['alert_message']); ?>
    <?php endif; ?>
});
</script>
</body>
</html>
