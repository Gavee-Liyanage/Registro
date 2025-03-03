<?php

$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];
    
    // Fetch selected schools for this child where approval status is 1 (approved)
    $query = "SELECT sc.school_name, cs.scl_approval_date
              FROM child_school cs
              JOIN school sc ON cs.school_id = sc.school_id
              WHERE cs.child_id = ? AND cs.scl_approval = 1";  // Filter by scl_approval = 1 (approved)
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schools = [];
    while ($row = $result->fetch_assoc()) {
        $schools[] = [
            'school_name' => $row['school_name'],
            'scl_approval_date' => $row['scl_approval_date']
        ];
    }

    // Output the selected schools with approval date
    if (!empty($schools)) {
        echo '<ul>';
        foreach ($schools as $school) {
            echo '<li>' . htmlspecialchars($school['school_name']) . ' - Approved: ' . htmlspecialchars($school['scl_approval_date']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No schools approved for this child.</p>';
    }
}

?>
