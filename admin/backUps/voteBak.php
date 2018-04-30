<?php 
    session_start();
    
    require_once 'includes/sessionHandling.php';
    require_once 'includes/redirections.php';
    require_once 'includes/connDB.php';
    
    /*if(!isAdmin()) {
        signOut();
    } elseif(idleLimitReached()) {
        signOut();
    } */
	
/* Session verification ends here */ 

	$pollId = "";
	$profIds = array();
	$profCmts = array();

    date_default_timezone_set('America/Los_Angeles');
    
	# Set voting variables
	$day = $month = "";
	$title = $description = $actDate = $deactDate = $profName = $effDate = "";
	$pollType = $dept = $tmp_dateDeact = $tmp_dateAct = $tmp_effDate = "";
	$errTitle = $errEffDate= $errActDate = $errDeactDate = $errProfName = "";
 	$pollTypeText = "";
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
		if(!empty($_POST["title"])) {
			$title = cleanInput($_POST["title"]);
			$validTitle = true;
		}

		# Check for valid activation date input
		if(!empty($_POST["dateActive"])) {
			$dateAct = $_POST["dateActive"];
			$validActDate = true;
		}	
	 	
	 	# Check for valid deactivation date input
		if(!empty($_POST["dateDeactive"])) {
			$dateDeact = $_POST["dateDeactive"];
			$validDeactDate = true; 
		}

		# Check for valid Effective Date
		if(!empty($_POST["effDate"])) {
			$effDate = $_POST["effDate"];
			$validEffDate = true;
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
<title>Create Poll</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
.error {color: #FF0000;}
.form-inline .actionGroup {
	margin-left: 0;	
	margin-right: 0;
}
.hideOption {
	display: none;
}
</style>
</head>
<body>
<!-- <div>To Do: Update savePoll.php to store the new data in the form</div> -->
<!-- HTML for page Voting page elements begins here --> 
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li class="active"><a href="vote.php">Create Poll</a></li>
			<li><a href="edit.php">Edit Poll</a></li>
			<li><a href="review.php">Review Poll</a></li>
			<li><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<!-- Form input allows the user to cancel current form data, save the data, -->
<!-- or process the data; User information remains in input area incase form -->
<!-- data needs to be modified before being submitted -->
<div class="container well">
	<form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<div id="votingInfo"></div>
		<h2 class="form-heading">Create Poll</h2>
		<!-- Title of current vote -->
		<div class="form-group">
			<label for="title">Poll Title</label>
			<input type="text" class="form-control" name="title" id="title" placeholder="Poll Title" maxlength="30" value="<?php if(isset($_POST['title'])) { echo htmlentities ($_POST['title']); } ?>" >
			<span id="titleErr" class="help-block error"></span>
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
			<span id="dateActErr" class="help-block error"></span>
		</div>

		<div class="form-group">
			<label for="dateDeactive">Poll End Date</label>
			<input type="text" class="form-control" name="dateDeactive" id="dateDeactive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateDeactive'])) { echo htmlentities ($_POST['dateDeactive']); } ?>" >
			<span id="dateDeactErr" class="help-block error"></span>
		</div>

		<div class="form-group">
			<label for="effDate">Effective Date</label>
			<input type="text" class="form-control" name="effDate" id="effDate" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['effDate'])) { echo htmlentities ($_POST['effDate']); } ?>" >
			<span id="effDateErr" class="help-block error"></span>
		</div>
		<div class="form-group">
			<label for="dept">Department</label>
			<select class="form-control" id="dept" name="dept">
					<option>Chemical and Enviromental Engineering</option>
	    			<option>Computer Engineering</option>
	    			<option>Electrical Engineering</option>
	    			<option>Mechanical Engineering</option>
	    			<option>Bioengineering</option>
	    			<option>CE-CERT</option>
			</select>
		</div>
		<div class="form-group" id="pollNoticesDiv">
			<label for="pollNotices">Notice</label>
			<select class="form-control" id="pollNotices" name="pollNotices">
				<option value="CEE">CEE</option>
				<option value="ECE">ECE</option>
				<option value="BOTH">ECE &amp; CEE</option>
			</select>
		</div>
		<div class="form-group" id="noticeTextDiv">
			<label>Notice Text</label>
			<div id="CEENotice" name="CEENotice">Comments may be submitted to the chair prior to the department meeting if the faculty member will not be able to attend the meeting and would like the comments brought up at the meeting for discussion.</div>
			<div class="hideOption" id="ECENotice" name="ECENotice">Anonymous or absentee comments will be raised at the meeting at the Chair's discretion. This is in addition to the above statement i.e. Note: Comments may be submitted.... :)</div>
		</div>
		<div class="form-group">
			<label for="profName">Professor's Name</label>
			<input type="text" class="form-control" name="profName" id="profName" placeholder="Professor's Name" value="<?php if(isset($_POST['profName'])) { echo htmlentities ($_POST['profName']); } ?>" >
			<span id="profNameErr" class="help-block error"></span>
		</div>
		<div class="form-group">
			<label for="pollType">Poll Type</label>
			<select class="form-control" name="pollType" id="pollType">
       			<option value="Fifth Year Review">Fifth Year Review</option>
    			<option value="Fifth Year Appraisal">Fifth Year Appraisal</option>
    			<option value="Merit">Merit</option>
				<option value="Promotion">Promotion</option>
				<option value="Reappointment">Reappointment</option>
				<option value="Other">Other</option>
			</select>
		</div>
		<div id="otherPollTypeDiv" class="form-group">
			<label for="otherPollTypeInput">Please enter poll type</label>
			<input class="form-control" id="otherPollTypeInput" name="otherPollTypeInput" type="text" maxlength="100" placeholder="Poll type"
				value="<?php if(isset($_POST['otherPollTypeInput'])) { echo htmlentities ($_POST['otherPollTypeInput']); } ?>" >
				<span id="otherPollTypeError" class="help-block error"></span>
		</div>
		<div id="profTitleDiv" class="form-group">
			<label for="profTitle">Professor Title</label>
			<select class="form-control" id="profTitle" name="profTitle">
				<option value="Assistant Professor">Assistant Professor</option>
				<option value="Associate Professor">Associate Professor</option>
				<option value="Full Professor">Full Professor</option>
			</select>
		</div>
		<div id="actions" class="form-group">
			<div class="form-inline">
				<div class="form-group actionGroup"> 
					<label for="fromTitle">From</label>
					<input class="form-control" name="fromTitle" type="text" maxlength="100" placeholder="Title">
					<label for="fromStep" class="sr-only">From title</label>
					<input class="form-control" name="fromStep" type="text" maxlength="3" placeholder="Step">
					<span name="actionError" class="help-block error"></span>
				</div>
			</div>
			<div class="form-inline">
				<div class="form-group actionGroup">
					<label for="toTitle">To&nbsp&nbsp&nbsp&nbsp&nbsp</label>
					<input class="form-control" name="toTitle" type="text" maxlength="100" placeholder="Title">
					<label for="toStep" class="sr-only">To step</label>
					<input class="form-control" name="toStep" type="text" maxlength="3" placeholder="Step">
				</div>
				<div class="form-group actionGroup">	
					<label for="accelerated">Accelerated</label>
					<select class="form-control" name="accelerated">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
				<div class="form-group actionGroup">
					<button id="actionButton" type="button" class="btn btn-success addAction"><span id="actionIcon" class="glyphicon glyphicon-plus"></span></button>
				</div>
			</div>
		</div>
		<div class="form-group" id="votingOptionsDiv">
			<label for="voteOptions">Voting Options</label>
			<select class="form-control" id="voteOptions" name="voteOptions">
				<option value="1">In Favor, Opposed, Abstain</option>
				<option value="2">Satisfactory, Unsatisfactory, Abstain</option>
				<option value="3">Satisfactory, Satisfactory w/ Qualifications, Unsatisfactory, Abstain</option>
			</select>
		</div>	
		<!-- Begin professor selection -->
		<label for="addProfessors">Add Professors to Poll</label>
		<div class="form-inline" name="addProfessors">
			<div class="form-group">
				<!-- Selection displays the names and titles of professors -->
				<select multiple class="form-control" id="profSel" size="20" ondblclick="addToSelected()">
				<?php
				$selectCmd = "SELECT user_id, fName, lName, title FROM Users WHERE title !='Administrator' ORDER BY fName ASC";

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
						} // End of while looop
						mysqli_close($conn);
					} // End if(!empty)
				?>
				</select>		
			</div>
		</div> <br />
		<div class="form-inline">
			<div class="form-group">
				<label for="assistantForm">Assistant Form</label><br />
				<select class="form-control" id="assistantForm" name="assistantForm">
	       			<option value="1">Regular Vote</option>
	    			<option value="2">Advisory Vote</option>
	    			<option value="3">Confidential Evaluation</option>
				</select>
			</div>
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			<div class="form-group">
				<label for="associateForm">Associate Form</label><br />
				<select class="form-control" id="associateForm" name="associateForm">
	       			<option value="1">Regular Vote</option>
	    			<option value="2">Advisory Vote</option>
	    			<option value="3">Confidential Evaluation</option>
				</select>
			</div>
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			<div class="form-group">
				<label for="fullForm">Full Professor Form</label><br />
				<select class="form-control" id="fullForm" name="fullForm">
	       			<option value="1">Regular Vote</option>
	    			<option value="2">Advisory Vote</option>
	    			<option value="3">Confidential Evaluation</option>
				</select>
			</div>
		</div><br />
		<div class="form-group">
			<a href="home.php"><button type="button" class="btn btn-danger" value="Cancel">Cancel</button></a>
			&nbsp
			<button type="button" class="btn btn-primary" value="Save" onclick="pollAction(0)">Save</button>
			&nbsp
			<button type="button" class="btn btn-success" value="Start" onclick="pollAction(1)">Start</button>
		 	<button type="button" value="Testing" onclick="testing()">Testing</button>
			<span id="formErrorMessage" class="help-block error"></span>
		</div>
	</form>
