<?php 
	//echo "Entering savePoll.php";
	require 'event/connDB.php';

	// poll data
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
		//$cmd = "UPDATE Polls SET title='"+$pollData['title']+"' description='"+$pollData['descr']+"'";
		//$cmd += " actDate='"+$pollData['actDate']+"' deactDate='"+$pollData['deactDate']+"'";
		$pollId = $pollData['pollId'];

		$cmd = "Select * from Polls where poll_id='$pollId'";
		//echo "cmd: $cmd";
		$result = mysqli_query($conn, $cmd);
		$row = $result->fetch_assoc();

		if($row) {
			$title = $row['title'];
			echo "title: $title";
		} else {
			echo "no result returned from query";
		}


	}
	/*if($pollData["pollId"])
	$selectCmd = """*/
	//echo "Exiting savePoll.php";
?>
