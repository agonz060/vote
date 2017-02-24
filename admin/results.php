<?php
	session_start();

    require_once "event/sessionHandling.php";
    require_once "event/redirections.php";

	// Session verfication 
    if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
    // End Session verification

	require_once 'edit/connDB.php';

	// Server POST data capture
	$poll_id = $pollType = $profName= $dept = "";
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(!empty($_POST["home"])) {
			redirectToHome();
		}
		if(isset($_POST["poll_id"])) {
			$pollId = cleanInput($_POST["poll_id"]);
		}
		if(isset($_POST["pollType"])) {
			$pollType = cleanInput($_POST["pollType"]);
		}
		if(isset($_POST["profName"])) {
			$profName = cleanInput($_POST["profName"]);
		}
		if(isset($_POST["dept"])) {
			$dept = cleanInput($_POST["dept"]);
		}
		if(isset($_POST["profTitle"])) {
			$profTitle = cleanInput($_POST["profTitle"]);
		}
	}// End Server POST data capture

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	function intToRoman($num) {
		if($num=="") {
			return "";
		}
		$romanArr = array();
		$romanArr[1] = "I"; $romanArr[2] = "II"; $romanArr[3]= "III";
		$romanArr[4] = "IV"; $romanArr[5] = "V";
		return $romanArr[$num];
	}
	//Returns the appropriate SQL table to be used for each pollType
	function pollTypeToTable($pollType) {
		switch($pollType) {
			case "Merrit":
				return "";
			case "Promotion":
				return "Associate_Promotion_Data";
			case "Reappointment":
				return "Reappointment_Data";
			case "Fifth Year Review":
				return "Fifth_Year_Review_Data";
			case "Fifth Year Appraisal":
				return "Fifth_Year_Appraisal_Data";
			default:
				return "";
		}	
	}
 	//Gets the count of all eligible voters in a poll. Excludes Advisory Votes(assistant profs)	
	function getEligibleVotes($poll_id, $conn) {
		$eligible = 0;
		$stmt = "SELECT count(Voters.user_id) AS Eligible FROM Voters INNER JOIN Users ON Users.user_id=Voters.user_id";
		$stmt .= " WHERE Users.title !='Assistant Professor' and Voters.poll_id='$poll_id' GROUP BY Voters.poll_id";
		//echo $stmt . '\n';
		$result = $conn->query($stmt) or die($conn->error);
		while($row=$result->fetch_assoc()) {
			$eligible = $row["Eligible"];
		}
		return $eligible;
	}
	function getVotes($pollDataTable, $pollId, $conn) {
		$eligibleVotes =  getEligibleVotes($pollId,$conn);
		$vote = $voteCount = $forCount = $againstCount = $abstainCount = 0;
		$totalVotes = $eligibleVotes;
		// Load data from appropiate database
		$stmt = "SELECT vote, count(vote) AS voteCount FROM $pollDataTable WHERE poll_id=$pollId GROUP BY vote";
		$result = $conn->query($stmt) or die($conn->error);
		while($row = $result->fetch_assoc()) {
			$vote = $row["vote"];
			$voteCount = $row["voteCount"];
			$totalVotes = $totalVotes - $voteCount;
			if($vote == "1") {
				$forCount = $voteCount;
			}
			if($vote == "2") {
				$againstCount = $voteCount;
			}
			if($vote == "3") {
				$abstainCount = $voteCount;
			}
		}

	}
	// End Helper functions

	// Displaying user data in table
	$pollDataTable = pollTypeToTable($pollType);
	if(empty($pollDataTable)) {
		echo 'No Table for this pollType';
	}
	// Data vars
	$eligibleVotes =  getEligibleVotes($pollId,$conn);
	$vote = $voteCount = $forCount = $againstCount = $abstainCount = 0;
	$totalVotes = $eligibleVotes;
	// Load data from appropiate database
	$stmt = "SELECT toLevel, vote, count(vote) AS voteCount FROM $pollDataTable WHERE poll_id=$pollId GROUP BY vote,toLevel";
	$result = $conn->query($stmt) or die($conn->error);
	while($row = $result->fetch_assoc()) {
		$vote = $row["vote"];
		$voteCount = $row["voteCount"];
		$toLevel = $row["toLevel"];
		$totalVotes = $totalVotes - $voteCount;
		if($vote == "1") {
			$forCount = $voteCount;
		}
		if($vote == "2") {
			$againstCount = $voteCount;
		}
		if($vote == "3") {
			$abstainCount = $voteCount;
		}
	}
	if(!$result) {
		echo 'returned empty';
	}
	if(empty($toLevel)) {
		$toLevel = "";
	}
	// End Displaying user data in table
?>
<html>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style></style>
</head>
<body>
<form id="menuForm" method="post" value="home" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<button name="home" class="pure-button" value="home">Home</button> 
</form>
<table class="pure-table pure-table-bordered" align="center">
	<thead>
		<tr>
			<th>
			<?php echo $profName . ', Step ' . intToRoman($toLevel); ?> 
			</th>
			<th>Eligible</th>
			<th>For</th>
			<th>Against</th>
			<th>Abstain</th>
			<th>Not Voting/Unvailable</th>
		</tr>
	</thead>
	<tbody>
	<?php echo	"<tr>
			<td>Vote: </td>
			<td>$eligibleVotes</td>
			<td>$forCount</td>
			<td>$againstCount</td>
			<td>$abstainCount</td>
			<td>$totalVotes</td>
		</tr>";
	?>
	</tbody>
</table>
</body>
</html>
