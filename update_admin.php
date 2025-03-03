<?php
session_start(); // Ensure session is started

// Database connection
$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$adminId = $_GET['id'] ?? null;
$adminData = null;

// Fetch old data for the admin
if (isset($adminId)) {
    $query = "SELECT * FROM admin_register WHERE admin_Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $adminData = $result->fetch_assoc();
    } else {
        // Redirect with an error
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'No record found for this ID';
        header("Location: view_admin.php");
        exit;
    }
    $stmt->close();
}

// Update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminName = $_POST['admin_name'];
    $adminUsername = $_POST['admin_username'];
    $adminContact = $_POST['admin_contact'];
    $adminEmail = $_POST['admin_email'];

    $query = "UPDATE admin_register SET admin_name = ?, admin_username = ?, admin_contact = ?, admin_email = ? WHERE admin_Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $adminName, $adminUsername, $adminContact, $adminEmail, $adminId); 

    if ($stmt->execute()) {
        $_SESSION['alert_type'] = 'success';
        $_SESSION['alert_message'] = 'Record updated successfully.';
        $_SESSION['redirect_url'] = 'update_admin.php';
       
    } else {
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Error updating record.';
        $_SESSION['redirect_url'] = "update_admin.php?id=$adminId";
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
    <title>Update Admin Data</title>
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

        .h1{
            text-align: center;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 2.5rem;
            color: #333;
        }

        label{
            font-weight: 600;
        }

        .box .btn-primary {
            background-color: #0056b3;
            border-color: #004085;
            
        }

        .box .btn-secondary {
            background-color: #6c757d;
            border-color: #5a6268;
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
        <h2 class="h1 mb-4">Update Admin Data</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="admin_name">Name</label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($adminData['admin_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_username">Username</label>
                <input type="text" class="form-control" id="admin_username" name="admin_username" value="<?php echo htmlspecialchars($adminData['admin_username'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_contact">Contact</label>
                <input type="text" class="form-control" id="admin_contact" name="admin_contact" value="<?php echo htmlspecialchars($adminData['admin_contact'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_email">Email</label>
                <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($adminData['admin_email'] ?? ''); ?>" required>
            </div>
        
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="view_admin.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (!empty($_SESSION['alert_type']) && !empty($_SESSION['alert_message']) && !empty($_SESSION['redirect_url'])): ?>
        const redirectUrl = '<?php echo $_SESSION['redirect_url']; ?>';
        Swal.fire({
            icon: '<?php echo $_SESSION['alert_type']; ?>',
            title: '<?php echo ucfirst($_SESSION['alert_type']); ?>',
            text: '<?php echo $_SESSION['alert_message']; ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = redirectUrl;
        });
        <?php unset($_SESSION['alert_type'], $_SESSION['alert_message'], $_SESSION['redirect_url']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>
