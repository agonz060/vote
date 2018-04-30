<?php
	require_once '../includes/connDB.php';

	if(isset($_POST["poll_id"])) {
		$resultsAvailable = false;

		$id = $_POST["poll_id"];
		$delCmd = "DELETE from Polls WHERE poll_id=$id";
		$result = mysqli_query($conn, $delCmd);

		if(!$result) { echo "deleteRow.php: could not delete poll"; }

		$delVotersCmd = "DELETE FROM Voters WHERE poll_id='$id'";
		$result = mysqli_query($conn,$delVotersCmd);

		if(!$result) { echo "deleteRow.php: could not delete voters with poll_id: $id"; }
	}
	else {
		echo "deleteRow.php: poll_id not set";
	}

