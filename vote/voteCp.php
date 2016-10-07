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
	#if($result->num_rows > 0) {
	#	echo "Results available";
	#	$resultsAvailable = true;
	#} else {
	#	echo "0 results<br>";
	#}

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
	<select id="profSel" size="20" ondblclick="addToSelected()">
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
	<select id="selected" size="20" ondblclick="removeFromSelected()">
	</select>
	</td>
</tr>
</table>

<script type="text/javascript">
function addToSelected() {
	// Get the name of the professor that was doubled clicked
 	var index = document.getElementById("profSel").selectedIndex;
	var profName = document.getElementsByTagName("option")[index].value;
	
	// Check to the 'selected' list so that duplicates are not add to the list
	var selectedProfs = document.getElementById("selected");
	var professors = selectedProfs.options;
	
	var profFound = false;
	for(var x=0; x < professors.length; x++) {
		if(professors[x].value == profName)
		{
			profFound = true;
		}
	}

	// Add the professor to the 'selected' list 	
	if(!profFound) {
		// Places the selected list in a variable so that options can be added to the list 
		var option = document.createElement("option");
		option.text = profName;
		option.value = profName;
		selectedProfs.add(option);
	} else {
		alert(profName+" is already selected to participate.");
	}
};

// Remove the selected professor from the list
function removeFromSelected() {
	var selected = document.getElementById("selected");
	selected.remove(selected.selectedIndex);			
};
</script>

<p>
<input type="button" onclick=t"alert('Cancel')" value="Cancel">
<input type="button" onclick="alert('Save')" value="Save">
<input type="button" onclick="alert('Start')" value="Start">
</p>

</body>
</html>
