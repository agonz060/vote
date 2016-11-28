<?php
	if(isset($_POST["poll_id"])) {
		$servername = "localhost";
		$username = "root";
		$pwd = "Computer_Science99";
		$db = "Voting";
		$resultsAvailable = false;

		$conn = new mysqli($servername, $username, $pwd, $db);

		if($conn->connect_error) {
			echo "Connection error: " . $conn->connect_error . "<br>";
		}
		$key = $_POST["poll_id"];	
		$delCmd = "DELETE from Polls WHERE poll_id=$key";
		$result = $conn->query($delCmd);
	}
	else {
		echo "poll_id not set"; 
	}
?>
