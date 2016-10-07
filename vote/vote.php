<html>
<body>

<!-- PHP begins -->
<?php
	# Setup variables necessary to connect to database
	$serverName = "localhost";
	$userName = "root";
	$pwd = "on^yp6Ai";
	$db = "Voting";	
	$resultsAvailable = false;

	# Establish connection with db (using setting from variables above)
	$conn = new mysqli($serverName, $userName, $pwd, $db);

	# Check connection to db
	if($conn->connect_error) {
		echo "Connection error: " . $conn->connect_error . "<br>";
	}
	
	# Select first and last name of professor as well as the professor's title
	$selectCmd = "SELECT FirstName, LastName, Title FROM Professors";

	# Execute command
	$result = $conn->query($selectCmd);

	# Output results from command
	if($result->num_rows > 0) {
		echo "Results available";
		$resultsAvailable = true;
	} else {
		echo "0 results<br>";
	}

	# Close connection to db
	#$conn->close();

?>


<!-- Display title of page -->
<h1 align="center">Voting</h1>
<hr> 

<!-- Title of current vote -->
<p>
Title: <input type="text" name="title"><br>
</p>

<!-- Descriptions/Comments about vote -->
<p>
Description/Comments: <br><textarea name="voteDescription" rows="5" cols="70"></textarea></br>
</p>

<!-- Date vote becomes active/inactive -->
<p>
Date Active(MM/DD): <input size="2" name="monthActive"> / <input size="2" name="dayActive"><br><br>
Date Deactive(MM/DD): <input size=2" type="number" name="monthDeactive"> /
<input size="2" type="number" name="dayDeactive"><br>
</p>

<table style="width:30%">
<tr>
	<td align="center">Professors</td>
	<td>Participating Professors</td>
</tr>

<tr>
	<td>
	<!-- Selection displays the names and titles of professors -->
	<select id="profSel" size="20" ondblclick="dbClickFct()">
	<?php 
		# Variables used to store a professors information
		$firstName = $lastName = $title = "";
		$fullName = $selection = "";
	
		# Store results from database into variables for displaying 
		while($row = $result->fetch_assoc()) {
			$firstName = $row["FirstName"];
			$lastName = $row["LastName"];
			$title = $row["Title"];
			$fullName = $firstName." ".$lastName;
			$selection = " ".$fullName." : ".$title." ";

			echo "<option value='$fullName'>".$selection."</option>";	
		}
	?>
	</select>
	</td>
	
	<!-- Selection displays list of double clicked (selected) professors -->
	<td>
	<select id="selected" size="20">
	<option>TESTING123 </option>
	</select>
	</td>
</tr>

</table>

<script>
function dbClickFct() {
	# Get the name of the professor that was doubled clicked
	var index = document.getElementById("profSel").selectedIndex;
	var profName = document.getElementsByTagName("option")[index].value	

	var select = document.getElementById("selected");
	alert(
	var option = new Option(profName, profName);

	document.getElementById("selected").appendChild(option);	
}
</script>

</body>
</html>
