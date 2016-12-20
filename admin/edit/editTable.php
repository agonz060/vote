<html>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
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
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".button-delete").click(function() {
			var confirmation = prompt("Type DELETE if you are sure you want to delete entry");
			if(confirmation == "DELETE") { 
				var poll_id = $(this).val(); 		
				$.post("deleteRow.php", {poll_id : poll_id}, 
				function(response,status) {
					location.reload();
				});
			}
		});
	});
</script>
</head>
<body>
<?php require "loadEditTable.php";
?>

<table class="pure-table pure-table-bordered" align="center">
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th>Date Active</th>
			<th>Date Deactive</th>
			<th>Date Modified</th>
			<th>Edit/Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$selectCmd="Select * from Polls Where deactDate > CURDATE()";
			$result = $conn->query($selectCmd);
			while($row = $result->fetch_assoc()) {
				$poll_id = $row["poll_id"];
				$title = $row["title"];
				$description = $row["description"];
				$dateModified = $row["dateModified"];
				$actDate = $row["actDate"];
				$deactDate = $row["deactDate"];
				$lName=$row["lName"];
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
							<form method='post' id='editForm' action='../event/vote.php'>
								<button class='button-edit pure-button' name='poll_id' value='$poll_id'>Edit</button>
								<input type='hidden' name='title' value='$title'>
								<input type='hidden' name='description' value='$description'>
								<input type='hidden' name='dateActive' value='$actDate'>
								<input type='hidden' name='dateDeactive' value='$deactDate'>
								<input type='hidden' name='lName' value='$lName'>
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
</body>
</html>