</div>
<!-- Javascript/Json/Jquery begins here -->		
<!-- Load javascript sources -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>	
<script>
$(document).ready(function() {
	// Populate form fields if user is editing a poll
    setupPoll();
	// Hide input unless selected 
	$('#otherPollTypeDiv').hide();
	$('#actions').hide();
	$('#votingOptionsDiv').hide();
	// Timeout for auto reload of page for session check
    setTimeout(timeOutReload,1200000); // 1200000ms = 20 mins
		
}); // End .ready()

	function testing() {
		var asstForm = $('#assistantForm').val();
		var assocForm = $('#associateForm').val();
		var fullForm = $('#fullForm').val();
		console.log(asstForm);
		console.log(assocForm);
		console.log(fullForm);
	};

	function timeOutReload() {
        	location.reload(true);
    	}; 

	function setupPoll() {
		storePollId();	
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
	function createProfField(profName) {
		// Create hidden input to store a voting professors's name and comment 
 		var prof = document.createElement("input");
		prof.setAttribute("type", "hidden");
		prof.setAttribute("id", profName);
		document.getElementById("votingInfo").appendChild(prof);	 
	};

</script>
<!-- Double click script that interacts with the list of professors displayed to user -->
<script type="text/javascript">
function addToSelected() {
	// Get the name of the professor that was doubled clicked
    var profName = $("#profSel").val();
    //console.log(profName);
    if(profName != null) {
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
		if(profFound) {
			alert(profName+" is already selected to participate.");
		} else {
			// Places the selected list in a variable so that options can be added to the list 
			var option = document.createElement("option");
			option.text = profName;
			option.value = profName;
			selectedProfs.add(option);
			// Create input field to store any comments for the professor
			createProfField(profName);
		}
	} // End of if
}; // End of addToSelected()

function getActions() {
	var fromStep = fromLevel = toStep = toLevel = accelerated = actions = [];
	$('[name="fromTitle"]').each(function(index) {
		actions[index] = {
			fromTitle : $('[name="fromTitle"]').eq(index).val(),
			fromStep : $('[name="fromStep"]').eq(index).val(),
			toTitle: $('[name="toTitle"]').eq(index).val(),
			toStep: $('[name="toStep"]').eq(index).val(),
			accelerated : $('#actions select[name="accelerated"]').eq(index).val()
		};
	});	
	return actions;
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
	var pollNotice = $('#pollNotices').val();
	var voteOptions = $('#voteOptions').val();
	var pollType = $('#pollType option:selected').text();	
	var profTitle = $('#profTitle option:selected').text();
	var dept = $('#dept option:selected').text();
	var assistantForm = $('#assistantForm').val();
	var associateForm = $('#associateForm').val();
	var fullForm = $('#fullForm').val();
	//console.log(dept);
	var otherPollTypeInput = $('#otherPollTypeInput').val();
	// Setting All flags
	var validTitle = validDescr = validAct = validDeact = validDateEff = 0;
	var validPollType = validDept = validData = 0;
    var validTitle = validDescr = validAct = validDeact = validEffDate = 0;
    var validName = validPollType = validData = validOtherPollType = 0;
    var formError = 0;

    if(!title || title.length == 0) {
        $("#titleErr").text("* Title required");
    } else { 
    	$("#titleErr").text("");
    	validTitle = 1; 
    }
    if(!dateActive || dateActive.length == 0) {
        $("#dateActErr").text("* Date required");
    } else { 
    	$("#dateActErr").text("");
    	validActDate = 1; 
    }
    if(!dateDeactive || dateDeactive.length == 0) {
        $("#dateDeactErr").text("* Date required");
    } else { 
    	$("#dateDeactErr").text("");
    	validDeactDate = 1; 
    }
    if(!effDate || effDate.length == 0) {
        $("#effDateErr").text("* Date required");
    } else { 
    	$("#effDateErr").text("");
    	validEffDate = 1; 
    } 
    if(!pollType || pollType.length == 0) {
        $("#pollTypeErr").text("* Poll type required");
    } else { 
    	$("#pollTypeErr").text("");
    	validPollType = 1; 
    } 
    // If dept = other then there is additional input available
    if(!profName || profName.length == 0) {
        $('#profNameErr').text('* Name required');
    } else { 
    	$('#profNameErr').text('');
    	validName = 1; 
    }
	if(pollType == 'Other') {
		if(!otherPollTypeInput || otherPollTypeInput.length == 0) {
			$('#otherPollTypeError').text('* Poll type required');
		} else { 
			$('#otherPollTypeError').text('');
			validOtherPollType = 1; 
		}
	}
	if(pollType == 'Promotion' || pollType == 'Merit' || pollType == 'Other') { 
		actions = getActions(); 
	}
    // Set valid data flag if all form data is present and correct
    if(validTitle && validActDate && validDeactDate && validEffDate && validPollType) {
        if(validName) {
        	if(pollType == 'Other') {
        		if(validOtherPollType) {
        			validData = 1;
        		} else { 
        			formError = 1; 
        		} 
        	} else {
        		validData = 1;
        	}
        } else { formError = 1; }
    } else { formError = 1; }
    // Form does not fit on screen, so let user know about errors at top of form
    if(formError) {
    	$('#formErrorMessage').text("Some form data is incorrect or missing.");
    }

        // Create pollData object
    var _pollData = { title: title,
            actDate: dateActive,
            deactDate: dateDeactive,
			effDate: effDate,
			descr: description,
			pollNotice: pollNotice,
			profTitle: profTitle,
            pollType: pollType,
			otherPollTypeInput: otherPollTypeInput,
			votingOptions: voteOptions,
			dept: dept,
            name: profName,
            assistantForm: assistantForm,
            associateForm: associateForm,
            fullForm: fullForm, 
            sendFlag: sendFlag 
     }; // end of _pollData

    var _votingInfo = { };
	var id = '';
	var val = '';

	// Iterate through hidden input fields and store the input field into 
	// an associative array for posting  
	$('#votingInfo').children('input:hidden').each(function() {
			// Store all hidden input field data into an votingInfo object
			id = $(this).attr("id");
			val = $(this).val();
			if(id == "pollId") {
				_pollData[id] = val;
			} else {
				_votingInfo[id] = val;
			}	
	});
	// Post data
	console.log(_votingInfo );
	console.log( _pollData );
	//validData = 0;
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
                    .fail(function(error,status) {
                		console.log(error);
				console.log(status);
			}); // End of $.post()
        } // End of if(_reason)
    } // End of if(validData)
}; // End of pollAction()
</script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
// Duplication or removal of poll actions on button click
$(".addAction").on('click',clonePollAction);
$(".delAction").on('click',removePollAction);
// Clears the input fields of the newly cloned action fields
function updateActionFields(actionData,index) {
	$('[name="fromTitle"]').eq(index).val(actionData['fromTitle']);
	$('[name="fromStep"]').eq(index).val(actionData['fromStep']);
	$("[name='toTitle']").eq(index).val(actionData['toTitle']);
	$('[name="toStep"]').eq(index).val(actionData['toStep']);
};
// Store action data in array before cloning to prevent data loss
function saveActionData(index) {
	actionData = {
		fromTitle: $('[name="fromTitle"]').eq(index).val(),
		fromStep: $('[name="fromStep"]').eq(index).val(),
		toTitle: $("[name='toTitle']").eq(index).val(),
		toStep: $('[name="toStep"]').eq(index).val()
	};
	return actionData;
}
// Store the field values of the second 
// Date setup function
$(function () {
	$( "#dateActive" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#dateDeactive" ).datepicker( {dateFormat: 'yy-mm-dd' });
	$( "#effDate" ).datepicker( {dateFormat: 'yy-mm-dd' });
});
function clonePollAction() {
	var count = $("[name='fromTitle']").size();
	var tmpActionData = [];
	var EMPTY = { fromTitle: "", fromStep: "", toTitle: "", toStep: "" };
	var ACTION2_INDEX = 1; 
	var ACTION3_INDEX = 2;
	
	if(count < 3) {
		switch(count) {
			case 1:
				// Cloning causes data to be copied as well, so erase
				$('#actions').clone(true).insertAfter("#actions:last");
				updateActionFields(EMPTY,ACTION2_INDEX)
				break;
			case 2:
				// cloning action causes a deep clone (field and data) to be copied
				// So save previous data, clone, then replace clone data with saved data
				tmpActionData = saveActionData(ACTION2_INDEX);
				$('#actions').clone(true).insertAfter("#actions:last");
				updateActionFields(tmpActionData,ACTION2_INDEX);
				updateActionFields(EMPTY,ACTION3_INDEX);
				break;
			default:
				$('#actions').clone(true).insertAfter("#actions:last");
		} // End of switch
	} // End of if(count < 3)
	$('#actionButton').each(function(index) {
		//console.log("index: "+index+" count:"+count);
		if(index < count) {
			// Change color and icon of actionButton when a clone is made
			$(this).removeClass("btn-success addAction").addClass("btn-danger delAction");
			$('#actionIcon').removeClass("glyphicon-plus").addClass("glyphicon-minus");
			$(this).off('click',clonePollAction);
			$(this).on('click',removePollAction);
		} 
	}); 
}
function removePollAction() {
	$(this).parent().parent().parent().remove();
};
// Hide options depending on poll type 
$("#pollType").change(function() {
	if($(this).val() === "Promotion" || $(this).val() === "Merit" || $(this).val() === "Other") {
			$('#profTitleDiv').hide();
			$('#actions').show();

			if($(this).val() === "Other") {
				$('#otherPollTypeDiv').show();
				$('#votingOptionsDiv').show();
			} else {
				$('#otherPollTypeDiv').hide();
				$('#votingOptionsDiv').hide();
			}
			if($(this).val() === "Promotion") {
				$("#profTitle option[value='Assistant Professor']").hide();	
			} else {
				$("#profTitle option[value='Assistant Professor']").show();
			}
	} else { // All other polls
		$('#profTitleDiv').show();
		$("#profTitle option[value='Assistant Professor']").show();
		$("#actions").hide();
		$('#otherPollTypeDiv').hide();
		$('#votingOptionsDiv').hide();
	}
});
//Remove selected professors from hidden input(_votingInfo)
$("#selected").dblclick(function() {
	var name = $("#selected option:selected").val();
	document.getElementById(name).remove();	
	$("#selected option:selected").remove();
});

//Change Notice Descriptions
$(document).ready(function() {
	$("#pollNotices").change(function() {
		if($(this).val() === "CEE" || $(this).val() === "BOTH") {
			$('#ECENotice').hide(200);
			$('#CEENotice').show(200);
		}
		else if($(this).val() === "ECE") {
			$('#CEENotice').hide(200);
			$('#ECENotice').show(200);
		}
	});
});
</script>
<!-- End of javascript/jquery -->
</body>
</html>
