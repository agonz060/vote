<?php
	if(isset($_POST["dateModified"])) {
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
		echo $key;
		$delCmd = "DELETE from Saved WHERE poll_id='".$key."'";
		$result = $conn->query($delCmd);
	}
	else {
		echo "dateModified not set"; 
	}
?>
