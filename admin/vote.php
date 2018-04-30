<?php
    session_start();

    //require_once 'includes/sessionHandling.php';
    //require_once 'includes/redirections.php';
    require_once 'includes/connDB.php';

    // if(!isAdmin()) {
    //     signOut();
    // } elseif(idleLimitReached()) {
    //     signOut();
    // }

	$pollId = "";
	$profIds = array();
	$profCmts = array();

    date_default_timezone_set('America/Los_Angeles');

	# Set voting variables
	$day = $month = "";
	$title = $description = $actDate = $deactDate = $profName = $effDate = "";
	$pollType = $dept = $tmp_dateDeact = $tmp_dateAct = $tmp_effDate = "";
	$errTitle = $errEffDate= $errActDate = $errDeactDate = $errProfName = "";
 	$pollTypeText = $pollData = "";
	$validTitle =  $validActDate = $validDeactDate = false;

	# User input processing begins here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
        //print_r($_POST);
		# Check for pollId
		# If pollId is set then it is an edit
		# Initialize all values if edit
		if(isset($_POST['pollData'])) {
			$pollData = json_decode($_POST['pollData'],true);
			var_dump($pollData);
		}
		if(isset($_POST["poll_id"])) {
			$pollId = $pollData['poll_id'];
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
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
.error {color: #FF0000;}
.form-inline .actionGroup {
	margin-left: 0;
	margin-right: 0;
}
.hideOption {
	display: none;
}
.hr-title {
	height: 2px;
	background-color: #555;
	margin-top: 20px;
	margin-bottom: 20px;
	width: 100%;
}
.hr-body {
	height: 1px;
	background-color: #555;
	margin-top: 20px;
	margin-bottom: 20px;
	width: 100%;
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
			<li><a href="manage.php">Manage Users</a></li>
		</ul>
	</div>
</nav>
<!-- Form input allows the user to cancel current form data, save the data, -->
<!-- or process the data; User information remains in input area incase form -->
<!-- data needs to be modified before being submitted -->
<div class="container well">
	<div class="center-block" style="width: 90%">
		<ul class="nav nav-tabs nav-justified">
			<li class="active"><a data-toggle="tab" href="#create">Create Poll</a></li>
			<li><a data-toggle="tab" href="#options">Add Options</a></li>
			<li><a data-toggle="tab" href="#preview">Preview Poll</a></li>
		</ul>
	</div>
	<div class="tab-content center-block" style="width: 80%;">
		<div id="create" class="tab-pane fade in active">
			<h2 class="form-heading text-left">Create Poll</h2>
			<hr class="hr-title">
			<form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div id="votingInfo"></div>
				<!-- Row 1 -->
				<div class="row">
					<div class="form-group col-md-6">
						<label for="title">Poll Title</label>
						<input type="text" class="form-control" name="title" id="title" placeholder="Poll Title" maxlength="100" value="<?php if(isset($_POST['title'])) { echo htmlentities ($_POST['title']); } ?>" >
						<span id="titleErr" class="help-block error"></span>
					</div>
					<div id="pollTypeDiv" class="form-group col-md-6">
						<label for="pollType">Poll Type</label>
						<select class="form-control" name="pollType" id="pollType">
							<?php
								$selectCmd = "SELECT p_id,poll_type FROM poll_types ORDER BY poll_type ASC";
								if($result = $conn->query($selectCmd)) {
									while($row = mysqli_fetch_assoc($result)) {
										echo '<option value="'.$row['p_id'].'">'.$row['poll_type'].'</option>';
									}
								}
							?>
						</select>
					</div>
				</div>
				<!-- Row 2 -->
				<div class="row">
					<div class="form-group col-md-4">
						<label for="dept">Department</label>
						<select class="form-control" id="dept" name="dept">
							<?php
								$selectCmd = "SELECT d_id,department FROM departments ORDER BY department ASC";
								if($result = $conn->query($selectCmd)) {
									while($row = mysqli_fetch_assoc($result)) {
										echo '<option value="'.$row['d_id'].'">'.$row['department'].'</option>';
									}
								} else {
									echo mysqli_error($conn);
								}
							?>
						</select>
					</div>
					<div id="profNameDiv" class="form-group col-md-4">
						<label for="profName">Professor's Name</label>
						<input type="text" class="form-control" name="profName" id="profName" placeholder="Professor's Name" value="<?php if(isset($_POST['profName'])) { echo htmlentities ($_POST['profName']); } ?>" >
						<span id="profNameErr" class="help-block error"></span>
					</div>
					<div id="profTitleDiv" class="form-group col-md-4">
						<label for="profTitle">Professor Title</label>
						<select class="form-control" id="profTitle" name="profTitle">
							<?php
								$selectCmd = "SELECT t_id,title FROM titles ORDER BY title ASC";
								if($result = $conn->query($selectCmd)) {
									while($row = mysqli_fetch_assoc($result)) {
										echo '<option value="'.$row['t_id'].'">'.$row['title'].'</option>';
									}
								} else {
									echo mysqli_error($conn);
								}
							?>
						</select>
					</div>
					<div id="otherPollTypeDiv" class="form-group col-md-4 hideOption">
						<label for="otherPollTypeInput">Please enter poll type</label>
						<input class="form-control" id="otherPollTypeInput" name="otherPollTypeInput" type="text" maxlength="100" placeholder="Poll type"
							value="<?php if(isset($_POST['otherPollTypeInput'])) { echo htmlentities ($_POST['otherPollTypeInput']); } ?>" >
						<span id="otherPollTypeError" class="help-block error"></span>
					</div>
				</div>
				<!-- Row 3 -->
				<div class="row">
					<div id="actions" class="form-group form-inline col-md-12 hideOption">
						<span name="actionError" class="help-block error"></span>
						<div class="form-group actionGroup">
							<label for="fromTitle">From</label>
							<input class="form-control" name="fromTitle" type="text" maxlength="100" placeholder="Title">
							<label for="fromStep" class="sr-only">From title</label>
							<input class="form-control" name="fromStep" type="text" maxlength="3" placeholder="Step">
						</div>
						&nbsp&nbsp&nbsp&nbsp&nbsp
						<div class="form-group actionGroup">
							<label for="toTitle">To</label>
							<input class="form-control" name="toTitle" type="text" maxlength="100" placeholder="Title">
							<label for="toStep" class="sr-only">To step</label>
							<input class="form-control" name="toStep" type="text" maxlength="3" placeholder="Step">
						</div>
						&nbsp&nbsp&nbsp&nbsp&nbsp
						<div class="form-group actionGroup">
							<label for="accelerated">Accelerated</label>
							<select class="form-control" name="accelerated">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select>
						</div>
						&nbsp&nbsp&nbsp
						<div class="form-group actionGroup">
							<button id="actionButton" type="button" class="btn btn-success addAction"><span id="actionIcon" class="glyphicon glyphicon-plus"></span></button>
						</div>
					</div>
				</div>
				<hr class="hr-body">
				<!-- Row 4 -->
				<div class="row">
					<div class="form-group col-md-12">
						<label for="description">Description</label>
						<?php
							$description = '';
							$textareaOpen = '<textarea class="form-control" id="description" name="description" maxlength="300" rows="5" cols="70">';
							$textareaClose = '</textarea>';
							if(isset($_POST["description"])) {
								$description = trim(htmlentities($_POST["description"]));
							}
							echo $textareaOpen . $description . $textareaClose;
					 	?>
					</div>
				</div>
				<hr class="hr-body">
				<div class="row">
					<div class="form-group col-md-4">
						<label for="dateActive">Poll Start Date</label>
						<input type="text" class="form-control" name="dateActive" id="dateActive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateActive'])) { echo htmlentities ($_POST['dateActive']); } ?>" >
						<span id="dateActErr" class="help-block error"></span>
					</div>

					<div class="form-group col-md-4">
						<label for="dateDeactive">Poll End Date</label>
						<input type="text" class="form-control" name="dateDeactive" id="dateDeactive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateDeactive'])) { echo htmlentities ($_POST['dateDeactive']); } ?>" >
						<span id="dateDeactErr" class="help-block error"></span>
					</div>

					<div class="form-group col-md-4">
						<label for="effDate">Effective Date</label>
						<input type="text" class="form-control" name="effDate" id="effDate" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['effDate'])) { echo htmlentities ($_POST['effDate']); } ?>" >
						<span id="effDateErr" class="help-block error"></span>
					</div>
				</div>
				<hr class="hr-body">
				<div class="row">
					<div class="form-group col-md-4" id="pollNoticesDiv">
						<label for="pollNotices">Notice</label>
						<select class="form-control" id="pollNotices" name="pollNotices">
							<?php
								$selectCmd = "SELECT n_id,type FROM notices ORDER BY type ASC";
								if($result = $conn->query($selectCmd)) {
									while($row = mysqli_fetch_assoc($result)) {
										echo '<option value="'.$row['n_id'].'">'.$row['type'].'</option>';
									}
								} else {
									echo mysqli_error($conn);
								}
							?>
						</select>
					</div>
					<div class="form-group col-md-8" id="noticeTextDiv">
						<label>Notice Text</label>
							<?php
								$selectCmd = "SELECT n_id,notice FROM notices ORDER BY type ASC";
								$displayFirst = true;
								if($result = $conn->query($selectCmd)) {
									while($row = mysqli_fetch_assoc($result)) {
										$id = 'notice'.$row['n_id'];
										if($displayFirst) {
											echo "<div id=\"{$id}\">{$row['notice']}</div>";
											$displayFirst = false;
										} else {
											echo "<div class=\"hideOption\" id=\"{$id}\">{$row['notice']}</div>";
										}

									}
								} else {
									echo mysqli_error($conn);
								}
							?>
						<!-- <div id="CEENotice" name="CEENotice">Comments may be submitted to the chair prior to the department meeting if the faculty member will not be able to attend the meeting and would like the comments brought up at the meeting for discussion.</div>
						<div class="hideOption" id="ECENotice" name="ECENotice">Anonymous or absentee comments will be raised at the meeting at the Chair's discretion. This is in addition to the above statement i.e. Note: Comments may be submitted.... :)</div> -->
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12 hideOption" id="votingOptionsDiv">
						<label for="voteOptions">Voting Options</label>
						<select class="form-control" id="voteOptions" name="voteOptions">
							<?php
								$selectCmd = "SELECT v_id,options FROM voting_options ORDER BY options ASC";
								if($result = $conn->query($selectCmd)) {
									while($row = mysqli_fetch_assoc($result)) {
										echo '<option value="'.$row['v_id'].'">'.$row['options'].'</option>';
									}
								} else {
									echo mysqli_error($conn);
								}
							?>
						</select>
					</div>
				</div>
				<hr class="hr-body">
				<!-- Begin professor selection -->
				<!-- Selection displays the names and titles of professors -->
				<div class="row">
					<div class="col-md-5">
						<label for="profSel" style="text-align:left;">Add Professors to Poll</label>
						<div class="form-inline">
							<div class="form-group">
								<select multiple size="15" class="form-control" id="profSel" name="profSel" ondblclick="addToSelected()">
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
							<!-- Selection displays list of double clicked (selected) professors -->
							<div id="pollActions" name="pollActions" class="form-group">
								<select multiple size="15" class="form-control"  id="selected" >
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
						</div>
					</div>
					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<label for="assistantForm">Assistant Form</label><br />
								<select class="form-control" id="assistantForm" name="assistantForm">
									<option value="1">Regular Vote</option>
									<option value="2">Advisory Vote</option>
									<option value="3">Confidential Evaluation</option>
								</select>
							</div>
							&nbsp;&nbsp;
							<div class="form-group" id="assistantEvaluationDiv">
								<label for="assistantEvaluationNum">Action</label><br />
								<select class="form-control" id="assistantEvaluationNum">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
								</select>
							</div>
						</div>
						<br /><br /><br />
						<div class="form-inline">
							<div class="form-group">
								<label for="associateForm">Associate Form</label><br />
								<select class="form-control" id="associateForm" name="associateForm">
					       			<option value="1">Regular Vote</option>
					    			<option value="2">Advisory Vote</option>
					    			<option value="3">Confidential Evaluation</option>
								</select>
							</div>
							&nbsp;&nbsp;
							<div class="form-group" id="associateEvaluationDiv">
								<label for="associateEvaluationNum">Action</label><br />
								<select class="form-control" id="associateEvaluationNum">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
								</select>
							</div>
						</div>
						<br /><br /><br />
						<div class="form-inline">
							<div class="form-group">
								<label for="fullForm">Full Professor Form</label><br />
								<select class="form-control" id="fullForm" name="fullForm">
									<option value="1">Regular Vote</option>
									<option value="2">Advisory Vote</option>
									<option value="3">Confidential Evaluation</option>
								</select>
							</div>
							&nbsp;&nbsp;
							<div class="form-group" id="fullEvaluationDiv">
								<label for="fullEvaluationNum">Action</label><br />
								<select class="form-control" id="fullEvaluationNum">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
								</select>
							</div>
							<span id="evaluationFormError" class="help-block error"></span>
						</div>
					</div>
				</div><br />
				<div class="row">
					<div class="form-group">
						<a href="home.php"><button type="button" class="btn btn-danger" value="Cancel">Cancel</button></a>
						&nbsp
						<button type="button" class="btn btn-primary" value="Save" onclick="pollAction(0)">Save</button>
						&nbsp
						<button type="button" class="btn btn-success" value="Start" onclick="pollAction(1)">Start</button>
					 	<button type="button" value="Testing" onclick="testing()">Testing</button>
						<span id="formErrorMessage" class="help-block error"></span>
					</div>
				</div>
			</form>
		</div> <!-- End of create pane -->
		<div id="options" class="tab-pane fade in">
			<form class="form">
			<div class="row">
				<div class="col-md-12">
				<h2 class="form-heading">Add Options</h2>
				<hr class="hr-title">
				</div>
			</div>
			<div class="row">
				<div id="add_dept" class="form-group col-md-4">
					<label for="new_dept">Department</label>
					<input id="new_dept" name="new_dept" class="form-control" type="text" placeholder="New department" maxlength="100">
				</div>
				<div class="form-group col-md-1">
					<button id="new_dept_btn" name="new_dept_btn" class="btn btn-success center-block">
						<span class="glyphicon glyphicon-plus"></span>
					</button>
				</div>
				<div class="form-group col-md-7">
					<p id="add_dept_notice" name="add_dept_notice"></p>
				</div>
			</div>
			<div class="row">
				<div id="add_prof_title" class="form-group col-md-5">
					<label for="new_prof_title">Title</label>
					<input id="new_prof_title" name="new_prof_title" class="form-control" type="text" placeholder="New title" maxlength="100">
				</div>
				<div class="col-md-2">
					<button id="new_prof_title_btn" name="new_prof_title_btn" type="button" class="btn btn-success center-block">
						<span class="glyphicon glyphicon-plus"></span>
					</button>
				</div>
				<div class="col-md-5">
					<p id="add_prof_title_notice" name="add_prof_title_notice"></p>
				</div>
			</div>
			<div class="row">
				<div id="add_poll_type" class="form-group col-md-5">
					<label for="new_poll_type">Poll Type</label>
					<input id="new_poll_type" name="new_poll_type" type="text" placeholder="New poll type" maxlength="100">
				</div>
				<div class="col-md-2">
					<button id="new_poll_type_btn" name="new_poll_type_btn" type="button" class="btn btn-success center-block">
						<span class="glyphicon glyphicon-plus"></span>
					</button>
				</div>
				<div class="col-md-5">
					<p id="add_poll_type_notice" name="add_poll_type_notice"></p>
				</div>
			</div>
		</form>
		</div> <!-- End of options pane -->
	</div> <!-- End of tab content-->
</div> <!-- End of container -->
<!-- Javascript/Json/Jquery begins here -->
<!-- Load javascript sources -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
$(document).ready(function() {
	// Hide evaluation divs until correct poll type is selected
	hideEvaluationDivs();
	// Populate form fields if user is editing a poll
    setupPoll();

	// Hide input unless selected
	$('#otherPollTypeDiv').hide();
	$('#actions').hide();
	$('#votingOptionsDiv').hide();

	$('#assistantForm').change(function() {
		var MERIT = "Merit";
		var OTHER = "Other";
		var PROMOTION = "Promotion";
		var SHOW = 3;
		var pollType = $('#pollType option:selected').text();
		var value = this.value;
		//console.log("assistantFormValue="+value);
		if(pollType == MERIT || pollType == PROMOTION || pollType == OTHER) {
			if(this.value == SHOW) {
				$('#assistantEvaluationDiv').show();
			} else { // hide
				//console.log('hiding assistant evaluation div');
				$('#assistantEvaluationDiv').hide();
			}
		}

	});
	$('#associateForm').change(function() {
		var MERIT = "Merit";
		var OTHER = "Other";
		var PROMOTION = "Promotion";
		var SHOW = 3;
		var pollType = $('#pollType option:selected').text();
		if(pollType == MERIT || pollType == PROMOTION || pollType == OTHER) {
			if(this.value == SHOW) {
				$('#associateEvaluationDiv').show();
			} else { // hide
				$('#associateEvaluationDiv').hide();
			}
		}
	});
	$('#fullForm').change(function() {
		var MERIT = "Merit";
		var OTHER = "Other";
		var PROMOTION = "Promotion";
		var SHOW = 3;
		var pollType = $('#pollType option:selected').text();
		if(pollType == MERIT || pollType == PROMOTION || pollType == OTHER) {
			if(this.value == SHOW) {
				$('#fullEvaluationDiv').show();
			} else { // hide
				$('#fullEvaluationDiv').hide();
			}
		}
	});
}); // End .ready()
	function testing() {
		console.log(getActions());
	};
	function hideEvaluationDivs() {
		$('#assistantEvaluationDiv').hide();
		$('#associateEvaluationDiv').hide();
		$('#fullEvaluationDiv').hide();
	}
	function timeOutReload() {
        	location.reload(true);
    };

	function setupPoll() {
		storePollId();
		setPollType();
		setDept();
		setProfTitle();
		setForms();

	};

	function setForms() {
		var EVALUATION = 3;
		// Extract all form information
		var assistantForm = <?php if(isset($pollData['assistantForm'])) { echo $pollData['assistantForm']; } else { echo 0; } ?>;
		var associateForm = <?php if(isset($pollData['associateForm'])) { echo $pollData['associateForm']; } else { echo 0; } ?>;
		var fullForm = <?php if(isset($pollData['fullForm'])) { echo $pollData['fullForm']; } else { echo 0; } ?>;
		// Log all data for testing
		//console.log("assistant form: "+assistantForm+" associate form: "+associateForm+" full form: "+fullForm);
		// Set all forms
		if(assistantForm) { $('#assistantForm').val(assistantForm); }
		if(associateForm) { $('#associateForm').val(associateForm); }
		if(fullForm) { $('#fullForm').val(fullForm); }
		// Set all evaluation forms
		var evaluationNum = 0;
		if(assistantForm == EVALUATION) {
			evalutationNum = <?php if(isset($pollData['assistantEvaluationNum'])) { echo $pollData['assistantEvaluationNum']; } else { echo 0; } ?>;
			console.log("asst eval num: "+evaluationNum);
			$('#assistantEvaluationNum').val(evaluationNum);
			$('#assistantEvaluationDiv').show();
		}
		if(associateForm == EVALUATION) {
			evaluationNum = <?php if(isset($pollData['associateEvaluationNum'])) { echo $pollData['associateEvaluationNum']; } else { echo 0; } ?>;
			console.log("assoc. eval. num: "+evaluationNum);
			$('#associateEvaluationNum').val(evaluationNum);
			$('#associateEvaluationDiv').show();
		}
		if(fullForm == EVALUATION) {
			evaluationNum = <?php if(isset($pollData['associateEvaluationNum'])) { echo $pollData['associateEvaluationNum']; } else { echo 0; } ?>;
			console.log("full eval. num: "+evaluationNum);
			$('#fullEvaluationNum').val(evaluationNum);
			$('#fullEvaluationDiv').show();
		}
	};

	function setProfTitle() {
		var profTitle = <?php if(isset($pollData['profTitle'])) { echo json_encode($pollData['profTitle']); } else { echo 0; } ?>;
		//console.log("profTitle: "+profTitle);
		if(profTitle) { $('#profTitle').val(profTitle); }
	};

	function setDept() {
		var dept = <?php if(isset($pollData['dept'])) { echo json_encode($pollData['dept']); } else { echo 0; } ?>;
		console.log("dept: "+dept);
		if(dept) { $('#dept').val(dept); }
	};

	function setPollType() {
		var type = <?php if($pollType) { echo json_encode($pollType); } else { echo 0; } ?>;
		console.log("type: "+type);
		if(type) { $('#pollType').val(type); }
	};

	function storePollId() {
		var id = <?php if(isset($pollData['poll_id'])) { echo json_encode($pollData['poll_id']); } else { echo -1; } ?>;
		console.log("pollId: "+id);
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

function getActionCount() {
	var count = 0;
	$('[name="fromTitle"').each(function() {
		count += 1;
	});
	return count;
}

function pollAction(sendFlag) {
	var MERIT = "Merit";
	var OTHER = "Other";
	var PROMOTION = "Promotion";
	var CONFIDENTIAL_EVAL = "Confidential Evaluation";
	var ACTION_NUM_TOO_LARGE_ERROR = "Evaluation action number greater than number of actions.";
	var SELECT_ACTION_NUM_ERROR = "Please select an action number";
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
	var pollTypeVal = $('#pollType').val();
	var pollTypeTxt = $('#pollType option:selected').text();
	var profTitle = $('#profTitle').val();
	var dept = $('#dept').val();
	var assistantFormVal = $('#assistantForm').val();
	var assistantFormTxt = $("#assistantForm option:selected").text();
	var associateFormVal = $('#associateForm').val();
	var associateFormTxt = $("#associateForm option:selected").text();
	var fullFormVal = $('#fullForm').val();
	var fullFormTxt = $("#fullForm option:selected").text();
	var assistantEvaluationNum = $('#assistantEvaluationNum').val();
	var associateEvaluationNum = $('#associateEvaluationNum').val();
	var fullEvaluationNum = $('#fullEvaluationNum').val();
	var otherPollTypeInput = $('#otherPollTypeInput').val();

	// Setting All flags
	var validTitle = validDescr = validAct = validDeact = validDateEff = false;
	var validPollType = validDept = validData = validEvaluationNum = false;
    var validTitle = validDescr = validAct = validDeact = validEffDate = false;
    var validName = validPollType = validData = validOtherPollType = false;
    var validAssistantEvalNum = validAssociateEvalNum = validFullEvalNum = formError = false;

    // Begin validating form information
    if(pollTypeTxt == MERIT || pollTypeTxt == PROMOTION || pollTypeTxt == OTHER) {
 		console.log(1);
    	var numActions = getActionCount();
    	if(assistantFormTxt == CONFIDENTIAL_EVAL) {
    		if(assistantEvaluationNum > numActions) {
    			$('#evaluationFormError').text(ACTION_NUM_TOO_LARGE_ERROR);
    		} else if(assistantEvaluationNum == 0) {
    			$('#evaluationFormError').text(SELECT_ACTION_NUM_ERROR);
    		} else {
    			console.log(2);
    			validAssistantEvalNum = true;
    		}
    	}
    	if(associateFormTxt == CONFIDENTIAL_EVAL) {
    		if(associateEvaluationNum > numActions) {
    			$('#evaluationFormError').text(ACTION_NUM_TOO_LARGE_ERROR);
    		} else if(associatetEvaluationNum == 0) {
    			$('#evaluationFormError').text(SELECT_ACTION_NUM_ERROR);
    		} else {
    			console.log(3);
    			validAssociateEvalNum = true;
    		}
    	} else {
    		validAssociateEvalNum = true;
    	}
    	if(fullFormTxt == CONFIDENTIAL_EVAL) {
    		if(fullEvaluationNum > numActions) {
    			$('#evaluationFormError').text(ACTION_NUM_TOO_LARGE_ERROR);
    		} else if(assistantEvaluationNum == 0) {
    			$('#evaluationFormError').text(SELECT_ACTION_NUM_ERROR);
    		} else {
    			console.log(4);
    			validFullEvalNum = true;
    		}
    	} else {
    		validFullEvalNum = true;
    	}
    	if(validAssistantEvalNum && validAssociateEvalNum && validFullEvalNum) {
    		$('#evaluationFormError').text("");
    		validEvaluationNum = true;
    	}
    } else { // the other poll types do not contain multiple actions
    	validEvaluationNum = true;
    }
    if(!title || title.length == 0) {
        $("#titleErr").text("* Title required");
    } else {
    	$("#titleErr").text("");
    	validTitle = true;
    }
    if(!dateActive || dateActive.length == 0) {
        $("#dateActErr").text("* Date required");
    } else {
    	$("#dateActErr").text("");
    	validActDate = true;
    }
    if(!dateDeactive || dateDeactive.length == 0) {
        $("#dateDeactErr").text("* Date required");
    } else {
    	$("#dateDeactErr").text("");
    	validDeactDate = true;
    }
    if(!effDate || effDate.length == 0) {
        $("#effDateErr").text("* Date required");
    } else {
    	$("#effDateErr").text("");
    	validEffDate = true;
    }
    if(!pollTypeTxt || pollTypeTxt.length == 0) {
        $("#pollTypeErr").text("* Poll type required");
    } else {
    	$("#pollTypeErr").text("");
    	validPollType = true;
    }
    // If dept = other then there is additional input available
    if(!profName || profName.length == 0) {
        $('#profNameErr').text('* Name required');
    } else {
    	$('#profNameErr').text('');
    	validName = true;
    }
	if(pollTypeTxt == 'Other') {
		if(!otherPollTypeInput || otherPollTypeInput.length == 0) {
			$('#otherPollTypeError').text('* Poll type required');
		} else {
			$('#otherPollTypeError').text('');
			validOtherPollType = true;
		}
	}
	if(pollTypeTxt == 'Promotion' || pollTypeTxt == 'Merit' || pollTypeTxt == 'Other') {
		actions = getActions();
	}

    // Set valid data flag if all form data is present and correct
    if(validTitle && validActDate && validDeactDate && validEffDate && validPollType) {
        if(validName && validEvaluationNum) {
        	if(pollType == 'Other') {
        		if(validOtherPollType) {
        			validData = true;
        		} else {
        			formError = true;
        		}
        	} else {
        		validData = true;
        	}
        } else { formError = true; }
    } else { formError = true; }
    // Form does not fit on screen, so let user know about errors at top of form
    if(formError) {
    	$('#formErrorMessage').text("Some form data is incorrect or missing.");
    	console.log("valid title: "+ validTitle + " actDate: " + validActDate + " validDeactDate: " + validDeactDate);
    	console.log("validEffDate: " + validEffDate + "pollType: " + validPollType + " validName: " + validName);
    	console.log("validEvaluationNum: " + validEvaluationNum);
    }

    if(validData) {
        // Create pollData object
        var _pollData = { title: title,
            actDate: dateActive,
            deactDate: dateDeactive,
			effDate: effDate,
			descr: description,
			pollNotice: pollNotice,
			profTitle: profTitle,
            pollType: pollTypeVal,
			otherPollTypeInput: otherPollTypeInput,
			votingOptions: voteOptions,
			dept: dept,
            name: profName,
            assistantForm: assistantFormVal,
            associateForm: associateFormVal,
            fullForm: fullFormVal,
            assistantEvaluationNum: assistantEvaluationNum,
            associateEvaluationNum: associateEvaluationNum,
            fullEvaluationNum: fullEvaluationNum,
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
	}

	// Post data
	console.log(_votingInfo );
	console.log( _pollData );
	// validData = false;
    // Store data in database if all required information is valid
    if(validData) {
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

	// Now that the page has loaded, store the first notice that appears and prepare for changes
	// Changes to the notice selection dropdown will cause different notices to appear besides
	// the dropdown
	var prevNotice = '';
	var prevNoticeId = $("#pollNotices").val();
	console.log(prevNotice);
	$("#pollNotices").change(function() {
		prevNotice = '#notice' + prevNoticeId;
		nextNotice = '#notice' + this.value;

		$(prevNotice).hide(200);
		$(nextNotice).show(200);
		prevNoticeId = this.value;
	});
});
function clonePollAction() {
	var count = $("[name='fromTitle']").size();
	var tmpActionData = [];
	var EMPTY = { fromTitle: "", fromStep: "", toTitle: "", toStep: "" };
	var ACTION1_INDEX = 0;
	var ACTION2_INDEX = 1;
	var ACTION3_INDEX = 2;

	if(count < 3) {
		switch(count) {
			case 1:
				// Cloning causes data to be copied as well, so erase
				tmpActionData = saveActionData(ACTION1_INDEX);
				$('#actions').clone(true).insertAfter("#actions:last");
				updateActionFields(tmpActionData,ACTION1_INDEX);
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
	//console.log($(this).parent());
	//console.log($(this));
	$(this).parent().parent().remove();
};
// Hide options depending on poll type
$("#pollType").change(function() {
	// Reset form options when poll changes
	var defaultForm = 1;
	$('#assistantForm').val(defaultForm);
	$('#associateForm').val(defaultForm);
	$('#fullForm').val(defaultForm);
	var pollType = $('#pollType option:selected').text();
	if(pollType === "Promotion" || pollType === "Merit" || pollType === "Other") {
			$('#profTitleDiv').hide();
			$('#actions').show();
			if(pollType === "Other") {
				$('#profNameDiv').attr('class','form-group col-md-4');
				$('#otherPollTypeDiv').show();
				$('#votingOptionsDiv').show();
			} else {
				$('#otherPollTypeDiv').hide();
				$('#profNameDiv').attr('class','form-group col-md-8');
				$('#votingOptionsDiv').hide();
			}
			if(pollType === "Promotion") {
				$("#profTitle option[value='Assistant Professor']").hide();
			} else {
				$("#profTitle option[value='Assistant Professor']").show();
			}
	} else { // All other polls
		$('#profNameDiv').attr('class','form-group col-md-4');
		$('#profTitleDiv').show();
		$("#profTitle option[value='Assistant Professor']").show();
		$("#actions").hide();
		$('#otherPollTypeDiv').hide();
		$('#votingOptionsDiv').hide();
		hideEvaluationDivs();
	}
});
//Remove selected professors from hidden input(_votingInfo)
$("#selected").dblclick(function() {
	var name = $("#selected option:selected").val();
	document.getElementById(name).remove();
	$("#selected option:selected").remove();
});

</script>
<!-- End of javascript/jquery -->
</body>
</html>
