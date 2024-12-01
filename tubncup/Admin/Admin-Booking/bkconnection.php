<?php
$host = "localhost";
$username = "root"; // Change this if needed
$password = ""; // Change this if needed
$dbname = "cafe-form"; // Ensure this matches your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
