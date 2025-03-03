<?php
// Database connection parameters
$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];

    // Retrieve the document path from the database
    $sql = "SELECT document_path FROM child WHERE child_id = '$child_id'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $file_path = $row['document_path'];

        if (file_exists($file_path)) {
            // Set headers to prompt a file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Pragma: public');

            // Clear output buffer
            ob_clean();
            flush();

            // Read and output the file
            readfile($file_path);
            exit();
        } else {
            echo "File not found.";
        }
    } else {
        echo "No document associated with this child ID.";
    }
} else {
    echo "Child ID not specified.";
}

// Close connection
$conn->close();
?>
