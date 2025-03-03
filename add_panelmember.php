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
    $memberName = $_POST['member_name'];
    $school = $_POST['school'];
    $panelContact = $_POST['panel_contact'];
    $panelEmail = $_POST['panel_email'];
    $panelUsername = $_POST['panel_username'];

    // Prepare the query
    $query = "INSERT INTO panel_member (member_name, school, panel_contact, panel_email, panel_username) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $memberName, $school, $panelContact, $panelEmail, $panelUsername);

    if ($stmt->execute()) {
        $_SESSION['alert_type'] = 'success';
        $_SESSION['alert_message'] = 'Panel member added successfully.';
        header("Location: add_panelmember.php");
        exit;
    } else {
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Error adding panel member: ' . $stmt->error;
        header("Location: add_panelmember.php");
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
    <title>Add Panel Member</title>
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
        <h2 class="h1 mb-4">Add Panel Member</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="member_name">Name</label>
                <input type="text" class="form-control" id="member_name" name="member_name" required>
            </div>
            <div class="form-group">
                <label for="school">School</label>
                <input type="text" class="form-control" id="school" name="school" required>
            </div>
            <div class="form-group">
                <label for="panel_contact">Contact</label>
                <input type="text" class="form-control" id="panel_contact" name="panel_contact" required>
            </div>
            <div class="form-group">
                <label for="panel_email">Email</label>
                <input type="email" class="form-control" id="panel_email" name="panel_email" required>
            </div>
            <div class="form-group">
                <label for="panel_username">Username</label>
                <input type="text" class="form-control" id="panel_username" name="panel_username" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Add Panel Member</button>
                <a href="view_panel.php" class="btn btn-secondary">Cancel</a>
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
