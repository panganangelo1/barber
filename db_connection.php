<?php
$servername = "localhost";
$username = "root"; // Change if using a different user
$password = ""; // Change if a password is set
$database = "barber";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
