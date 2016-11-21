<?php 
	//echo "Entering savePoll.php";
	require 'event/connDB.php';

	// poll data
	$title = $descr = $actDate = $deactDate = "";
	$pollData = $votingInfo = "";

	// Check if data is set before accessing
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["pollData"])) {
			$pollData = $_POST["pollData"];
			//echo "pollData: "; print_r($pollData);
			 //print_r(array_keys($pollData));
			 //echo "pollId: " + $pollData['pollId'];
		}

		if(isset($_POST["votingInfo"])) {
			$votingInfo = $_POST["votingInfo"];
			//echo " votingInfo: "; print_r($votingInfo);
		}
	}



	if(isset($pollData["pollId"])) {
		// Check if poll already exists
		$pollId = $pollData['pollId'];

		$cmd = "Select * from Polls where poll_id='$pollId'";
		//echo "cmd: $cmd";
		$result = mysqli_query($conn, $cmd);
		$row = $result->fetch_assoc();

		// If poll exists, then update
		if($row) {
			$title = $pollData['title'];
			$descr = $pollData['descr'];
			$actDate = $pollData['actDate'];
			$deactDate = $pollData['deactDate'];

			$cmd = "UPDATE Polls SET title='$title', description='$descr', actDate='$actDate', ";
			$cmd += "deactDate='deactDate' WHERE poll_id='$pollId'";
			$result = mysqli_query($conn, $cmd);
			//$row = $result->fetch_assoc();

			//print_r(array_keys($votingInfo));
			
			$keys = array_keys($votingInfo);
			for($x = 0; $x < sizeof($keys); ++$x) {
				if($keys[$x] != '0') {
					echo "key($x) = $keys[$x]";
				}
			}


		}
	}
?>