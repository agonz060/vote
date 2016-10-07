<?php
// Setup variables 
$servername = "localhost";
$username = "root";
$password = "on^yp6Ai";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";
?>
