<?php
$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8mb4
$conn->set_charset("utf8mb4");

// Store the connection as a global variable
$GLOBALS['dbConnection'] = $conn;
?>
