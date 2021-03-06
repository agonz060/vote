<html>
<head>
<style>
.error {color: #FF0000;}
</style>


<!-- Check php comment for functionality -->
<?php #This sets global variables 
	$pollId = "";
	$profIds = array();
	$profCmts = array();
?>

<body>
<!-- Connect to database to load professor information -->
<?php require "event/loadProfs.php"; ?> 

<!-- PHP that processes user input begins here -->
<?php
	# Set voting variables
	$day = $month = "";
	$title = $description = $actDate = $deactDate = "";
	$tmp_dateDeact = $tmp_dateAct = "";
	$errTitle = $errActDate = $errDeactDate = "";
	$validTitle =  $validActDate = $validDeactDate = false;
	date_default_timezone_set('America/Los_Angeles'); 
	
	# User input processing begins here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		# Check for pollId
		# If pollId is set then it is an edit
		# Initialize all values if edit
		if(isset($_POST["poll_id"])) {
			$pollId = $_POST["poll_id"];
		}
	
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
			list($year, $month, $day) = explode("-",$dateAct);
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
			list($year,$month,$day) =explode("-",$dateDeact);	
			if(checkdate($month,$day,$year)) {
				if($tmp_dateAct && $tmp_dateDeact < $tmp_dateAct) {
					$errDeactDate = "Deactivation Date cannot come before Activation Date.";
					$dateDeact = "";
					$validDeactDate = false;
				}		
				$validDeactDate = true; 
			}
			else {
				$errDeactDate = "Invalid Deactivation Date";
			}
		}
	 	
		
		# Process comment for selected professors
		if(!empty($_POST["description"])) {
			$description = $_POST["description"];
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
<!-- <p><?php var_dump($_POST); ?></p> -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

<div id="votingInfo"></div>

<!-- Title of current vote -->
<p>
Title: <input type="text" id="title" name="title" value="<?php if(isset($_POST['title'])) { echo htmlentities ($_POST['title']); } ?>"/>
<span class="error"><?php echo "$errTitle";?></span> <br>
</p>

<!-- Descriptions/Comments about vote -->
<p>
Description: <br><textarea id="description" name="description" rows="5" cols="70">
<?php
	if(isset($_POST["description"])) {
		echo htmlentities ($_POST["description"]);
	} 
	# echo $description;
 ?>
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
			$profId = $row["prof_id"];
			$firstName = $row["fName"];
			$lastName = $row["lName"];
			$title = $row["title"];
			$fullName = $firstName." ".$lastName;
			$selection = " ".$fullName." : ".$title." ";
			
			echo "<option  value='$fullName'>".$selection."</option>";	
			
			// Store a mapping of professor names to professor id's 
			// for quicker storage later on
			$profId = array($fullName => $profId);
			$profIds = array_merge($profIds, $profId);
		}
	?>
	</select>
	</td>
	
	<!-- Selection displays list of double clicked (selected) professors -->
	<td>
	<select id="selected" size="20" >
	<?php 
		if(!empty($pollId)) {
			// Select the first name and last name of all professors participating in the current poll
			$selectCmd = "SELECT Professors.fName, Professors.lName FROM Votes INNER JOIN Professors";
			$selectCmd = $selectCmd." ON Professors.prof_id=Votes.prof_id WHERE Votes.poll_id=$pollId";
			$result = $conn->query($selectCmd);
			
			
			// Execute sql command and loop through results	
			while($row = $result->fetch_assoc()) {
				// Store basic professor information
				$name = $row["fName"];
				$name = $name. " ".$row["lName"];
				
				// Store professor comments using professors name as key
				$prof = array($name => "");
				$profCmts = array_merge($profCmts, $prof);

				// Display voting professors name
				echo "<option value='$name'>".$name."</option>";
				
			}
			
			// Select all the professors id's and comments associated with "pollId"
			$selectCmd = "SELECT prof_id, comment FROM Votes WHERE Votes.poll_id=$pollId";
			$result = $conn->query($selectCmd);
			while($row = $result->fetch_assoc()) {
				$id = $row["prof_id"];
				$cmt = $row["comment"];
			
				// Retreive professors name using the professors id	
				// Then store comment associated with professor
				$profName = array_search($id, $profIds); 
				$profCmts[$profName] = $cmt;
			}
			// Close connection to db
			mysqli_close($conn);
		}
	?>
	</select>
	</td>
	
	<td>
	<span class="error">
	<p id="result" name="result"></p>
	</span>
	<textarea id="profCmtBox" name="profCmtBox" rows="3" cols="20"></textarea> 
	<input type="button" id="remove" name="remove" value="Remove" onclick="removeFromSelected()">
	<input type="button" id="saveCmt" name="saveCmt" value="Save" onclick="saveProfCmt()">  	
	</td>
</tr>
</table>

<p>
<a href="admin/index.php "><input type="button" value="Cancel"></a>
<input type="button" value="Save" onclick="savePoll()">
<input type="submit" value="Start">
</p>
</form>
<!-- <p><?php var_dump($profCmts); ?></p> -->

