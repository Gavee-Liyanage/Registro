<?php

$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "DELETE FROM panel_member WHERE panel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    
    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Record deleted successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'view_panel.php';  // Change the redirect URL to the appropriate page
                    }
                });
            </script>
        </body>
        </html>";
    } else {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Error deleting record.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'view_panel.php';  // Change the redirect URL to the appropriate page
                    }
                });
            </script>
        </body>
        </html>";
    }
    $stmt->close();
}

$conn->close();
?>
