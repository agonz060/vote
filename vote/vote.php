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

		// Send professor name to php page for storage
		$.post("add.php", {name : profName},
			function(response,status) {
		});
  
	} else {
		alert(profName+" is already selected to participate.");
	}
};

// Remove the selected professor from the list
function removeFromSelected() {
	// Select highlighted professor from list for removal
	var selected = document.getElementById("selected");
	var name = selected.value;
	
	// Remove professor from 'Participating Professors' selection
	selected.remove(selected.selectedIndex);
	
	// Post index to php file so that the appropiate professor can be removed
	// from the list of participating professors
	$.post("remove.php", {name : name}, 
		function(response,status) {
	}); 
};

// Function resets professor comment box and "Comment Saved" message
// when a new participatig professor is selected
function newSelection() {
	document.getElementById("result").innerHTML = "";
	var name = document.getElementById("selected").value;
	
	$.post("getCmt.php", { name : name }, 
		function(response) {
			$("#profCmtBox").html(response);
		}
	);
	
		
}; 

// These functions wait until the page has loaded before executing
$(document).ready(function() {
	// Function stores a comment for a selected professor on button click
	// by passing along the comment and professors name to a saveCmt.php
	$("#saveCmt").click(function(e) {
		var txtCmt = $("#profCmtBox").val();
		var selected = document.getElementById("selected");
		var name = selected.value;
		
		if(txtCmt && name) {	
			$.post("saveCmt.php", {name : name, comment : txtCmt},
				function(response,status) {
					$("#result").html("Comment saved");
			});
		}
	});
});


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

<!-- Check php comment for functionality -->
<?php #This sets global variables 
	$_SESSION["title"] = "";
	$_SESSION["actDate"] = "";
	$_SESSION["deactDate"] = "";
	$_SESSION["voteDescrip"] = "";
	$_SESSION["votingProfs"] = array();
	$_SESSION["profCmts"] = array(); 
?>
</head>

<body>

<!-- Connect to database to load professor information -->
<?php require "loadProfs.php"; ?> 

<!-- PHP that processes user input begins here -->
<?php
	# Set voting variables
	date_default_timezone_set('America/Los_Angeles'); 
	$day = $month = "";
	$title = $description = $actDate = $deactDate = "";
	$tmp_dateDeact = $tmp_dateAct = "";
	$errTitle = $errActDate = $errDeactDate = "";
	$validTitle =  $validActDate = $validDeactDate = false;
	
	# User input processing begins here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		# Check for title input; error if not provided
		if(empty($_POST["title"])) {
			$errTitle = "* Title is required";
		} else {
			$title = cleanInput($_POST["title"]);
			$_SESSION["title"] = $title;
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
				$_SESSION["actDate"] = $dateAct;
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
				$_SESSION["deactDate"] = $dateDeact;
				$validDeactDate = true; 
			}
			else {
				$errDeactDate = "Invalid Deactivation Date";
			}
		}
	 	
		if($tmp_dateDeact < $tmp_dateAct) {
			$errDeactDate = "Deactivation Date cannot come before Activation Date.";
			$_SESSION["deactDate"] = "";
		}		
		
		# Process comment for selected professors
		if(!empty($_POST["voteDescription"])) {
			$_SESSION["voteDescrip"] = $_POST["voteDescription"];
		}
			
		echo "Title: ".$_SESSION["title"]."<br>";
		echo "Descr: ".$_SESSION["voteDescrip"]."<br>";
		echo "ActDate: ".$_SESSION["actDate"]."<br>";
		echo "DeactDate: ".$_SESSION["deactDate"]."<br>";
		echo "Participating: "; print_r($_SESSION["votingProfs"]);
		echo "Prof. Cmts: "; print_r($_SESSION["profCmts"]);
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
Description: <br><textarea id= "voteDescription" name="voteDescription" form="votingInfo" rows="5" cols="70">
<?php echo $_SESSION["voteDescrip"]; ?>
</textarea>
</p>

<!-- Date vote becomes active/inactive -->
<p>Date Active <input type="text" id="dateActive" name="dateActive" value="<?php if(isset($_POST['dateActive'])) {echo htmlentities ($_POST['dateActive']);} ?>" ></p>
<span class ="error"><?php echo "$errActDate";?></span><br>
<p>Date Deactive <input type="text" id="dateDeactive" name="dateDeactive" value="<?php if(isset($_POST['dateActive'])) {echo htmlentities ($_POST['dateDeactive']);} ?>" ></p>
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
		$firstName = $lastName = $title = $email = "";
		$fullName = $selection = "";
		
		# Store results from database for displaying 
		while($row = $result->fetch_assoc()) {
			$firstName = $row["fName"];
			$lastName = $row["lName"];
			$title = $row["title"];
			$fullName = $firstName." ".$lastName;
			$selection = " ".$fullName." : ".$title." ";
			
			echo "<option value='$fullName'>".$selection."</option>";	
		}
	?>
	</select>
	</td>
	
	<!-- Selection displays list of double clicked (selected) professors -->
	<td>
	<select id="selected" size="20" onclick="newSelection()">
	</select>
	</td>

	<td>
	<span class="error">
	<p id="result" name="result"></p>
	</span>
	<textarea id="profCmtBox" name="profCmtBox" rows="3" cols="20"></textarea> 
	<input type="button" id="remove" name="remove" value="Remove" onclick="removeFromSelected()">
	<input type="button" id="saveCmt" name="saveCmt" value="Save">  	
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
