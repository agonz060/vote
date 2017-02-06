<?php
	session_start();
	require_once 'connDB.php';
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(!empty($_POST["home"])) {
			redirectToHome();
		}
	}
	function redirectToHome() {
		echo "<script type='text/javascript'>location.href='../home.php'</script> ";
		return;
	}
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
		$stmt = $stmt." WHERE Users.title !='Assistant Professor' and Voters.poll_id='$poll_id' GROUP BY Voters.poll_id";
		//echo $stmt . '\n';
		$result = $conn->query($stmt) or die($conn->error);
		while($row=$result->fetch_assoc()) {
			$eligible = $row["Eligible"];
		}
		return $eligible;
	}

	$poll_id = $pollType = $profName= $dept = "";
	if($_SERVER["REQUEST_METHOD"] == "POST") {
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
	}
	$pollDataTable = pollTypeToTable($pollType);
	if(empty($pollDataTable)) {
		echo 'No Table for this pollType';
	}

	$eligibleVotes =  getEligibleVotes($pollId,$conn);
	$vote = $voteCount = $forCount = $againstCount = $abstainCount = 0;
	$totalVotes = $eligibleVotes;
	$stmt = "SELECT toLevel, vote, count(vote) AS voteCount FROM $pollDataTable WHERE poll_id=$pollId GROUP BY vote";
	$result = $conn->query($stmt) or die($conn->error);
	while($row = $result->fetch_assoc()) {
		$vote = $row["vote"];
		$voteCount = $row["voteCount"];
		$toLevel = $row["toLevel"];
		$totalVotes = $totalVotes - $voteCount;
		if($vote == "0") {
			$forCount = $voteCount;
		}
		if($vote == "1") {
			$againstCount = $voteCount;
		}
		if($vote == "2") {
			$abstainCount = $voteCount;
		}
	}
	if(!$result) {
		echo 'returned empty';
	}
	if(empty($toLevel)) {
		$toLevel = "";
	}
?>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<style></style>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="../home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="../home.php">Home</a></li>
			<li><a href="../vote.php">Create Poll</a></li>
			<li><a href="../edit/editTable.php">Edit Poll</a></li>
			<li class="active"><a href="../edit/reviewTable.php">Review Poll</a></li>
			<li><a href="../add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<div class="container">
<table class="table table-responsive table-hover table-bordered" align="center">
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
</div>
</body>
</html>
