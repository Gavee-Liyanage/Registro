<?php
// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "registrodb");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the document ID from the URL
$docId = $_GET['docId'];

// Fetch the document paths from the database
$sql = "SELECT recideint_doc, waterbill, electrycity_bill FROM recidencial_doc WHERE doc_Id = $docId";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // List of documents to download
    $documents = [
        'Resident Document' => $row['recideint_doc'],
        'Water Bill' => $row['waterbill'],
        'Electricity Bill' => $row['electrycity_bill'],
    ];

    foreach ($documents as $docName => $docPath) {
        if (file_exists($docPath)) {
            // Set headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($docPath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($docPath));

            // Read the file and send it to the browser
            readfile($docPath);
        } else {
            echo "Error: File not found for $docName.";
        }
    }
} else {
    echo "Error: Document not found.";
}

// Close the connection
mysqli_close($conn);
?>
