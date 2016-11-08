<?php
	$servername = "localhost";
	$username = "root";
	$pwd = "Computer_Science99";
	$db = "Voting";
	$resultsAvailable = false;

	$conn = new mysqli($servername, $username, $pwd, $db);

	if($conn->connect_error) {
		echo "Connection error: " . $conn->connect_error . "<br>";
	}
	
	$selectCmd = "Select * from Polls where deactDate > CURDATE()";
	
	$result = $conn->query($selectCmd);
?>
