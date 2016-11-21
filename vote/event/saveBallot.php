<?php 
	echo "Entering saveBallot.php";
	// Ballot data
	$ballotData = $votingInfo = "";

	// Check if data is set before accessing
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["ballotData"])) {
			$ballotData = $_POST["ballotData"];
			echo "ballotData: "; print_r($ballotData);
		}

		if(isset($_POST["votingInfo"])) {
			$votingInfo = $_POST["votingInfo"];
			echo " votingInfo: "; print_r($votingInfo);
		}
	}

	$selectCmd = "SELECT Professors.fName, Professors.lName FROM Votes INNER JOIN Professors";
			$selectCmd = $selectCmd." ON Professors.prof_id=Votes.prof_id WHERE Votes.poll_id=$pollId";
			$result = $conn->query($selectCmd);
			
			
			// Execute sql command and loop through results	
			while($row = $result->fetch_assoc()) {
				// Store basic professor information
				$name = $row["fName"];
				$name = $name. " ".$row["lName"];
				
				// Store professor comments using professors name as key
				$prof = array($name => "");
				$profCmts = array_merge($profCmts, $prof);

				// Display voting professors name
				echo "<option value='$name'>".$name."</option>";
				
			}
?>
