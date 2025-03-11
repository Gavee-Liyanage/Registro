<?php

session_start();  // Start the session

// Check if the applicant is logged in
if (!isset($_SESSION['applicant_id'])) {
    die("Applicant not logged in.");
}

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

// Get the applicant ID from the session
$applicant_id = $_SESSION['applicant_id'];

// Fetch the file paths from the 'recidencial_doc' table
$sql = "SELECT recideint_doc, water_bill, electrycity_bill FROM recidencial_doc WHERE applicant_id = '$applicant_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $residential_doc = $row['recideint_doc'];
    $water_bill = $row['water_bill'];
    $electricity_bill = $row['electrycity_bill'];

    // Check if all files exist
    if (file_exists($residential_doc) && file_exists($water_bill) && file_exists($electricity_bill)) {
        // Create a temporary ZIP file
        $zip = new ZipArchive();
        $zip_filename = 'Residential Document_' . $applicant_id . '.zip';

        if ($zip->open($zip_filename, ZipArchive::CREATE) === TRUE) {
            // Add files to the ZIP archive
            $zip->addFile($residential_doc, 'Residential_Document.' . pathinfo($residential_doc, PATHINFO_EXTENSION));
            $zip->addFile($water_bill, 'Water_Bill.' . pathinfo($water_bill, PATHINFO_EXTENSION));
            $zip->addFile($electricity_bill, 'Electricity_Bill.' . pathinfo($electricity_bill, PATHINFO_EXTENSION));

            // Close the ZIP archive
            $zip->close();

            // Set headers for ZIP file download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
            header('Content-Length: ' . filesize($zip_filename));
            readfile($zip_filename);

            // Delete the temporary ZIP file after download
            unlink($zip_filename);
            exit;
        } else {
            echo "Failed to create ZIP file.";
        }
    } else {
        echo "One or more files are missing.";
    }
} else {
    echo "No documents found for this applicant.";
}

// Close connection
$conn->close();
?>