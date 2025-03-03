<?php
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

// Check if applicant_id is set in the query string
if (isset($_GET['applicant_id'])) {
    $applicant_id = intval($_GET['applicant_id']);

    // Fetch files directly using a SQL query
    $sql = "SELECT water_bill, electrycity_bill, recideint_doc FROM recidencial_doc WHERE applicant_id = $applicant_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $water_bill = $row['water_bill'];
        $electricity_bill = $row['electrycity_bill'];
        $residential_doc = $row['recideint_doc'];

        // Create a zip archive to download all files
        $zip = new ZipArchive();
        $zipFileName = "documents_applicant_$applicant_id.zip";

        if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
            exit("Unable to create zip file.");
        }

        // Save the BLOB data as temporary files and add them to the zip
        if ($water_bill) {
            // Create a temporary file for water bill
            $tempFileWaterBill = tempnam(sys_get_temp_dir(), 'water_bill_') . '.pdf';
            file_put_contents($tempFileWaterBill, $water_bill); // Save the binary data as PDF
            $zip->addFile($tempFileWaterBill, 'Water_Bill.pdf');
        }

        if ($electricity_bill) {
            // Create a temporary file for electricity bill
            $tempFileElectricityBill = tempnam(sys_get_temp_dir(), 'electricity_bill_') . '.pdf';
            file_put_contents($tempFileElectricityBill, $electricity_bill); // Save the binary data as PDF
            $zip->addFile($tempFileElectricityBill, 'Electricity_Bill.pdf');
        }

        if ($residential_doc) {
            // Create a temporary file for residential document
            $tempFileResidentialDoc = tempnam(sys_get_temp_dir(), 'residential_doc_') . '.pdf';
            file_put_contents($tempFileResidentialDoc, $residential_doc); // Save the binary data as PDF
            $zip->addFile($tempFileResidentialDoc, 'Residential_Document.pdf');
        }

        $zip->close();

        // Serve the zip file for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        // Delete the temporary zip file and temp files after sending the download to the user
        unlink($zipFileName);
        unlink($tempFileWaterBill);
        unlink($tempFileElectricityBill);
        unlink($tempFileResidentialDoc);
    } else {
        echo "No documents found for this applicant.";
    }

    $result->free();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
