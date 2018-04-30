<?php
	session_start();
	require_once '../includes/connDB.php';
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
		$romanArr[1] = "I"; $romanArr[2] = "II"; $romanArr[3] = "III";
		$romanArr[4] = "IV"; $romanArr[5] = "V"; $romanArr[6] = "VI";
		$romanArr[7] = "VII"; $romanArr[8] = "VIII"; $romanArr[9] = "IX";
		$romanArr[10] = "X";
		return $romanArr[$num];
	}
	//Returns the appropriate SQL table to be used for each pollType
	function pollTypeToTable($pollType) {
		switch($pollType) {
			case "Merit":
				return "Merit_Data";
			case "Promotion":
				return "Associate_Promotion_Data";
			case "Reappointment":
				return "Reappointment_Data";
			case "Fifth Year Review":
				return "Fifth_Year_Review_Data";
			case "Fifth Year Appraisal":
				return "Fifth_Year_Appraisal_Data";
			case "Other":
				return "Other_Poll_Date";
			default:
				return "";
		}	
	}
	function getTitleRestrictions($profTitle, $pollType, $advisoryVote) {
		$titleRestriction = "";
		if($profTitle == "Full Professor") {
			if($pollType == "Promotion" || $pollType == "Merit" || $pollType == "Fifth Year Review") {
				if(!$advisoryVote) { $titleRestriction = "='Full Professor'"; }
			}
			elseif($pollType == "Reappointment") {
				if(!$advisoryVote) { $titleRestriction = "!='Assistant Professor'"; }
			}
			else {
				if(!$advisoryVote) { $titleRestriction = "!='Assistant Professor'"; }
				else { $titleRestriction = "='Assistant Professor'"; }
	
			}
		}
		else {
			if($advisoryVote) { $titleRestriction= "='Assistant Professor'";}
			else { $titleRestriction ="!='Assistant Professor'";}
		}
		return $titleRestriction;
	}	
 	//Gets the count of all eligible voters in a poll. 	
	function getEligibleVotes($poll_id, $profTitle, $pollType,$conn,$advisoryFlag=0) {
		$titleRestriction = getTitleRestrictions($profTitle, $pollType,$advisoryFlag);
		if($titleRestriction == "" && $advisoryFlag) {return 0;}
		$eligible = 0;
		$stmt = "SELECT count(Voters.user_id) AS Eligible FROM Voters INNER JOIN Users ON Users.user_id=Voters.user_id";
		$stmt = $stmt." WHERE Users.title $titleRestriction and Voters.poll_id='$poll_id' GROUP BY Voters.poll_id";
		//echo $stmt . '\n';
		$result = $conn->query($stmt) or die($conn->error);
		while($row=$result->fetch_assoc()) {
			$eligible = $row["Eligible"];
		}
		return $eligible;
	}
	//Merits and Promotions
	function getMultiActionCounts($pollId, $pollDataTable, $profTitle,$pollType, $conn, $advisoryFlag=0) {
		$titleRestriction = getTitleRestrictions($profTitle, $pollType,$advisoryFlag);
		$multiVoteCounts = [];
		$eligibleVotes = getEligibleVotes($pollId,$profTitle,$pollType,$conn,$advisoryFlag);
		$stmt = "SELECT * from Poll_Actions WHERE poll_id=$pollId";
		$result = $conn->query($stmt) or die($conn->error);
		while($row = $result->fetch_assoc()) {
			$action_num = $row["action_num"];
			$multiVoteCounts[$action_num]['fromLevel'] = $row["fromLevel"];
			$multiVoteCounts[$action_num]['toLevel'] = $row["toLevel"];
			$multiVoteCounts[$action_num]['accelerated'] = $row["toLevel"];
			$multiVoteCounts[$action_num]['for'] = 0;
			$multiVoteCounts[$action_num]['against'] = 0;
			$multiVoteCounts[$action_num]['abstain'] = 0;
			$multiVoteCounts[$action_num]['eligible'] = 0;
			$multiVoteCounts[$action_num]['total'] = 0;
		}
		if($titleRestriction == "" && $advisoryFlag) {return $multiVoteCounts;}
		foreach($multiVoteCounts as $key => $item) {
			$totalVotes = $eligibleVotes;
			$multiVoteCounts[$key]['for']= $multiVoteCounts[$key]['against'] = $multiVoteCounts[$key]['abstain'] = 0;
			$stmt = "SELECT $pollDataTable.vote, count($pollDataTable.vote) as voteCount FROM $pollDataTable INNER JOIN Users ON Users.user_id=$pollDataTable.user_id";
			$stmt .= " WHERE Users.title $titleRestriction AND $pollDataTable.poll_id=$pollId AND $pollDataTable.action_num=$key GROUP BY $pollDataTable.vote";
			$result = $conn->query($stmt) or die($conn->error);
			while($row = $result->fetch_assoc()) {
				$vote = $row["vote"];
				$voteCount = $row["voteCount"];
				$totalVotes = $totalVotes - $voteCount;
				if($vote == "1") {
					$multiVoteCounts[$key]['for'] = $voteCount;
				}
				if($vote == "2") {
					$multiVoteCounts[$key]['against'] = $voteCount;
				}
				if($vote == "3") {
					$multiVoteCounts[$key]['abstain'] = $voteCount;
				}
			}
			$multiVoteCounts[$key]['eligible'] = $eligibleVotes;
			$multiVoteCounts[$key]['total'] = $totalVotes;
		}		
		return $multiVoteCounts;
	}
	//The rest of the poll types
	function getVoteCounts($pollId, $pollDataTable, $profTitle,$pollType,$conn,$advisoryFlag=0) {
		$voteCounts = array("for"=> 0,"eligible"=>0,"against"=>0, "abstain"=>0, "total"=>0);
		$titleRestriction = getTitleRestrictions($profTitle, $pollType,$advisoryFlag);
		if($titleRestriction == "" && $advisoryFlag) {return $voteCounts;}
		$voteCounts['eligible'] =  getEligibleVotes($pollId,$profTitle,$pollType,$conn,$advisoryFlag);
		$vote = $voteCount = 0;
		$totalVotes = $voteCounts['eligible'];
		$stmt = "SELECT $pollDataTable.vote, count($pollDataTable.vote) as voteCount FROM $pollDataTable INNER JOIN Users ON Users.user_id=$pollDataTable.user_id";
		$stmt .= " WHERE Users.title $titleRestriction AND $pollDataTable.poll_id=$pollId GROUP BY $pollDataTable.vote";
		//$stmt = "SELECT vote, count(vote) AS voteCount FROM $pollDataTable WHERE poll_id=$pollId GROUP BY vote";
		$result = $conn->query($stmt) or die($conn->error);
		while($row = $result->fetch_assoc()) {
			$vote = $row["vote"];
			$voteCount = $row["voteCount"];
			$totalVotes = $totalVotes - $voteCount;
			if($vote == "1") {
				$voteCounts['for'] = $voteCount;
			}
			if($vote == "2") {
				$voteCounts['against'] = $voteCount;
			}
			if($vote == "3") {
				$voteCounts['abstain'] = $voteCount;
			}
		}
		if(!$result) {
			return "";
		}
		$voteCounts['total'] = $totalVotes;
		return $voteCounts;

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
		if(isset($_POST["profTitle"])) {
			$profTitle = cleanInput($_POST["profTitle"]);
		}
	}
	
