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
		$id = $_POST["poll_id"];	
		$delCmd = "DELETE from Polls WHERE poll_id=$id";
		$result = mysqli_query($conn, $delCmd);

		if(!$result) { echo "deleteRow.php: could not delete poll"; }

		$delVotersCmd = "DELETE FROM Voters WHERE poll_id='$id'";
		$result = mysqli_query($conn,$delVotersCmd);

		if(!$result) { echo "deleteRow.php: could not delete voters with poll_id: $id"; }

	}
	else {
		echo "poll_id not set"; 
	}
?>
