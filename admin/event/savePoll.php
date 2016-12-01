<?php 
	//echo "Entering savePoll.php";
	require 'connDB.php';

	// Poll data
	$pollId = $title = $descr = $actDate = $deactDate = "";
	
	// Voting info
	$profName = $fName = $lName = $profId = $pollData = $votingInfo = "";
	
	// Mysql queries
	$updatePolls = "UPDATE Polls SET title='$title', description='$descr', actDate='$actDate', ";
	$updatePolls += "deactDate='deactDate' WHERE poll_id='$pollId'";	

	//Set Timezone
	date_default_timezone_set('America/Los_Angeles');

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
		if(isset($_POST["reason"])) {
			$reason = $_POST["reason"];
		}
	}
	
	// Get poll data
	if(isset($pollData['title'])) {
		$title = $pollData['title'];
	}

	if(isset($pollData['descr'])) {
		$descr = $pollData['descr'];
	}

	if(isset($pollData['actDate'])) {
		$actDate = $pollData['actDate'];
	}

	if(isset($pollData['deactDate'])) {
		$deactDate = $pollData['deactDate'];
	}

	// Update Polls database if pollId exists
	if(isset($pollData["pollId"])) {
		// Check if poll already exists
		$pollId = $pollData['pollId'];

		$cmd = "Select * from Polls where poll_id='$pollId'";
		//echo "cmd: $cmd";
		
		$result = mysqli_query($conn, $cmd);
		$row = $result->fetch_assoc();
		
		$profName = "";
		$cmt = "";
		$history=":edit:" . "user" . ":" . date("Y-m-d") . ":" . $reason;
		$cmd = "UPDATE Polls SET title='$title', description='$descr', actDate='$actDate', ";
		$cmd .= "deactDate='$deactDate' , history=CONCAT(history,'$history') WHERE poll_id='$pollId'";	
		//echo "Update Polls cmd: $cmd";
		$result = mysqli_query($conn, $cmd);
			
		if(!$result) { echo "savePoll.php: could not update Polls table;"; }
		
	} else { // Create new Poll in database
		$history="create:" ."user" . ":" . date("Y-m-d") . ":" . $reason; 
		$cmd = "INSERT INTO Polls(title,description,actDate,deactDate,history,lName,pollType,dept,effDate)";
		$cmd .= "VALUES('$title','$descr','$actDate','$deactDate','$history','Zhen','Promotion','Computer Engineering','2017-07-1')";
		$result = mysqli_query($conn,$cmd);
		$newPollId=mysqli_insert_id($conn);
		//insert into Votes
		if(!$result) { echo "savePoll.php: could not create new Poll"; }
	}
	if(isset($pollId)) {
		$profIds = array();
		$cmd="Select user_id from Voters where poll_id='$pollId'";
		$result=mysqli_query($conn,$cmd);
		while($row=$result->fetch_assoc()) {
			array_push($profIds, $row["user_id"]);
		}
	}	
	if($votingInfo) {
		$keys = array_keys($votingInfo);
		for($x = 0; $x < sizeof($keys); ++$x) {
			if($keys[$x] != '0') {
				// Get first name and last name of professor
				$profName = $keys[$x];
				$profNamePieces = explode(" ",$keys[$x]); 
				$fName = $profNamePieces[0];
				$lName = $profNamePieces[1];
				// check if professor is already voting in the current poll
				$cmd = "SELECT user_id from Users WHERE fName='$fName' AND lName='$lName'";
				//echo "cmd: $cmd"; 

				$result = mysqli_query($conn, $cmd);
				if($row = $result->fetch_assoc()) {
					$profId = $row["user_id"];
					//Keep track of which profIds need to be deleted from Votes 
					$profIds = array_diff($profIds,array($profId));
					// Execute cmd, save result, store cmt
					//echo "profName: $profName";
					//echo "profId: $profId";
					//echo "profId: $profId";
					
					$cmt = $votingInfo[$profName];
					//echo "user: $profName cmt: $cmt";
					$cmd = "SELECT * FROM Voters WHERE user_id='$profId' AND poll_id='$pollId'";
					$result = mysqli_query($conn,$cmd);
					$row = $result->fetch_assoc();

					if($row) {
						$cmd = "UPDATE Voters set comment='$cmt' WHERE user_id='$profId' AND poll_id='$pollId'";
						$result = mysqli_query($conn, $cmd);
						
						if(!$result) {
						echo "savePoll.php: could not Update cmt for $profName";
						}

					} else {
						echo "user_id: " . $profId . "\n";
						echo "poll_id: " . $newPollId . "\n";
						echo "comment: " . $cmt . "\n";
						$cmd = "INSERT INTO Voters(user_id,poll_id,comment,voteFlag) VALUES ('$profId','$newPollId','$cmt',0)";
						$result = mysqli_query($conn,$cmd);
						
						if(!$result) {
						echo "savePoll.php: could not Insert cmt for $profName";
						}
					}
				}
			}
		}
		//Deletes a removed participating prof from Votes 
		if(!empty($profIds)) {
			//var_dump($profIds);	
			$cmd="Delete from Voters where poll_id=$pollId AND user_id IN ('".join("','", $profIds)."')";
			$result=mysqli_query($conn, $cmd);	
		}
	}
?>
