<?php
session_start();
require('fpdf/fpdf.php');

// Check if the panel member is logged in
if (!isset($_SESSION['panel_username'])) {
    header("Location: panel_login.php");
    exit();
}

// Database connection
$host = "registrodb";
$username = "dbgavee";
$password = "1234";
$db = "registrodb";

$con = mysqli_connect($host, $username, $password, $db);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch applicant data (You can modify the query to fetch based on your logic)
$applicant_id = 1; // For example purposes, use the actual applicant ID
$sql = "SELECT * FROM applicant WHERE applicant_id = $applicant_id";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $applicant = mysqli_fetch_assoc($result);

    // Create the PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'Application Form', 1, 1, 'C');

    // Applicant data
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'First Name:', 1);
    $pdf->Cell(140, 10, $applicant['f_name'], 1, 1);
    
    $pdf->Cell(50, 10, 'Last Name:', 1);
    $pdf->Cell(140, 10, $applicant['l_name'], 1, 1);
    
    $pdf->Cell(50, 10, 'Initials Name:', 1);
    $pdf->Cell(140, 10, $applicant['initial_name'], 1, 1);

    $pdf->Cell(50, 10, 'Gender:', 1);
    $pdf->Cell(140, 10, $applicant['gender'], 1, 1);

    $pdf->Cell(50, 10, 'Date of Birth:', 1);
    $pdf->Cell(140, 10, $applicant['dob'], 1, 1);

    $pdf->Cell(50, 10, 'Religion:', 1);
    $pdf->Cell(140, 10, $applicant['religion'], 1, 1);

    $pdf->Cell(50, 10, 'Address:', 1);
    $pdf->Cell(140, 10, $applicant['address'], 1, 1);

    $pdf->Cell(50, 10, 'City:', 1);
    $pdf->Cell(140, 10, $applicant['city'], 1, 1);

    // More fields if necessary...

    // Output PDF to the browser
    $pdf->Output('D', 'Application_Form.pdf'); // 'D' will force download
} else {
    echo "No application form found.";
}

// Close the database connection
mysqli_close($con);
?>
