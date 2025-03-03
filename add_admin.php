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
    $adminName = $_POST['admin_name'];
    $adminUsername = $_POST['admin_username'];
    $adminContact = $_POST['admin_contact'];
    $adminEmail = $_POST['admin_email'];

    
    $query = "INSERT INTO admin_register (admin_name, admin_username, admin_contact, admin_email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $adminName, $adminUsername, $adminContact, $adminEmail);

    if ($stmt->execute()) {
        $_SESSION['alert_type'] = 'success';
        $_SESSION['alert_message'] = 'Admin added successfully.';
        header("Location: add_admin.php"); 
        exit;
    } else {
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Error adding admin: ' . $stmt->error;
        header("Location: add_admin.php"); 
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
    <title>Add Admin</title>
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
        <h2 class="h1 mb-4">Add Admin</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="admin_name">Name</label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" required>
            </div>
            <div class="form-group">
                <label for="admin_username">Username</label>
                <input type="text" class="form-control" id="admin_username" name="admin_username" required>
            </div>
            <div class="form-group">
                <label for="admin_contact">Contact</label>
                <input type="text" class="form-control" id="admin_contact" name="admin_contact" required>
            </div>
            <div class="form-group">
                <label for="admin_email">Email</label>
                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Add Admin</button>
                <a href="view_admin.php" class="btn btn-secondary">Cancel</a>
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
