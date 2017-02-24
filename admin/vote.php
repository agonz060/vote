<?php 
    session_start();
    
    require_once "event/sessionHandling.php";
    require_once "event/redirections.php";
    
    if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
/* Session verification ends here */ 
?>
<!-- Check php comment for functionality -->
<?php #This sets global variables 
	$pollId = "";
	$profIds = array();
	$profCmts = array();

?>
<!-- PHP that processes user input begins here -->
<?php
    require_once 'event/connDB.php';
    date_default_timezone_set('America/Los_Angeles');
    
	# Set voting variables
	$day = $month = "";
	$title = $description = $actDate = $deactDate = $profName = $effDate = "";
	$pollType = $dept = $tmp_dateDeact = $tmp_dateAct = $tmp_effDate = "";
	$errTitle = $errEffDate= $errActDate = $errDeactDate = $errProfName = "";
	$validTitle =  $validActDate = $validDeactDate = false; 
	
	# User input processing begins here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
        //print_r($_POST);
		# Check for pollId
		# If pollId is set then it is an edit
		# Initialize all values if edit
		if(isset($_POST["poll_id"])) {
			$pollId = cleanInput($_POST["poll_id"]);
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
				$errActDate = "* Invalid Activation Date";
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
					$errDeactDate = "* Deactivation Date cannot come before Activation Date.";
					$dateDeact = "";
					$validDeactDate = false;
				}		
				$validDeactDate = true; 
			}
			else {
				$errDeactDate = "* Invalid Deactivation Date";
			}
		}

		# Check for valid Effective Date
		if(empty($_POST["effDate"])) {
			$errEffDate = "* Invalid Effective Date";
		} else {
			$effDate = $_POST["effDate"];
			$tmp_effDate = new DateTime($effDate);
			list($year, $month, $day) = explode("-",$effDate);
			if(checkdate($month,$day,$year)) {
				$validEffDate = true;
			} else {
				$errEffDate = "* Invalid Effective Date";
			}
		}
		
		# Process comment for selected professors
		if(!empty($_POST["description"])) {
			$description = cleanInput($_POST["description"]);
		}

        # Check for professor's name
        if(!empty($_POST["profName"])) {
            $profName = cleanInput($_POST["profName"]);
        } else { $errProfName = "* Name required"; }

        # Check for poll type
        if(!empty($_POST["pollType"])) {
        	$pollType = cleanInput($_POST["pollType"]);
        } 

        # Check for department
        if(!empty($_POST["dept"])) {
        	$dept = cleanInput($_POST["dept"]);
        } 
	} // End of SERVER POST capture

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
.error {color: #FF0000;}
.form-inline .form-group {
	margin-left: 0;	
	margin-right: 0;
}
</style>
</head>
<body>
<!-- HTML for page Voting page elements begins here --> 
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li class="active"><a href="vote.php">Create Poll</a></li>
			<li><a href="edit/editTable.php">Edit Poll</a></li>
			<li><a href="edit/reviewTable.php">Review Poll</a></li>
			<li><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<!-- Form input allows the user to cancel current form data, save the data, -->
<!-- or process the data; User information remains in input area incase form -->
<!-- data needs to be modified before being submitted -->
<div class="container well">
<form class="form-horizontal" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
	<div id="votingInfo"></div>
	<h2 class="form-heading">Create Poll</h2>
	<!-- Title of current vote -->
	<div class="form-group">
		<label for="title">Poll Title</label>
		<input type="text" class="form-control" name="title" id="title" placeholder="Poll Title" value="<?php if(isset($_POST['title'])) { echo htmlentities ($_POST['title']); } ?>" >
		<span id="titleErr" class="help-block error"><?php echo "$errTitle";?></span>
	</div>

	<!-- Descriptions/Comments about vote -->
	<div class="form-group">
		<label for="description">Description</label>
		<textarea class="form-control" id="description" name="description" maxlength="300" rows="5" cols="70"><?php
		if(!empty($_POST["description"])) {
			echo htmlentities($_POST["description"]);
		} 
		# echo $description;
	 ?></textarea>
	</div>
	<!-- Date vote becomes active/inactive -->
	<div class="form-group">
		<label for="dateActive">Poll Start Date</label>
		<input type="text" class="form-control" name="dateActive" id="dateActive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateActive'])) { echo htmlentities ($_POST['dateActive']); } ?>" >
		<span id="dateActErr" class="help-block error"><?php echo "$errActDate";?></span>
	</div>

	<div class="form-group">
		<label for="dateDeactive">Poll End Date</label>
		<input type="text" class="form-control" name="dateDeactive" id="dateDeactive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateDeactive'])) { echo htmlentities ($_POST['dateDeactive']); } ?>" >
		<span id="dateDeactErr" class="help-block error"><?php echo "$errDeactDate";?></span>
	</div>

	<div class="form-group">
		<label for="effDate">Effective Date</label>
		<input type="text" class="form-control" name="effDate" id="effDate" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['effDate'])) { echo htmlentities ($_POST['effDate']); } ?>" >
		<span id="effDateErr" class="help-block error"><?php echo "$errEffDate";?></span>
	</div>
	<div class="form-group">
		<label for="profName">Professor's Name</label>
		<input type="text" class="form-control" name="profName" id="profName" placeholder="Professor's Full Name" value="<?php if(isset($_POST['profName'])) { echo htmlentities ($_POST['profName']); } ?>" >
		<span id="profNameErr" class="help-block error"><?php echo "$errProfName";?></span>
	</div>
	<div class="form-group">
		<label for="profTitle">Professor Title</label>
		<select class="form-control" id="profTitle" name="profTitle">
    			<option>Full Professor</option>
    			<option>Associate Professor</option>
    			<option>Assistant Professor</option>
		</select>
	</div>

	<div class="form-group">
		<label for="pollType">Poll Type</label>
		<select class="form-control" name="pollType" id="pollType">
    			<option value="Promotion">Promotion</option>
    			<option value="Merrit">Merrit</option>
    			<option value="Reappointment">Reappointment</option>
    			<option value="Fifth Year Review">Fifth Year Review</option>
    			<option value="Fifth Year Appraisal">Fifth Year Appraisal</option>
		</select>
	</div>
	<div id="actions" class="form-group">
		<div class="form-inline">
		
		<div class="form-group">
			<label for="fromLevel">From</label>
			<select class="form-control" name="fromLevel">
				<option value="1">I</option>
				<option value="2">II</option>
				<option value="3">III</option>
				<option value="4">IV</option>
				<option value="5">V</option>
				<option value="6">VI</option>
				<option value="7">VII</option>
			</select>
		</div>

		<div class="form-group">
			<label for="toLevel">To</label>
			<select class="form-control" name="toLevel">
				<option value="1">I</option>
				<option value="2">II</option>
				<option value="3">III</option>
				<option value="4">IV</option>
				<option value="5">V</option>
				<option value="6">VI</option>
				<option value="7">VII</option>
			</select>
		</div>
		<div class="form-group">
			<label for="accelerated">Accelerated</label>
			<select class="form-control" name="accelerated">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select>
		</div>
	
		<div class="form-group">
			<button type="button" class="btn btn-success addAction"><span class="glyphicon glyphicon-plus"></span></button>
		</div>
		</div>
		
	</div>
		
	<div class="form-group">
		<label for="dept">Department</label>
		<select class="form-control" id="dept" name="dept">
    			<option>Computer Engineering</option>
    			<option>Electrical Engineering</option>
    			<option>Mechanical Engineering</option>
		</select>
	</div>
	<div class="form-group">
		<label for="emailCmt">Email</label>
		<textarea class="form-control" maxlength="500" name="emailCmt" id="emailCmt" rows="5" cols="70"></textarea>
	</div>
	<!-- Begin professor selection -->
	<label for="addProfessors">Add Professors to Poll</label>
	<div class="form-inline" name="addProfessors">
	<div class="form-group">
		<!-- Selection displays the names and titles of professors -->
		<select multiple class="form-control" id="profSel" size="20" ondblclick="addToSelected()">
		<?php
		$selectCmd = "SELECT user_id, fName, lName, title FROM Users WHERE title !='Administrator'";

		$result = $conn->query($selectCmd);

			# Variables used to store a professors information
			$firstName = $lastName = $title = $email = "";
			$fullName = $selection = "";
			
			# Store results from database for displaying 
			while($row = $result->fetch_assoc()) {
				$profId = $row["user_id"];
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
	</div>
	<div id="pollActions" name="pollActions" class="form-group">	
		<!-- Selection displays list of double clicked (selected) professors -->
		<select multiple class="form-control"  id="selected" size="20" >
		<?php 
			if(!empty($pollId)) {
				// Select the first name and last name of all professors participating in the current poll
				$selectCmd = "SELECT Users.fName, Users.lName FROM Voters INNER JOIN Users";
				$selectCmd = $selectCmd." ON Users.user_id=Voters.user_id WHERE Voters.poll_id=$pollId";
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
				$selectCmd = "SELECT user_id, comment FROM Voters WHERE Voters.poll_id=$pollId";
				$result = $conn->query($selectCmd);
				while($row = $result->fetch_assoc()) {
					$id = $row["user_id"];
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
			
	</div>
	</div>
	<div class="form-group">
		<a href="home.php"><button type="button" class="btn btn-danger" value="Cancel">Cancel</button></a>
		<button type="button" class="btn btn-primary" value="Save" onclick="pollAction(0)">Save</button>
		<button type="button" class="btn btn-success" value="Start" onclick="pollAction(1)">Start</button>
	</div>
	<!--
	<div class="form-group">
		<label for="profCmtBox">Comments</label>
		<textarea class="form-control" id="profCmtBox" name="profCmtBox"></textarea> 
		<input type="button" id="remove" name="remove" value="Remove" onclick="removeFromSelected()">
		<input type="button" id="saveCmt" name="saveCmt" value="Save" onclick="saveProfCmt()">  
	</div>
	-->
</form>
</div>
</body>
<!-- Javascript/Json/Jquery begins here -->		
<!-- Load javascript sources -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>	
<script>
$(document).ready(function() {

    setupPoll();
    setTimeout(timeOutReload,1200000); // 1200000ms = 20 mins

    $('#selected').change(function() {
            var selected = $(this).find('option:selected');
            var profName = selected.val();
			//var cmt = <?php echo json_encode($profCmts) ?>;
			var cmt = $('input:hidden[id="'+profName+'"]').val();
			$('#profCmtBox').val(cmt);
	});
        
	function timeOutReload() {
        location.reload(true);
    };

	function setupPoll() {
		storePollId();	
		loadProfCmts();
		setPollType();
		setDept();
	};
	
	function setDept() {
		var dept = <?php if($dept) { echo json_encode($dept); } else { echo 0; } ?>;
		if(dept) { $('#dept').val(dept); }
	};

	function setPollType() {
		var type = <?php if($pollType) { echo json_encode($pollType); } else { echo 0; } ?>;
		if(type) { $('#pollType').val(type); } 
	};

	function storePollId() {
		var id = <?php if($pollId) { echo json_encode($pollId); } else { echo -1; } ?>;
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
    var profName = $("#profSel").val();
	
	// Check to the 'selected' list so that duplicates are not add to the list
	var selectedProfs = document.getElementById("selected");
	var professors = selectedProfs.options;
	var profFound = false;
	for(var x=0; x < professors.length; x++) {
		if(professors[x].value == profName) {
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
}; // End of addToSelected()

function getActions() {
	var fromLevel = toLevel = accelerated = ret = [];
	$('#actions select[name="toLevel"]').each(function(index) {
		ret[index] = {
			toLevel : $(this).val(),
			fromLevel : $('#actions select[name="fromLevel"]').eq(index).val(),
			accelerated : $('#actions select[name="accelerated"]').eq(index).val()
		};
	});	
	return ret;
}

function pollAction(sendFlag) {
	//alert("in savePoll")
	var actions = [];
	// Grab all input field data
	var title = $('#title').val();
	var description = $('#description').val();
	var dateActive = $('#dateActive').val();
	var dateDeactive = $('#dateDeactive').val();
    	var profName = $('#profName').val();
	var effDate = $('#effDate').val();
	var pollType = $('#pollType option:selected').text();	
	var profTitle = $('#profTitle option:selected').text();
	var dept = $('#dept option:selected').text();	
	var emailCmt = $('#emailCmt').val();
	if(pollType == 'Promotion') { actions = getActions(); }
		
	var validTitle = validDescr = validAct = validDeact = validDateEff = 0;
	var validPollType = validDept = validData = 0;
    	var validTitle = validDescr = validAct = validDeact = validEffDate = 0;
    	var validName = validPollType = validDept = validData = 0;
    
    if(!title || title.length == 0) {
        $("#titleErr").text("* Title required");
    } else { validTitle = 1; }

    if(!dateActive || dateActive.length == 0) {
        $("#dateActErr").text("* Date required");
    } else { validActDate = 1; }

    if(!dateDeactive || dateDeactive.length == 0) {
        $("#dateDeactErr").text("* Date required");
    } else { validDeactDate = 1; }

    if(!effDate || effDate.length == 0) {
        $("#effDateErr").text("* Date required");
    } else { validEffDate = 1; } 

    if(!pollType || pollType.length == 0) {
        $("#pollTypeErr").text("* Poll type required");
    } else { validPollType = 1; } 

    if(!dept || dept.length == 0) {
        $("#deptErr").text("* Department required");
    } else { validDept = 1; } 
   
    if(!profName || profName.length == 0) {
        $('#profNameErr').text('* Name required');
    } else { validName = 1; }

    if(validTitle && validActDate && validDeactDate && validEffDate && validPollType && validDept) {
        if(validName) {
            validData = 1;
        }
    }

        // Create pollData object
    var _pollData = { title: title,
                    	descr: description,
                        actDate: dateActive,
                        deactDate: dateDeactive,
			effDate: effDate,
			profTitle: profTitle,
                        pollType: pollType,
			dept: dept,
			emailCmt: emailCmt,
                        name: profName, 
                    	sendFlag: sendFlag };
        

    var _votingInfo = { };
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
	//console.log(_votingInfo );
	//console.log( _pollData );

    // Store data in database if all required information is valid
    if(validData == 1) {
        var _reason = prompt("Why did you create/edit this page?"); 
        if(_reason) {
            $.post("event/savePoll.php", { pollData: _pollData, votingInfo: _votingInfo, actions: actions, reason: _reason }
                    , function(data) { 
                		if(data) { 
                			alert(data); 
                		} else { // Output appropiate message
                			if(!sendFlag) { alert("Poll saved!"); } 
                			else { alert("Poll notification(s) sent"); } 
                			
                			window.location.href = "home.php"; 
                			} // End of else 
                		}) // End of function()
                    .fail(function() {
                            alert("vote.php: error posting to savePoll.php");
                }); // End of $.post()
        } // End of if(_reason)
    } // End of if(validData)
}; // End of pollAction()

function createProfCmtField(profName,cmt) {
	// Create hidden input to store a voting professors's name and comment 
 	var prof = document.createElement("input");
	prof.setAttribute("type", "hidden");
	prof.setAttribute("id", profName);
	prof.setAttribute("value", cmt);

	document.getElementById("votingInfo").appendChild(prof);	 
};
/*
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
 */
</script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function () {
	$( "#dateActive" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#dateDeactive" ).datepicker( {dateFormat: 'yy-mm-dd' });
	$( "#effDate" ).datepicker( {dateFormat: 'yy-mm-dd' });
});
function clonePollAction() {
	//form-inline
	var count = $("[name='toLevel']").size();
	//console.log(count);
	if(count < 3) {	
		var par = $(this).parent().parent();
		var clone = par.clone(true,true);
		$(this).removeClass("btn-success addAction").addClass("btn-danger delAction");
		$(this).children().eq(0).removeClass("glyphicon-plus").addClass("glyphicon-minus");
		$(this).off('click',clonePollAction);
		$(this).on('click',removePollAction);
		par.parent().append(clone);
	}
};
function removePollAction() {
	$(this).parent().parent().remove();
};
$(".addAction").on('click',clonePollAction);
$(".delAction").on('click',removePollAction);
$("#pollType").change(function() {
	if($(this).val() === "Promotion") { 
		$("#actions").show();
	}
	else {
		$("#actions").hide();
	}
});
//Remove selected professors from hidden input(votingInfo)
$("#selected").dblclick(function() {
	var name = $("#selected option:selected").val();
	document.getElementById(name).remove();	
	$("#selected option:selected").remove();
});
</script>
<!-- End of javascript/jquery -->