?>
<html>
<head>
<title>View Results</title>
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
			<li><a href="home.php">Home</a></li>
			<li><a href="vote.php">Create Poll</a></li>
			<li><a href="edit.php">Edit Poll</a></li>
			<li class="active"><a href="review.php">Review Poll</a></li>
			<li><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<?php 
	$pollDataTable = pollTypeToTable($pollType);
	if(empty($pollDataTable)) {
		echo 'No Table for this pollType';
	}
	if($pollType == "Merit" || $pollType == "Promotion") {
		$multiActionCounts = getMultiActionCounts($pollId, $pollDataTable, $profTitle,$pollType,$conn);
		$multiAdActionCounts = getMultiActionCounts($pollId, $pollDataTable, $profTitle,$pollType,$conn,1); 
		foreach($multiActionCounts as $key => $item) {
			$eligibleAdVotes = $multiAdActionCounts[$key]['eligible'];
			$forAdCount = $multiAdActionCounts[$key]['for'];
			$againstAdCount = $multiAdActionCounts[$key]['against'];
			$abstainAdCount = $multiAdActionCounts[$key]['abstain'];
			$totalAdVotes = $multiAdActionCounts[$key]['total'];

			$eligibleVotes = $multiActionCounts[$key]['eligible'];
			$forCount = $multiActionCounts[$key]['for'];
			$againstCount = $multiActionCounts[$key]['against'];
			$abstainCount = $multiActionCounts[$key]['abstain'];
			$totalVotes = $multiActionCounts[$key]['total'];
			$toLevel = intToRoman($multiActionCounts[$key]['toLevel']);
			$fromLevel = intToRoman($multiActionCounts[$key]['fromLevel']);
			$accelerated = $multiActionCounts[$key]['accelerated'];
			$accelText = "";
			if($accelerated) { $accelText = "Accelerated ";}
			echo '<div class="container">
			<table class="table table-responsive table-hover table-bordered" align="center">
				<thead>
					<tr>
						<th>'.$profName.'\'s '.$accelText.$pollType.' From '.$profTitle.' Step '.$fromLevel.' to '.$toLevel.'</th>
						<th>Eligible</th>
						<th>For</th>
						<th>Against</th>
						<th>Abstain</th>
						<th>Not Voting/Unvailable</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Votes: </td>
						<td>'.$eligibleVotes.'</td>
						<td>'.$forCount.'</td>
						<td>'.$againstCount.'</td>
						<td>'.$abstainCount.'</td>
						<td>'.$totalVotes.'</td>
					</tr>
					<tr>
						<td>Advisory Votes: </td>
						<td>'.$eligibleAdVotes.'</td>
						<td>'.$forAdCount.'</td>
						<td>'.$againstAdCount.'</td>
						<td>'.$abstainAdCount.'</td>
						<td>'.$totalAdVotes.'</td>
					</tr>
				</tbody>
			</table>
			</div>';
		}
	}
	else {
		$voteCounts = getVoteCounts($pollId, $pollDataTable,$profTitle,$pollType,$conn);
		$voteAdCounts = getVoteCounts($pollId, $pollDataTable, $profTitle,$pollType,$conn,1);

		//advisory
		$eligibleAdVotes = $voteAdCounts['eligible'];
		$forAdCount = $voteAdCounts['for'];
		$againstAdCount = $voteAdCounts['against'];
		$abstainAdCount = $voteAdCounts['abstain'];
		$totalAdVotes = $voteAdCounts['total'];
		
		$eligibleVotes = $voteCounts['eligible'];
		$forCount = $voteCounts['for'];
		$againstCount = $voteCounts['against'];
		$abstainCount = $voteCounts['abstain'];
		$totalVotes = $voteCounts['total'];
		echo '<div class="container">
		<table class="table table-responsive table-hover table-bordered" align="center">
			<thead>
				<tr>
					<th>'.$profName.'\'s '.$pollType.' for '.$profTitle.'</th>
					<th>Eligible</th>
					<th>For</th>
					<th>Against</th>
					<th>Abstain</th>
					<th>Not Voting/Unvailable</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Votes: </td>
					<td>'.$eligibleVotes.'</td>
					<td>'.$forCount.'</td>
					<td>'.$againstCount.'</td>
					<td>'.$abstainCount.'</td>
					<td>'.$totalVotes.'</td>
				</tr>
				<tr>
					<td>Advisory Vote: </td>
					<td>'.$eligibleAdVotes.'</td>
					<td>'.$forAdCount.'</td>
					<td>'.$againstAdCount.'</td>
					<td>'.$abstainAdCount.'</td>
					<td>'.$totalAdVotes.'</td>
				</tr>
			</tbody>
		</table>
		</div>';
	}
?>

</body>
</html>
