<?php
// Include the FPDF library
require('<fpdf186/fpdf.php');

// Database connection parameters
$host = 'localhost'; // Use 'localhost' instead of 'localhost:3306'
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch applicant data by ID
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0; // Ensure it's an integer

// Prepare the SQL statement
$sql = "SELECT * FROM child WHERE child_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters and execute the statement
$stmt->bind_param("i", $child_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if any records were found
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Create PDF document
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16); // Use Arial font, which is supported by FPDF

    // Title
    $pdf->Cell(0, 10, 'Child Information', 0, 1, 'C');
    $pdf->Ln(10);

    // Applicant details
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, 'Child ID:', 0, 0);
    $pdf->Cell(0, 10, $row['child_id'], 0, 1);

    $pdf->Cell(40, 10, 'Name:', 0, 0);
    $pdf->Cell(0, 10, $row['f_name'] . ' ' . $row['l_name'], 0, 1);

    $pdf->Cell(40, 10, 'Initials:', 0, 0);
    $pdf->Cell(0, 10, $row['initial_name'], 0, 1);

    $pdf->Cell(40, 10, 'Gender:', 0, 0);
    $pdf->Cell(0, 10, $row['gender'], 0, 1);

    $pdf->Cell(40, 10, 'Date of Birth:', 0, 0);
    $pdf->Cell(0, 10, $row['dob'], 0, 1);

    $pdf->Cell(40, 10, 'Religion:', 0, 0);
    $pdf->Cell(0, 10, $row['religion'], 0, 1);

    $pdf->Cell(40, 10, 'Address:', 0, 0);
    $pdf->MultiCell(0, 10, $row['address'], 0, 1);

    $pdf->Cell(40, 10, 'City:', 0, 0);
    $pdf->Cell(0, 10, $row['city'], 0, 1);

    $pdf->Cell(40, 10, 'Country:', 0, 0);
    $pdf->Cell(0, 10, $row['country'], 0, 1);

    // Output PDF
    $pdf->Output('D', 'Child_' . $row['child_id'] . '_Info.pdf');

} else {
    echo "No child found with ID: " . htmlspecialchars($child_id);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
