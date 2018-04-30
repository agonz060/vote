<?php
    session_start();

    require_once "includes/sessionHandling.php";
    require_once "includes/redirections.php";
    require_once "includes/connDB.php";
    require_once "../includes/functions.php";

    // Session verfication
    if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
    // End Session verification


    // Server POST capture here
    if($_SERVER["REQUEST_METHOD"] == "POST") {
    	// User menu options
		if(!empty($_POST["home"])) {
			redirectToHome();
		}
    }
?>
<html>
<head>
<title>Edit Polls</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
	.button-edit {
		color: white;
		background: rgb(28,184,65);
		width: 80px;
	}
	.button-delete {
		color: white;
		background: rgb(202,60,60);
		width: 80px;
	}
</style>
</head>
<body>
<!-- User Menu -->
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="home.php">BCOE Voting</a>
			</div>
			<ul class="nav navbar-nav">
				<li><a href="home.php">Home</a></li>
				<li><a href="vote.php">Create Poll</a></li>
				<li class="active"><a href="edit.php">Edit Poll</a></li>
				<li><a href="review.php">Review Poll</a></li>
				<li><a href="manage.php">Manage Users</a></li>
			</ul>
		</div>
	</nav>
<!-- End User Menu -->
<div class="container">
<table class="table table-responsive table-hover table-bordered" align="center">
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th>Vote End Date</th>
			<th>Date Modified</th>
			<th>Date Deactivated</th>
			<th>Edit/Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php
			// Only display inactive polls (polls that have a start date > current date)
			$selectCmd="Select * from Polls Where deactDate > CURDATE()";
			$result = $conn->query($selectCmd);
			$encodedPollData = "";
			// Get poll data for displaying
			while($row = $result->fetch_assoc()) {
				$encodedPollData = json_encode($row);
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
							<form method='post' id='editForm' action='vote.php'>
								<button class='button-edit pure-button' name='pollData' value='$encodedPollData'>Edit</button>
								<!-- <button class='button-edit pure-button' name='poll_id' value='$poll_id'>Edit</button> -->
								<input type='hidden' name='title' value='$title'>
								<input type='hidden' name='description' value='$description'>
								<input type='hidden' name='dateActive' value='$actDate'>
								<input type='hidden' name='dateDeactive' value='$deactDate'>
								<input type='hidden' name='profName' value='$name'>
								<input type='hidden' name='pollType' value='$pollType'>
								<input type='hidden' name='dept' value='$dept'>
								<input type='hidden' name='effDate' value='$effDate'>
							</form>
							<button class='button-delete pure-button' value='$poll_id'>Delete</button>
						</td>
					</tr>";
			}
		?>
	</tbody>
</table>
</div>
<!-- End of web page HTML -->
<!-- Start script -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $(".button-delete").click(function() {
            var confirmation = prompt("Type DELETE if you are sure you want to delete entry");
            if(confirmation == "DELETE") {
                var poll_id = $(this).val();
                $.post("edit/deleteRow.php", {poll_id : poll_id},
                function(response,status) {
                    location.reload();
                });
            }
        });
    });
</script>
<!-- End script -->
</body>
</html>