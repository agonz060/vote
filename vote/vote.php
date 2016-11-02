<?php session_start(); ?>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>

<!-- Load javascript sources -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

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
	alert(selected.value);
	selected.remove(selected.selectedIndex);
};

<<<<<<< HEAD
// Function for posting professor's comment to saveCmt.php without refreshing page
$(document).ready(function() {
	$("#saveCmt").click(function(e) {
		var mail = "sucess@asap.net";
		var txtCmt = $("#profCmtBox").val();
		
		$.post("saveCmt.php", {comment : txtCmt, email : mail},
		function(response,status) {
			$("#result").html("Comment saved");
		});
	});
	
	$("#remove").click(function(e) {
				
	});
});
=======
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
>>>>>>> a3f0e493ab66958be516729c6493da30765d03fb

</script>

<<<<<<< HEAD
<!-- Check php comment for functionality -->
<?php #This sets global variables ?>
<?php 	
	$_SESSION["title"] = "";
	$_SESSION["actDate"] = "";
	$_SESSION["deactDate"] = "";
	$_SESSION["voteDescrip"] = "";
	$_SESSION["profEmails"] = array();
	$_SESSION["votingProfs"] = "";
	$_SESSION["profCmts"] = ""; 
?>
=======
<!-- PHP for server connection begins -->
<?php
	# Setup variables necessary to connect to database
	$serverName = "localhost";
	$userName = "root";
	$pwd = "shaking99";
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
>>>>>>> a3f0e493ab66958be516729c6493da30765d03fb

</head>

<body>

<!-- Connect to database to load professor information -->
<?php require "loadProfs.php"; 
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
<<<<<<< HEAD
			$day = cleanInput($_POST["dayActive"]);
			$month = cleanInput($_POST["monthActive"]); 	
				
			$validDay = isValidDate($day);
			$validMonth = isValidDate($month);
			
			if($validDay && $validMonth) {
				$actDate = $month + "/" + $day;	
=======
			$dateAct = $_POST["dateActive"];
			$tmp_dateAct = new DateTime($dateAct);
			list($year, $month, $day) = split('[-]',$dateAct);
			if(checkdate($month,$day,$year)) {
				$validActDate = true;	
>>>>>>> a3f0e493ab66958be516729c6493da30765d03fb
			} else {
				$errActDate = "Invalid Activation Date";
			}	
		}	
	 	
	 	# Check for valid deactivation date input
		if(empty($_POST["dateDeactive"])) {
			$errDeactDate = "* Invalid Deactivation Date";  
		} else {
<<<<<<< HEAD
			$day = cleanInput($_POST["dayDeactive"]);
			$month = cleanInput($_POST["monthDeactive"]); 	
			
			$validDay = isValidDate($day);
			$validMonth = isValidDate($month);

			if($validDay && $validMonth) {
				$deactDate = $month + "/" + $day;
			} else {
				$errDeactDate = getDateErrMsg($validDay,$validMonth);
=======
			$dateDeact = $_POST["dateDeactive"];
			$tmp_dateDeact = new DateTime($dateDeact);
			list($year,$month,$day) = split('[-]',$dateDeact);	
			if(checkdate($month,$day,$year)) {
				$validDeactDate = true;	
			}
			else {
				$errDeactDate = "Invalid Deactivation Date";
>>>>>>> a3f0e493ab66958be516729c6493da30765d03fb
			}
		}
	 	if($tmp_dateDeact < $tmp_dateAct) {
			$errDeactDate = "Deactivation Date cannot come before Activation Date.";
		}		
		# Process comment for selected professors
		if(!empty($_POST["voteDescription"])) {
			$description = $_POST["voteDescription"];
			$_SESSION["voteDescrip"] = $description;
		} else {
			echo "IN ELSE";
		}
			
	}

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>

<!-- Testing -->
<?php echo "Variables <br>"; ?>
<?php echo "Act: $actDate <br> Deact: $deactDate <br>"; ?>
<?php echo "Vote desc: $description <br>"; ?>
<?php echo "Emails:"; print_r($_SESSION["profEmails"]); ?>
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
Description/Comments: <br><textarea id= "voteDescription" name="voteDescription" form="votingInfo" rows="5" cols="70"><?php echo "$description"; ?></textarea></br>
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
		$index = 0;
		$firstName = $lastName = $title = $email = "";
		$fullName = $selection = $profEmails = "";
		
		# Store results from database for displaying 
		while($row = $result->fetch_assoc()) {
			$firstName = $row["FirstName"];
			$lastName = $row["LastName"];
			$title = $row["Title"];
			$email = array($row["Email"]);
			$fullName = $firstName." ".$lastName;
			$selection = " ".$fullName." : ".$title." ";
				
			$_SESSION["profEmails"] = array_merge($_SESSION["profEmails"],$email);
			
			echo "<option name='$index' value='$fullName'>".$selection."</option>";	
			$index += 1;
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
<<<<<<< HEAD
	<span class="error">
	<p id="result" name="result"></p>
	</span>
	<form id="profCmt" name="profCmt">
	<textarea id="profCmtBox" name="profCmtBox" rows="3" cols="20"></textarea> 
	<input type="button" id="remove" name="remove" value="Remove" onclick="removeFromSelected()">
	<input type="button" id="saveCmt" name="saveCmt" value="Save">  	
=======
	<form>
	<textarea name="profComBox" rows="20" cols="20"></textarea> 
	<input type="button" value="Remove" onclick="removeFromSelected()">
	<input type="button" value="Save">  	
>>>>>>> a3f0e493ab66958be516729c6493da30765d03fb
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

<!-- Testing -->
<?php echo "Variables <br>"; ?>
<?php echo "Act: $actDate <br> Deact: $deactDate <br>"; ?>
<?php echo "Vote desc: $description <br>"; ?>
<?php echo "Emails:"; print_r($_SESSION["profEmails"]); ?>


</body>
</html>
