<html>
<head>
<style>
.error {color: #FF0000;}
</style>

<!-- Double click script that interacts with the list of professors displayed to user -->
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
</head>

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

<!-- PHP that processes user input begins here -->
<?php 
	# Set voting variables
	$title = $description = $dateAct = $dateDeact = "";
	$errTitle = $errDesc = $errDateAct = $errDateDeact = "";
	$validTitle = $validDateAct = $validDateDeact = false;

	# User input processing begins here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		# Check for title input; error if not provided
		echo "In submission PHP";	
		if(empty($_POST["title"])) {
			echo "In title empty";
			$errTitle = "* Title is required";
		} else {
			$title = cleanInput($title);
			
			if(empty($title)) {
				echo "Title is empty";
			}		
			echo "Title: $title"; 
			$validTitle = true;
		}
		
		# Check for valid date inputs
		if( empty($_POST["monthActive"]) || empty($_POST["dayActive"]) ) {
			$errDateAct = "* Invalid activation date";
		}	
		
		#$description = $_POST['voteDescription'];
		#echo "$description" ;	
		# Check for empty date inputs
		# $emptyMonth
		
		#check that activation and deactivation dates were set
	}

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>

<!-- HTML for page Voting page elements begins here --> 

<!-- Display title of page -->
<h1 align="center">Voting</h1>
<hr> 

<!-- Form input allows the user to cancel current form data, save the data, -->
<!-- or process the data; User information remains in input area incase form -->
<!-- data needs to be modified before being submitted -->
<form id="votingInfo" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

<!-- Title of current vote -->
<p>
Title: <input type="text" name="title" value="<?php if(isset($_POST['title'])) { echo htmlentities ($_POST['title']); } ?>"/>
<span class="error"><?php echo "$errTitle";?></span> <br>
</p>

<!-- Descriptions/Comments about vote -->
<p>
Description/Comments: <br><textarea name="voteDescription" form="votingInfo" rows="5" cols="70"><?php echo "$description"; ?></textarea></br>
</p>

<!-- Date vote becomes active/inactive -->
<p>
Date Active(MM/DD): <input size="2" name="monthActive" value="<?php if(isset($_POST['monthActive'])) {echo htmlentities ($_POST['monthActive']);} ?>"> / <input size="2" name="dayActive" value="<?php if(isset($_POST['dayActive'])) {echo htmlentities ($_POST['dayActive']);} ?>"><br><br>
Date Deactive(MM/DD): <input size=2" name="monthDeactive" value="<?php if(isset($_POST['monthDeactive'])) {echo htmlentities ($_POST['monthDeactive']);} ?>"> /
<input size="2" name="dayDeactive" value="<?php if(isset($_POST['dayDeactive'])) {echo htmlentities ($_POST['dayDeactive']);} ?>"><br>
</p>

<!-- Begin professor selection -->
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

<p>
<a href="/vote/index.php "><input type="button" value="Cancel"></a>
<input type="submit" value="Save">
<input type="submit" value="Start">
</p>
</form>

</body>
</html>
