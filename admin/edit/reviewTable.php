<?php 
    require_once '../event/connDB.php';
    session_start(); 
    if($_SERVER["REQUEST_METHOD"] == "POST") {
	    if(!empty($_POST["home"])) {
	    	redirectToHome();
	    }
    }	    
    function redirectToHome() {
	echo "<script type='text/javascript'>location.href='../home.php'</script>";
    }
?>
<html>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
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
			<a class="navbar-brand" href="../home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="../home.php">Home</a></li>
			<li><a href="../vote.php">Create Poll</a></li>
			<li><a href="editTable.php">Edit Poll</a></li>
			<li class="active"><a href="reviewTable.php">Review Poll</a></li>
			<li><a href="../add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<!-- Last change here from 'require' -> 'require_once' -->
<?php require_once "loadEditTable.php"; ?>
<table class="pure-table pure-table-bordered" align="center">
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
	<tbody>
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
							<form method='post' id='reviewForm' action='../event/results.php'>
								<button class='button-review pure-button' name='poll_id' value='$poll_id'>Results</button>
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
