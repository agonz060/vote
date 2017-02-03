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
    require_once "edit/loadEditTable.php";

    // Server POST data capture
    if($_SERVER["REQUEST_METHOD"] == "POST") {
	    if(!empty($_POST["home"])) {
	    	redirectToHome();
	    }
    }
    // End Server POST data capture
?>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
	.button-review {
		color: white;
		background: rgb(28,184,65); 
		width: 80px;
	}
</style>
</head>
<body>
<!-- Menu options -->
<form method="post" id="menuForm" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<button name="home" value="home" class="pure-button">Home</button>
</form>
<!-- End Menu options -->
<table class="table table-bordered" align="center">
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th>Vote End Date</th>
			<th>Date Modified</th>
			<th>Date Deactivated</th>
			<th>Results</th>
		</tr>
	</thead>
	<tbody class="table-hover">
		<?php
			// Only display inactive polls (polls that have a start date > current date) 
			$selectCmd="Select * from Polls";
			$result = $conn->query($selectCmd);

			// Get poll data for displaying
			while($row = $result->fetch_assoc()) {
				$poll_id = $row["poll_id"];
				$title = $row["title"];
				$description = $row["description"];
				$dateModified = $row["dateModified"];
				$actDate = $row["actDate"];
				$deactDate = $row["deactDate"];
				$name=$row["name"];
				$pollType=$row["pollType"];
				$dept=$row["dept"];
				$effDate=$row["effDate"];
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
								<button class='btn btn-success' name='poll_id' value='$poll_id'>Results</button>
								<input type='hidden' name='title' value='$title'>
								<input type='hidden' name='description' value='$description'>
								<input type='hidden' name='dateActive' value='$actDate'>
								<input type='hidden' name='dateDeactive' value='$deactDate'>
								<input type='hidden' name='profName' value='$name'>
								<input type='hidden' name='pollType' value='$pollType'>
								<input type='hidden' name='dept' value='$dept'>
								<input type='hidden' name='effDate' value='$effDate'>
							</form>
						</td>			
					</tr>";
			}
		?>
	</tbody>
</table>
<!-- End of web page HTML -->
<!-- Start script -->
</body>
</html>
