<?php
	session_start();

    require_once '../includes/connDB.php';
    // require_once "includes/sessionHandling.php";
	// if(!isAdmin()) {
 //        signOut();
 //    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
	    // if(!empty($_POST["home"])) {
	    // 	redirectToHome();
	    // }
    }
    function redirectToHome() {
		echo "<script type='text/javascript'>location.href='/admin/home.php'</script>";
    }
    function getActionInfo($pollId) {
        global $conn;
        $actionInfoArray = array();
        $fromTitle = $fromStep = $toTitle = $toStep = $accelerated = "";

        $query = "SELECT fromTitle,fromStep,toTitle,toStep,accelerated FROM Poll_Actions WHERE poll_id=?";
        $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
        mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_bind_result($stmt,$fromTitle,$fromStep,$toTitle,$toStep,$accelerated) or die(mysqli_error($conn));
        while(mysqli_stmt_fetch($stmt)) {
            $actionInfo = array( "fromTitle" => $fromTitle,
                            "fromStep" => $fromStep,
                            "toTitle" => $toTitle,
                            "toStep" => $toStep,
                            "accelerated" => $accelerated );
            $actionInfoArray[] = $actionInfo;
        }
        mysqli_stmt_close($stmt);
        return $actionInfoArray;
    }
    function getActionCount($pollId) {
        global $conn;
        $actionCount = 0;
        $query = "SELECT count(action_num) FROM Poll_Actions WHERE poll_id=?";
        $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
        mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_bind_result($stmt, $actionCount) or die(mysqli_error($conn));
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $actionCount;
    }
?>
<html>
<head>
<title>Review Polls</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
	.button-review {
		color: white;
		background: rgb(28,184,65);
		width: 80px;
	}
</style>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li><a href="vote.php">Create Poll</a></li>
			<li><a href="edit.php">Edit Poll</a></li>
			<li class="active"><a href="review.php">Review Poll</a></li>
			<li><a href="manage.php">Manage Users</a></li>
		</ul>
	</div>
</nav>
<div class="container">
<table class="table table-responsive table-bordered table-hover" align="center">
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th>Poll Start Date</th>
			<th>Poll End Date</th>
			<th>Date Modified</th>
			<th>Results</th>
		</tr>
	</thead>
	<tbody>
		<?php
			// Poll types
			$OTHER = "Other";
			$MERIT = "Merit";
			$PROMOTION = "Promotion";
			$actionInfoArray = "";

			$resultId = 0;
			$selectCmd="Select * from Polls ORDER BY actDate DESC, title ASC";
			$result = $conn->query($selectCmd);
			// Get poll data for displaying
			while($row = $result->fetch_assoc()) {
				# variables
				$resultId++;
				$pollData = $row;
				$poll_id = $row["poll_id"];
				$pollType = $row['pollType'];
				if($pollType == $MERIT || $pollType == $PROMOTION || $pollType == $OTHER) {
					$actionInfoArray = getActionInfo($poll_id);
					$pollData["actionInfoArray"] = $actionInfoArray;
					$pollData['numActions'] = getActionCount($poll_id);
				}
				$title = $row["title"];
				$description = $row["description"];
				$dateModified = $row["dateModified"];
				$actDate = $row["actDate"];
				$deactDate = $row["deactDate"];
				$encodedPollData = json_encode($pollData);
				echo "<tr>
						<td>
							$title
						</td>
						<td>
							$description
						</td>
						<td>
							$actDate
						</td>
						<td>
							$deactDate
						</td>
						<td>
							$dateModified
						</td>
						<td>
							<form method='post' id='reviewForm' action='results.php'>
								<button class='btn btn-success' name='encodedPollData' value='$encodedPollData'>Results</button>
							</form>
							<form method='post' id='downloadDocx' action='event/download.php'>
								<input type='hidden' name='poll_id' value='$poll_id'>
								<input type='hidden' name='title' value='$title'>
								<button class='btn btn-primary' name='resultId' value='$resultId'>Download</button>
							</form>
						</td>
					</tr>";
			}
		?>
	</tbody>
</table>
</div>
</body>
</html>
