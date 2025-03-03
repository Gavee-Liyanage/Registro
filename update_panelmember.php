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
$panelId = $_GET['id'] ?? null;
$panelData = null;

// Fetch old data for the panel member
if (isset($panelId)) {
    $query = "SELECT * FROM panel_member WHERE panel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $panelId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $panelData = $result->fetch_assoc();
    } else {
        // Redirect with an error
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'No record found for this ID';
        header("Location: view_panel.php");
        exit;
    }
    $stmt->close();
}

// Update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberName = $_POST['member_name'];
    $school = $_POST['school'];
    $panelContact = $_POST['panel_contact'];
    $panelEmail = $_POST['panel_email'];
    $panelUsername = $_POST['panel_username'];

    $query = "UPDATE panel_member SET member_name = ?, school = ?, panel_contact = ?, panel_email = ?, panel_username = ? WHERE panel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $memberName, $school, $panelContact, $panelEmail, $panelUsername, $panelId); 

    if ($stmt->execute()) {
        $_SESSION['alert_type'] = 'success';
        $_SESSION['alert_message'] = 'Record updated successfully.';
        $_SESSION['redirect_url'] = 'update_panelmember.php';
    } else {
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Error updating record.';
        $_SESSION['redirect_url'] = "update_panelmember.php?id=$panelId";
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
    <title>Update Panel Member Data</title>
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
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 2.5rem;
            color: #333;
        }

        label {
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
        <h2 class="h1 mb-4">Update Panel Member Data</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="member_name">Name</label>
                <input type="text" class="form-control" id="member_name" name="member_name" value="<?php echo htmlspecialchars($panelData['member_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="school">School</label>
                <input type="text" class="form-control" id="school" name="school" value="<?php echo htmlspecialchars($panelData['school'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="panel_contact">Contact</label>
                <input type="text" class="form-control" id="panel_contact" name="panel_contact" value="<?php echo htmlspecialchars($panelData['panel_contact'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="panel_email">Email</label>
                <input type="email" class="form-control" id="panel_email" name="panel_email" value="<?php echo htmlspecialchars($panelData['panel_email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="panel_username">Username</label>
                <input type="text" class="form-control" id="panel_username" name="panel_username" value="<?php echo htmlspecialchars($panelData['panel_username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="view_panel.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>


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
