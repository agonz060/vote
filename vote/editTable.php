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
			while($row = $result->fetch_assoc()) {
				$poll_id = $row["poll_id"];
				$title = $row["title"];
				$description = $row["description"];
				$dateModified = $row["dateModified"];
				$actDate = $row["actDate"];
				$deactDate = $row["deactDate"];
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
							<form method='post' id='editForm' action='editVote.php'>
							<button href='vote.php' class='button-edit pure-button' name='editRow' value='$poll_id'>Edit</button>
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