<!-- Javascript/Json/Jquery begins here -->		
<!-- Load javascript sources -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>	
<script>
$(document).ready(function() {
    $('#selected').change(function() {
            var selected = $(this).find('option:selected');
            var profName = selected.val();
			//var cmt = <?php echo json_encode($profCmts) ?>;
			var cmt = $('input:hidden[id="'+profName+'"]').val();
			$('#profCmtBox').val(cmt);
	});
        
	loadProfCmts();
	storePollId();	
	

	function storePollId() {
		var id = <?php echo $pollId ?>;
		var pollId = document.createElement("input");
		
		pollId.setAttribute("type","hidden");
		pollId.setAttribute("id", "pollId");
		pollId.setAttribute("value",id);

		document.getElementById("votingInfo").appendChild(pollId);	
	};

	function loadProfCmts() {
		var cmts = <?php echo json_encode($profCmts) ?>;
		var profs = <?php echo json_encode(array_keys($profCmts)) ?>;
		var profsLen = profs.length;
		var profName = "";

		if(profsLen > 0) {
			for(var x = 0; x < profsLen; ++x) {
				profName = profs[x];
				createProfCmtField(profName,cmts[profName]);	
			}
		}	
	};
	
	function createProfCmtField(profName,cmt) {
		// Create hidden input to store a voting professors's name and comment 
 		var prof = document.createElement("input");
		prof.setAttribute("type", "hidden");
		prof.setAttribute("id", profName);
		prof.setAttribute("value", cmt);

		document.getElementById("votingInfo").appendChild(prof);	 
	};
	
});
</script>


<!-- Double click script that interacts with the list of professors displayed to user -->
<script type="text/javascript">
function addToSelected() {
	// Get the name of the professor that was doubled clicked
 	var index = document.getElementById("profSel").selectedIndex;
	var profName = document.getElementsByTagName("option")[index].value;
	//alert("Prof:" + profName);	
	
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
	// Keep track of selected professors so that comments can be 
	// associated with a professor 	
	if(!profFound) {
		// Places the selected list in a variable so that options can be added to the list 
		var option = document.createElement("option");
		option.text = profName;
		option.value = profName;
		selectedProfs.add(option);

		// Create input field to store any comments for the professor
		createProfCmtField(profName,"");
	} else {
		alert(profName+" is already selected to participate.");
	}
};

function savePoll() {
	//alert("in savePoll")
	// Grab all input field data
	var title = $('#title').val();
	var description = $('#description').val();
	var dateActive = $('#dateActive').val();
	var dateDeactive = $('#dateDeactive').val();
	// For the creation of votingInfo objects
	
	var _pollData = {'':''};
	_pollData["title"] = title;
	_pollData["descr"] = description;
	_pollData["actDate"] = dateActive;
	_pollData["deactDate"] = dateDeactive;

	var _votingInfo = {'':''};
	var id = '';
	var val = '';

	//alert("title: " + _title + " descr: " + _description + " dateAct: " + _dateActive + " Deactive: " + _dateDeactive)
	
	// Iterate through hidden input fields and store the input field into 
	// an associative array for posting  
	$('input:hidden').each(function() {
		// Store all hidden input field data into an votingInfo object
		id = $(this).attr("id");
		val = $(this).val();
		//alert("name: " + $(this).attr("id") + " cmt: " + $(this).val());

		if(id == "pollId") {
			_pollData[id] = val;
		} else {
			_votingInfo[id] = val;
		}	
	});
	
	// Post data
	var reason = prompt("Why did you create/edit this page?"); 
	$.post("event/savePoll.php", { pollData: _pollData, votingInfo: _votingInfo, reason: reason }
		, function(response,status) { alert(response,status);	})
		.fail(function() {
			alert("error");
		});		
};

function createProfCmtField(profName,cmt) {
	// Create hidden input to store a voting professors's name and comment 
 	var prof = document.createElement("input");
	prof.setAttribute("type", "hidden");
	prof.setAttribute("id", profName);
	prof.setAttribute("value", cmt);

	document.getElementById("votingInfo").appendChild(prof);	 
};

// Remove the selected professor from the list
function removeFromSelected() {
	// Select highlighted professor from list for removal
	var selected = document.getElementById("selected");
	var name = selected.value;
	
	// Remove professor from 'Participating Professors' selection
	selected.remove(selected.selectedIndex);

	// Remove professor from participation
	document.getElementById(name).remove();	
};

// Store professors comment in a hidden field so the comment
// can be posted 
function saveProfCmt() {
	var selected = document.getElementById("selected");
	var name = selected.value;

	var cmt = document.getElementById("profCmtBox").value;
	if(name) {
		//alert("name: "+name+" comment: "+ cmt);
		document.getElementById(name).value = cmt;
	}
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
<!-- End of javascript/jquery -->
</head>
</body>
</html>
