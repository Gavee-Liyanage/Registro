<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "db_connection.php";  // Correct the path to your DBConnection file
require_once "./vendor/autoload.php"; 

class Mails
{
    // Remove the DBConnection instantiation as it's no longer needed
    private $dbConnection;

    public function __construct()
    {
        // Use the global connection
        $this->dbConnection = $GLOBALS['dbConnection'];
    }

    public function sendDocumentUploadNotification($applicantId)
    {
        $conn = $this->dbConnection;

        // Check if the applicant has uploaded residential documents
        $sqlCheck = "SELECT recideint_doc, water_bill, electrycity_bill FROM recidencial_doc WHERE applicant_id = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $applicantId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();


        if ($rowCheck = $resultCheck->fetch_assoc()) {
            if (empty($rowCheck['recideint_doc']) && empty($rowCheck['waterbill']) && empty($rowCheck['electrycity_bill'])) {
                echo json_encode(['success' => false, 'message' => "No residential documents uploaded."]);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Applicant not found in residential documents table."]);
            return;
        }

        // Fetch the gn_area_id associated with the applicant
        $sqlArea = "SELECT gn_area_id FROM applicant_gn_area WHERE applicant_id = ?";
        $stmtArea = $conn->prepare($sqlArea);
        $stmtArea->bind_param("s", $applicantId);
        $stmtArea->execute();
        $resultArea = $stmtArea->get_result();

        

        if ($rowArea = $resultArea->fetch_assoc()) {
            $gnAreaId = $rowArea['gn_area_id'];

            // Fetch Grama Niladhari's email using gn_area_id
            $sql = "SELECT gn_email FROM grama_niladhari WHERE gn_area_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $gnAreaId);  // Using integer for gn_area_id
            $stmt->execute();
            $result = $stmt->get_result();


            if ($row = $result->fetch_assoc()) {
                $gnEmail = $row['gn_email'];


                // Fetch applicant details
                $sql2 = "SELECT f_name, l_name FROM applicant WHERE applicant_id = ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("s", $applicantId);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                if ($row2 = $result2->fetch_assoc()) {
                    $applicantName = $row2['f_name'] . " " . $row2['l_name'];

                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->SMTPAuth = true;
                        $mail->Host = "smtp.gmail.com";
                        $mail->Username = 'registro.education@gmail.com';
                        $mail->Password = "ljgm dzwp cwtw ifzt"; // Replace with actual app password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;

                        $mail->setFrom("noreply@gmail.com", "Grama Niladhari Notifications");
                        $mail->addAddress($gnEmail);
                        $mail->Subject = "New Residential Documents Uploaded";
                        $mail->Body = "Dear Grama Niladhari,\n\nAn applicant ($applicantName) has uploaded residential documents. Please review them in the system.\n\nBest regards,\nYour System";

                        $mail->send();
                        echo "<script>alert('Email sent successfully');</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
                    }
                }
            } else {
                echo "<script>alert('Error: No Grama Niladhari found for the specified area');</script>";
            }
        } else {
            echo "<script>alert('Error: No Grama Niladhari found for the specified area');</script>";
        }
    }



    public function sendSchoolSelectionNotification()
    {
        $conn = $this->dbConnection;

        // Fetch applicants who have been selected by panel members
        $sql = "SELECT a.applicant_id, a.email, a.f_name, a.l_name 
                FROM applicant a
                JOIN child c ON a.applicant_id = c.applicant_id
                JOIN child_school cs ON c.child_id = cs.child_id
                WHERE cs.scl_approval = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $applicantId = $row['applicant_id'];
                $applicantEmail = $row['email'];
                $applicantName = $row['f_name'] . " " . $row['l_name'];

                // Send email to the applicant
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;
                    $mail->Host = "smtp.gmail.com";
                    $mail->Username = 'registro.education@gmail.com';
                    $mail->Password = "ljgm dzwp cwtw ifzt"; // Replace with actual app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom("noreply@gmail.com", "School Selection Notifications");
                    $mail->addAddress($applicantEmail);
                    $mail->Subject = "School Selection Notification";
                    $mail->Body = "Dear $applicantName,\n\nCongratulations! You have been selected by the panel members for school placement. Please check the system for further details.\n\nBest regards,\nYour School Selection Committee";

                    $mail->send();
                    echo "<script>alert('Email sent successfully to $applicantName');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Error sending email to $applicantName: " . addslashes($e->getMessage()) . "');</script>";
                }
            }
        } else {
            echo "<script>alert('No applicants selected for school placement.');</script>";
        }
    }

}

?>
