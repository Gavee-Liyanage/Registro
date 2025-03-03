<?php

$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $child_id = $_POST['child_id'];
    $school_id = $_POST['school_id'];

    // Insert the selected school into the child_school table
    $query = "INSERT INTO child_school (child_id, school_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $child_id, $school_id);
    
    if ($stmt->execute()) {
        echo "School selected successfully.";
        header("Location: panel_member.php"); // Redirect back to panel member page
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select School</title>
</head>
<body>
    <h1>Select School for Child ID: <?php echo $_GET['child_id']; ?></h1>
    
    <form method="POST" action="select_school.php">
        <input type="hidden" name="child_id" value="<?php echo $_GET['child_id']; ?>">
        
        <label for="school_id">Select School:</label>
        <select name="school_id" required>
            <?php
            // Fetch all schools for the dropdown
            $query = "SELECT school_id, school_name FROM school";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()):
            ?>
                <option value="<?php echo $row['school_id']; ?>"><?php echo $row['school_name']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit">Select School</button>
    </form>
    
    <a href="panel_member.php">Back to Panel Member Page</a>
</body>
</html>
