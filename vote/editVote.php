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

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function () {
	$( "#dateActive" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#dateDeactive" ).datepicker( {dateFormat: 'yy-mm-dd' });
});
</script>


</head>

<body>

<!-- PHP for server connection begins -->
<?php
	# Setup variables necessary to connect to database
	$serverName = "localhost";
	$userName = "root";
	$pwd = "Computer_Science99";
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

	# Close connection to db
	#$conn->close();

?>

<!-- PHP that processes user input begins here -->
<?php
	date_default_timezone_set('America/Los_Angeles'); 
	# Set voting variables
	$day = $month = "";
	$title = $description = $actDate = $deactDate = "";
	$errTitle = $errActDate = $errDeactDate = "";
	$validTitle = $validMonth = $validDay = $validActDate = $validDeactDate = false;
	
	# User input processing begins here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		# Check for title input; error if not provided
		if(empty($_POST["title"])) {
			$errTitle = "* Title is required";
		} else {
			$title = cleanInput($_POST["title"]);
			$validTitle = true;
		}
		# Check for valid activation date input
		if(empty($_POST["dateActive"])) {
			$errActDate = "* Invalid Activation Date";
		} else {
			$dateAct = $_POST["dateActive"];
			$tmp_dateAct = new DateTime($dateAct);
			list($year, $month, $day) = split('[-]',$dateAct);
			if(checkdate($month,$day,$year)) {
				$validActDate = true;	
			} else {
				$errActDate = "Invalid Activation Date";
			}	
		}	
	 	
	 	# Check for valid deactivation date input
		if(empty($_POST["dateDeactive"])) {
			$errDeactDate = "* Invalid Deactivation Date";  
		} else {
			$dateDeact = $_POST["dateDeactive"];
			$tmp_dateDeact = new DateTime($dateDeact);
			list($year,$month,$day) = split('[-]',$dateDeact);	
			if(checkdate($month,$day,$year)) {
				$validDeactDate = true;	
			}
			else {
				$errDeactDate = "Invalid Deactivation Date";
			}
		}
	 	if($tmp_dateDeact < $tmp_dateAct) {
			$errDeactDate = "Deactivation Date cannot come before Activation Date.";
		}		
		# Process comment for selected professors
		if(!empty($_POST["profComBox"])) {
			$comment = $_POST["profComBox"];
			echo "Comment: $comment <br>";			
		}	
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
<p>Date Active(YYYY-MM-DD) <input type="text" id="dateActive" name="dateActive" value="<?php if(isset($_POST['dateActive'])) {echo htmlentities ($_POST['dateActive']);} ?>" ></p>
<span class ="error"><?php echo "$errActDate";?></span><br>
<p>Date Deactive(YYYY-MM-DD)<input type="text" id="dateDeactive" name="dateDeactive" value="<?php if(isset($_POST['dateActive'])) {echo htmlentities ($_POST['dateDeactive']);} ?>" ></p>
<span class ="error"><?php echo "$errDeactDate";?></span><br>

<!-- Begin professor selection -->
<table style="width:30%">
<tr>
	<td>Professors</td>
	<td>Participating Professors</td>
	<td>Comments</td>
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
	<select id="selected" size="20">
	</select>
	</td>

	<td>
	<form>
	<textarea name="profComBox" rows="20" cols="20"></textarea> 
	<input type="button" value="Remove" onclick="removeFromSelected()">
	<input type="button" value="Save">  	
	</form>
	</td>
</tr>
</table>

<p>
<a href="/vote/index.php "><input type="button" value="Cancel"></a>
<input type="submit" value="Save">
<input type="submit" value="Start">
</p>
</form>
<!-- User input form ends here -->

</body>
</html>