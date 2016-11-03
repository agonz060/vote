<html>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
	.button-edit {
		color: white;
		background: rgb(28,184,65); 
	}
	.button-delete {
		color: white;
		background: rgb(202,60,60);
	}
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".button-delete").click(function() {
			var confirmation = prompt("Type DELETE if you are sure you want to delete entry");
			if(confirmation == "DELETE") { 
				var dateModified = $(this).val(); 		
				$.post("deleteRow.php", {dateModified : dateModified}, 
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
<table class="pure-table pure-table-bordered">
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th>Date Modified</th>
			<th>Edit/Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			while($row = $result->fetch_assoc()) {
				$title = $row["Title"];
				$description = $row["Description"];
				$dateModified = $row["DateModified"];
				echo "<tr>
						<td>
							$title
						</td>
						<td>
							$description
						</td>
						<td>
							$dateModified
						</td>
						<td>
							<form method='post' id='editForm' action='editVote.php'>
							<button href='vote.php' class='button-edit pure-button' name='editRow' value='$row'>Edit</button>
							</form>
							<button class='button-delete pure-button' value='$dateModified'>Delete</button> 	
						</td>			
					</tr>";
			}
		?>
	</tbody>
</table>
</body>
</html>
