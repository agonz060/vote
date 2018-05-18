<?php
    session_start();

    //require_once 'includes/sessionHandling.php';
    //require_once 'includes/redirections.php';
    require_once 'includes/connDB.php';
    require_once '../includes/functions.php';

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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
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
.heading-options, .align-create-poll-buttons {
	padding-left: 15px;
}
.heading-actions {
	padding-left: 15px;
	margin-bottom: 0px;
}
.button-options {
	margin-top: 3px;
}
.p-options {
	margin-top: 7px;
}
.hidden {
	display: none !important;
	visibility: hidden !important;
}
.show {
	display: block !important;
}
a.options, a.pollAction {
	color: #333 !important;
}
a:hover.options, a:hover.pollAction   {
  /* Applies to links under the pointer */
  text-decoration:  none !important;
  text-decoration-color: black !important;
  background-color: none !important;
  color:            black !important;
  }
textarea:disabled {
	background-color: #fff !important;
}
.selectProfs-btn-col {
	/*padding-left: 0px;
	padding-right: 0px;*/
}
.selectProfs-btn-container1 {
	width: auto;
	height: 134px;/* height of a <select> w/ of size 6 */
	/*margin-top: 25px;*/
}
.selectProfs-btn-container2 {
	/* height of a <select> w/ of size 6 */
	width: auto;
	height: 134px;
}
.selectProfs-btn {
	padding-left: 7px;
	padding-right: 7px;
}
.flex-container {
	display: flex;
	align-items: center;
	justify-content: center;
}
.flex-container-advisoryForm-action {
	/*display: flex;*/
	margin-top: 10px;
	height: 141px;
	align-content: flex-start;
}
.assignForms-p {
	margin-top: 0px;
	margin-bottom: 5px;
}
.assignForms-advisoryDiv {
	margin-top: 11px;
}
.assignForms-row {
	padding-left: 15px;
}
.advisory-action-dropdown {
	position: static;
}
.custom-menu {
    display: none;
    z-index: 1000;
    position: absolute;
    overflow: hidden;
    border: 1px solid #CCC;
    white-space: nowrap;
    font-family: sans-serif;
    background: #FFF;
    color: #333;
    border-radius: 5px;
}
.custom-menu li {
    padding: 8px 12px;
    cursor: pointer;
}
.custom-menu li:hover {
    background-color: #DEF;
}
.actions-title, .actions-col, .createVote-action-btn {
	padding-right: 0px;
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
		<ul id="voteCreateNav" class="nav nav-tabs nav-justified">
			<li class="active"><a data-toggle="tab" href="#create">Create Poll</a></li>
			<li><a data-toggle="tab" href="#preview">Preview Poll</a></li>
			<li><a data-toggle="tab" href="#options">Add Options</a></li>
		</ul>
	</div>
	<div class="tab-content center-block" style="width: 80%;">
		<div id="create" class="tab-pane fade in active">
			<h2 class="form-heading text-left">Create Poll<small id="formNotice"></small></h2>
			<hr class="hr-title">
			<form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<!-- Row 1 : Poll Title and Poll Type -->
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<h4><label for="title"><u>Poll Title</u></label><small id="titleErr"></small></h4>
							<input type="text" class="form-control" name="title" id="title" placeholder="Poll Title" maxlength="100" value="<?php if(isset($_POST['title'])) { echo htmlentities ($_POST['title']); } ?>" >
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group ">
							<h4><label for="pollType"><u>Poll Type</u></label></h4>
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
				</div> <!-- end row 1 -->
				<!-- Row 2 : Department, Prof. Name, Prof. Title, OtherPollType input -->
				<div class="row">
					<div class="col-md-4">
						<div class="form-group ">
							<h4><label for="dept"><u>Department</u></label></h4>
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
					</div>
					<div id="profNameDiv" class="col-md-4">
						<div class="form-group ">
							<h4><label for="profName"><u>Professor's Name</u></label>
								<small id="profNameErr"></small>
							</h4>
							<input type="text" class="form-control" name="profName" id="profName" placeholder="Professor's Name" value="">
						</div>
					</div>
					<div id="profTitleDiv" class="col-md-4">
						<div class="form-group ">
						<h4><label for="profTitle"><u>Professor Title</u></label></h4>
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
					</div>
					<div id="otherPollTypeDiv" class="col-md-4 hidden">
						<div class="form-group ">
							<h4><label for="otherPollTypeInput"><u>Please enter poll type</u></label>
								<small id="otherPollTypeError"></small>
							</h4>
							<input id="otherPollTypeInput" class="form-control"  name="otherPollTypeInput" type="text" maxlength="100" placeholder="Poll type"
								value="<?php if(isset($_POST['otherPollTypeInput'])) { echo htmlentities ($_POST['otherPollTypeInput']); } ?>" >
						</div>
					</div>
				</div> <!-- end row 2 -->
				<hr id="actionsDivider" class="hr-body hidden">
				<!-- Row 3 : Actions -->
				<div id="createPollActionRow" class="row hidden">
					<h4 id="actionsHeading" class="heading-actions"><label><u>Actions</u></label><small name="actionError"></small></h4>
					<div id="action" name="action1">
						<div class="col-xs-5 col-sm-4 col-md-4 actions-col">
							<h5><u>From</u></h5>
							<div class="row">
								<div class="col-xs-7 col-sm-7 col-md-7 form-group actions-title">
									<label for="fromStep" class="sr-only">From Title</label>
									<input class="form-control actions-title" name="fromTitle" type="text" maxlength="100" placeholder="Title">
								</div>
								<div class="col-xs-6 col-sm-5 col-md-5 form-group">
									<label for="fromStep" class="sr-only">From Step</label>
									<input class="form-control" name="fromStep" type="text" maxlength="3" placeholder="Step">
								</div>
							</div>
						</div>
						<div class="col-xs-5 col-sm-4 col-md-4 actions-col">
							<h5><u>To</u></h5>
							<div class="row">
								<div class="col-xs-7 col-sm-7 col-md-7 form-group actions-title">
									<label for="toTitle" class="sr-only">To</label>
									<input class="form-control" name="toTitle" type="text" maxlength="100" placeholder="Title">
								</div>
								<div class="col-xs-6 col-sm-5 col-md-5 form-group">
									<label for="toStep" class="sr-only">To step</label>
									<input class="form-control" name="toStep" type="text" maxlength="3" placeholder="Step">
								</div>
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 actions-col">
							<h5><u><a name="acceleratedAction" class="pollAction">Accelerated</a></u></h5>
							<div class="row">
								<div class="col-xs-8 col-sm-4 col-md-3 form-group">
									<!-- <div class="input-group-btn"> -->
										<button id="accelerated_action_btn" name="accelerated_action_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="no">No <span class="caret"></span>
										</button>
										<ul id="accelerated_action_dropdown" name="accelerated_action_dropdown" class="dropdown-menu">
											<li><a  href="#acceleratedAction" data-value="yes">Yes</a></li>
										</ul>
									<!-- </div> end button group -->
								</div>
								<div class="col-xs-4 col-sm-3 col-md-2 createVote-action-btn">
									<button id="actionButton" type="button" class="btn btn-success addAction"><span id="actionIcon" class="glyphicon glyphicon-plus"></span></button>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- end row 3  -->
				<!-- Start row 4 : voting options -->
				<hr id="votingOptionsDivider" class="hr-body hidden">
				<div id="votingOptionsDiv" class="row hidden">
					<div class="col-md-12">
						<div class="form-group">
							<h4><label for="voteOptions"><u>Voting Options</u></label></h4>
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
				</div>
				<hr class="hr-body">
				<!-- start row 5 : Poll Start Date, End Date, Effective Date -->
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<h4><label for="dateActive"><u>Poll Start Date</u></label><small id="dateActErr"></small></h4>
							<input type="text" class="form-control" name="dateActive" id="dateActive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateActive'])) { echo htmlentities ($_POST['dateActive']); } ?>" >
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<h4><label for="dateDeactive"><u>Poll End Date</u></label><small id="dateDeactErr"></small></h4>
							<input type="text" class="form-control" name="dateDeactive" id="dateDeactive" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['dateDeactive'])) { echo htmlentities ($_POST['dateDeactive']); } ?>" >
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<h4><label for="effDate"><u>Effective Date</u></label><small id="effDateErr"></small></h4>
							<input type="text" class="form-control" name="effDate" id="effDate" placeholder="YYYY-MM-DD" value="<?php if(isset($_POST['effDate'])) { echo htmlentities ($_POST['effDate']); } ?>" >
						</div>
					</div>
				</div> <!-- end row 5 -->
				<hr class="hr-body">
				<!-- start row 6 : Notices -->
				<div class="row">
					<div class="col-md-4">
						<div id="pollNoticesDiv" class="form-group" >
							<h4><label for="pollNotices"><u>Notice</u></label><small id="noticeMsg"></small></h4>
							<?php
								// open select tag
								echo '<select class="form-control" id="pollNotices" name="pollNotices">';

								// output select options
								$notices = getNotices();
								$noticeIds = array_keys($notices);
								$notice = array();
								for($i=0; $i < count($noticeIds); $i++) {
									$id = $noticeIds[$i];
									$notice = $notices[$id];
									echo '<option value="'.$id.'">'.$notice['type'].'</option>';
								}
								// close select tag and form-group div
								echo '</select>
									</div> <!-- end of pollNoticesDiv -->';

								// close col-md-4 div
								echo '</div> <!-- end of col-md-4 -->';

								// open div that displays the notice's text
								echo '<div id="noticeTextDiv" class="col-md-8" >';
								echo '<div class="form-group">';

								// display label notice's text
								echo '<h4><label><u>Notice Text</u></label></h4>';

								// begin display text
								$id = '';
								$displayFirstText = true;
								for($i=0; $i < count($noticeIds); $i++) {
									$id = $noticeIds[$i];
									$noticeId = 'notice'.$id;
									$notice = $notices[$id];
									if($displayFirstText) {
										echo '<p id="'.$noticeId.'" class="">'.$notice['notice'].'</p>';
										$displayFirstText = false;
									} else {
										echo '<p id="'.$noticeId.'" class="hidden">'.$notice['notice'].'</p>';
									}
								}
							?>
						</div> <!-- end form group -->
					</div> <!-- col-md-8 -->
				</div> <!-- end notices row -->
				<!-- Begin professor selection -->
				<ul class="custom-menu" style="list-style-type: none; margin: 0px; padding: 0px;">
					<li data-action="regular">Regular Form</li>
					<li data-action="advisory">Advisory Form</li>
				</ul>
				<!-- Selection displays the names and titles of professors -->
				<hr class="hr-body">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-5">
						<h4><u>Select Professors</u><br /><small id="selectedProfsNotice"></small></h4>
						<div class="form-group" oncontextmenu="return false;">
							<label for="profSelelection">All Professors</label>
							<select multiple size="15" class="form-control" id="profSelection" name="profSelection">
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

									echo '<option  value="'.$fullName.'">'.$selection."</option>";

									// Store a mapping of professor names to professor id's
									// for quicker storage later on
									$profId = array($fullName => $profId);
									$profIds = array_merge($profIds, $profId);
								}
							?>
							</select>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-7">
						<h4 class="col-md-offset-1"><a id="assignFormsHeading" name="assignFormsHeading" class="options"><u>Assign Forms</u></a><small id="assignFormsNotice"></small></h4>
						<div class="row assignForms-row">
							<div class="form-inline">
								<div class="form-group col-xs-12 col-sm-12 col-md-12">
									<div class="row">
										<div class="col-xs-1 col-sm-1 col-md-1">
											<div class="selectProfs-btn-container1 flex-container">
												<button type="button" class="btn btn-success selectProfs-btn"><span class="glyphicon glyphicon-arrow-right"></span></button>
											</div>
										</div>
										<div class="col-xs-11 col-sm-11 col-md-11">
											<p class="assignForms-p"><b><lable for="regularFormGroup">Regular Form</lable></b></p>
											<select multiple id="regularFormGroup" name="regularFormGroup" class="form-control" size="6">
											</select>
										</div>
									</div>
								</div>
								<div class="form-group col-xs-12 col-sm-12 col-md-12">
									<div class="row">
										<div class="form-group col-xs-1 col-sm-1 col-md-1">
											<div class="selectProfs-btn-container1 flex-container">
												<button type="button" id="advisoryFormBtn" class="btn btn-success selectProfs-btn"><span class="glyphicon glyphicon-arrow-right"></span></button>
											</div>
										</div>
										<div class="form-group col-xs-10 col-sm-10 col-md-10">
											<div class="form-group assignForms-advisoryDiv">
												<p class="assignForms-p"><b>Advisory Form</b><lable for="regularFormGroup" class="sr-only">Advisory Form</lable></p>
												<select multiple id="advisoryFormGroup" name="regularFormGroup" class="form-control" size="6">
													<option data-value="prof1">Professor 1GoesHere</option>
													<option data-value="prof2">Professor 2GoesHere</option>
												</select>
											</div>
											<div class="form-group flex-container-advisoryForm-action">
												<p class="assignForms-p"><b>Action</b></p>
												<!-- <div class="input-group-btn"> -->
													<button id="advisory_form_action_dropdown_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="1">1&nbsp;<span class="caret"></span>
													</button><br />
													<ul id="advisory_form_action_dropdown" class="dropdown-menu advisory-action-dropdown">
														<li><a href="#assignFormsHeading" data-value="2">2</a></li>
														<li><a href="#assignFormsHeading" data-value="3">3</a></li>
													</ul>
												<!-- </div> end button group -->
											</div> <!-- end of advisory action button div -->
										</div> <!-- end of advisory <select> and action <button> divs -->
									</div> <!-- end of advisory <select> and action roww -->
								</div> <!-- end of advisory <select> and action <button> col -->
							</div> <!-- end of form inline -->
						</div> <!-- end of assign forms row -->
					</div> <!-- end of assign forms columns -->
				</div> <!-- end of selecting professors/assigning forms row -->
				<hr class="hr-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<!-- <div class="center-block"> -->
							<a href="home.php"><button type="button" class="btn btn-danger" value="Cancel">Cancel</button></a>
							&nbsp
							<button type="button" class="btn btn-primary" value="Save" onclick="pollAction(0)">Save</button>
							&nbsp
							<button type="button" class="btn btn-success" value="Start" onclick="pollAction(1)">Start</button>
						<!-- </div> -->
					</div>
				</div>
				<div id="votingInfo">
					<?php
						echo '<input type="hidden" name="profIds" value="'.json_encode($profIds).'">';
					?>
				</div>
			</form> <!-- end form -->
		</div> <!-- End of create pane -->
		<!-- start of options pane -->
		<div id="options" class="tab-pane fade in">
			<h2 class="form-heading">Options</h2>
			<hr class="hr-title">
			<div class="row">
				<h3 class="heading-options"><a name="add_option_title" class="options"><u>Title</u></a>
					<small id="options_title_status" name="options_title_status" ></small>
				</h3>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button id="title_dropdown_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="add">Add <span class="caret"></span>
									</button>
									<ul id="title_dropdown" class="dropdown-menu">
										<li><a href="#add_option_title" data-value="remove">Remove</a></li>
									</ul>
								</div> <!-- end button group -->
								<input id="new_title" name="new_prof_title" class="form-control" type="text" placeholder="Enter new title" maxlength="100">
								<select id="title_remove_select" class="form-control hidden">
									<?php
										$titles = getTitles();
										$option = '';
										$titleIds = array_keys($titles);
										for($it=0; $it < count($titleIds); $it++) {
											$id = $titleIds[$it];
											if($it == 0) {
												echo '<option value="'.$id.'" selected>'.$titles[$id].'</option>';
											} else {
												echo '<option value="'.$id.'">'.$titles[$id].'</option>';
											}
										}
									?>
								</select>
							</div> <!-- end input group -->
						</div> <!-- End of col-md-7 -->
						<div class="col-md-1">
							<button id="title_action_btn" name="title_action_btn" type="button" class="btn btn-success button-options">
							<span id="title_action_glyphicon" class="glyphicon glyphicon-plus"></span>
							</button>
						</div>
					</div> <!-- end row -->
				</div> <!-- end col-md-12 -->
			</div> <!-- end title row -->
			<br />
			<hr class="hr-body">
			<div class="row">
				<h3 class="heading-options"><a name="add_option_poll_type" class="options"><u>Poll Type</u></a>
					<small id="options_poll_type_status" name="options_poll_type_status"></small>
				</h3>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button id="poll_type_dropdown_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="add">Add <span class="caret"></span>
									</button>
									<ul id="poll_type_dropdown" class="dropdown-menu">
										<li><a href="#add_option_poll_type" data-value="remove">Remove</a></li>
									</ul>
								</div> <!-- end button group -->
								<input id="new_poll_type" name="new_poll_type" class="form-control" type="text" placeholder="Enter new poll type" maxlength="100">
								<select id="poll_type_remove_select" class="form-control hidden">
									<?php
										$pollTypes = getPollTypes();
										$pollTypeIds = array_keys($pollTypes);
										$option = '';
										for($it=0; $it < count($pollTypeIds); $it++) {
											$id = $pollTypeIds[$it];
											echo '<option value="'.$id.'">'.$pollTypes[$id].'</option>';
										}
									?>
								</select>
							</div> <!-- end input group -->
						</div> <!-- end col-md-7 -->
						<div class="col-md-1">
							<button id="poll_type_action_btn" name="poll_type_action_btn" type="button" class="btn btn-success button-options">
							<span id="poll_type_action_glyphicon" class="glyphicon glyphicon-plus"></span>
							</button>
						</div>
					</div> <!-- end row -->
				</div> <!-- end col-md-12 -->
			</div> <!-- end poll type row -->
			<br />
			<hr class="hr-body">
			<div class="row">
				<h3 class="heading-options"><a name="add_option_dept" class="options"><u>Department</u></a>
					<small id="options_dept_status" name="options_dept_status"></small>
				</h3>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button id="dept_dropdown_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="add">Add <span class="caret"></span>
									</button>
									<ul id="dept_dropdown" class="dropdown-menu">
										<li><a href="#add_option_dept" data-value="remove">Remove</a></li>
									</ul>
								</div> <!-- end button group -->
								<input id="new_dept" name="new_dept" class="form-control" type="text" placeholder="Enter new department" maxlength="100">
								<select id="dept_remove_select" class="form-control hidden">
									<?php
										$depts = getDepartments();
										$deptIds = array_keys($depts);
										$option = '';
										for($it=0; $it < count($deptIds); $it++) {
											$id = $deptIds[$it];
											echo '<option value="'.$id.'">'.$depts[$id].'</option>';
										}
									?>
								</select>
							</div> <!-- end input group -->
						</div> <!-- end col-md-7 -->
						<div class="col-md-1">
							<button id="dept_action_btn" name="dept_action_btn" class="btn btn-success button-options">
							<span id="dept_action_glyphicon" class="glyphicon glyphicon-plus"></span>
							</button>
						</div>
					</div> <!-- end row -->
				</div> <!-- end col-md-12 -->
			</div> <!-- end department row -->
			<br />
			<hr class="hr-body">
			<div class="row">
				<h3 class="heading-options"><a name="add_option_notice" class="options"><u>Notice</u></a>
					<small id="options_notice_status" name="options_notice_status"></small>
				</h3>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3">
							<div class="input-group">
								<div class="input-group-btn">
									<button id="notice_dropdown_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="add">Add <span class="caret"></span>
									</button>
									<ul id="notice_dropdown" class="dropdown-menu">
										<li><a href="#add_option_notice" data-value="remove">Remove</a></li>
									</ul>
								</div> <!-- end button group -->
								<input id="new_notice_name" name="new_notice_name" class="form-control" type="text" placeholder="Enter notice name" maxlength="50">
								<?php
									// begin displaying notice select which contains a list of notices stored in a database
									$notices = getNotices();
									$noticeIds = array_keys($notices);
									$notice = array();
									$id = '';
									// Open select
									echo'<select id="notice_remove_select" class="form-control hidden">';
										for($it=0; $it < count($noticeIds); $it++) {
											$noticeId = $noticeIds[$it];
											$notice = $notices[$noticeId];
											echo '<option value="'.$noticeId.'">'.$notice['type'].'</option>';
										}
									// close select
									echo '</select>';
									// close input group div
									echo '</div> <!-- end input group -->';
									// close col-md-3 div
									echo '</div> <!-- end col-md-3 -->';

									// open col-md-6 div which will house the textarea as well as the text of the notices
									echo '<div class="col-md-6">';

									// display textarea which is the default option when adding a new notice
									echo '<textarea id="new_notice_text" class="form-control" row="6" placeholder="Enter new notice..."></textarea>';

									// create a div that contains the text of a notice
									echo '<div id="optionsNoticeTextDiv" class="hidden">';
										// begin display text
										$id = '';
										$displayFirstText = true;
										for($it=0; $it < count($noticeIds); $it++) {
											$noticeId = $noticeIds[$it];
											$pId = 'options_notice'.$noticeId;
											$notice = $notices[$noticeId];
											if($displayFirstText) {
												echo '<textarea id="'.$pId.'" class="form-control" rows="4" disabled>'.$notice['notice'].'</textarea>';
												$displayFirstText = false;
											} else {
												echo '<textarea id="'.$pId.'" class="form-control hidden" rows="4" disabled>'.$notice['notice'].'</textarea>';
											}
										}
									// close optionsNoticeTextDiv
									echo '</div> <!-- end of optionsNoticeTextDiv -->';

									// close col-md-6
									echo '</div> <!-- end col-md-6 -->';
								?>
						<div class="col-md-1">
							<button id="notice_action_btn" name="notice_action_btn" class="btn btn-success button-options">
							<span id="notice_action_glyphicon" class="glyphicon glyphicon-plus"></span>
							</button>
						</div>
					</div> <!-- end row -->
				</div> <!-- end col-md-12 -->
			</div> <!-- end notices row -->
			<br />
			<hr class="hr-body">
			<div class="row">
				<h3 class="heading-options"><a name="add_option_voting_options" class="options"><u>Voting Options</u></a>
					<small id="options_voting_options_status" name="options_voting_options_status"></small>
				</h3>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-9">
							<div class="input-group">
								<div class="input-group-btn">
									<button id="voting_options_dropdown_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="add">Add <span class="caret"></span>
									</button>
									<ul id="voting_options_dropdown" class="dropdown-menu">
										<li><a href="#add_option_voting_options" data-value="remove">Remove</a></li>
									</ul>
								</div> <!-- end button group -->
								<div id="votingOptionsHelpText" class="bg-info show">
								<p class="text-center" style="padding-top: 8px; padding-bottom: 8px; margin-bottom: 0px;">New options: Enter options in the input boxes below, then press the "+" button.</p>
								</div>
								<select id="voting_options_remove_select" class="form-control hidden">
									<?php
										$votingOptions = getVotingOptions();
										$votingOptionsIds = array_keys($votingOptions);
										$option = '';
										for($it=0; $it < count($votingOptionsIds); $it++) {
											$id = $votingOptionsIds[$it];
											echo '<option value="'.$id.'">'.$votingOptions[$id].'</option>';
										}
									?>
								</select>
							</div> <!-- end input group -->
						</div> <!-- end col-md-9 -->
						<div class="col-md-1">
							<button id="voting_options_action_btn" name="voting_options_action_btn" class="btn btn-success button-options">
							<span id="voting_options_action_glyphicon" class="glyphicon glyphicon-plus"></span>
							</button>
						</div>
					</div> <!-- end row -->
				</div> <!-- end col-md-12 -->
			</div> <!-- end voting options row 1 -->
			<br />
			<div class="row">
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon">
							<input type="checkbox" aria-label="..." checked disabled>
						</span>
						<input id="voting_options_1" type="text" class="form-control" aria-label="..." placeholder="Enter option 1">
					</div><!-- /input-group -->
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon">
							<input type="checkbox" aria-label="..." checked disabled="">
						</span>
						<input id="voting_options_2" type="text" class="form-control" aria-label="..." placeholder="Enter option 2">
					</div><!-- /input-group -->
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon">
							<input type="checkbox" aria-label="..." checked disabled>
						</span>
						<input id="voting_options_3" type="text" class="form-control" aria-label="..." placeholder="Enter option 3">
					</div><!-- /input-group -->
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon">
							<input type="checkbox" aria-label="..." checked disabled>
						</span>
						<input id="voting_options_4" type="text" class="form-control" aria-label="..." placeholder="Enter option 4">
					</div><!-- /input-group -->
				</div>
			</div> <!-- end voting options row 2 -->
		</div> <!-- End of options pane -->
	</div> <!-- End of tab content-->
</div> <!-- End of container -->
<!-- Javascript/Json/Jquery begins here -->
<!-- Load javascript sources -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function() {
	// Hide evaluation divs until correct poll type is selected
	hideEvaluationDivs();
	// Populate form fields if user is editing a poll
    setupPoll();
    //--------------- create poll jquery starts here  ------------------------------//
     // Displays calendar when selecting dates
    $( "#dateActive" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#dateDeactive" ).datepicker( {dateFormat: 'yy-mm-dd' });
	$( "#effDate" ).datepicker( {dateFormat: 'yy-mm-dd' });

	// Hide input unless selected
	$('#otherPollTypeDiv').removeClass('show').addClass('hidden');
	$('#actionsDivider').removeClass('show').addClass('hidden');
	// $('#action').removeClass('show').addClass('hidden');
	$('#votingOptionsDivider').removeClass('show').addClass('hidden');
	$('#votingOptionsDiv').removeClass('show').addClass('hidden');

	// Duplication or removal of poll actions on button click
	$(".addAction").on('click',clonePollAction);
	$(".delAction").on('click',removePollAction);

	//Remove selected professors from hidden input(_votingInfo)
	$("#regularFormGroup").dblclick(function() {
		var name = $("#regularFormGroup option:selected").val();
		$('#votingInfo input[type="hidden"][id="'+name+'"]').remove();
		$("#regularFormGroup option:selected").remove();
	});
	//Remove selected professors from hidden input(_votingInfo)
	$("#advisoryFormGroup").dblclick(function() {
		var name = $("#advisoryFormGroup option:selected").val();
		$('#votingInfo input[type="hidden"][id="'+name+'"]').remove();
		$("#advisoryFormGroup option:selected").remove();
	});
	// Trigger action when the contexmenu is about to be shown
	$('#profSelection').on("dblclick", function (event) {
		console.log("profSelection double click");
	    // Avoid the default double click action
	    event.preventDefault();
	    // Show contextmenu
	    $(".custom-menu").finish().toggle(100).
	    // Display menu where cursor was double clicked
	    css({
	        top: event.pageY + "px",
	        left: event.pageX + "px"
	    });
	});
	// If the menu element is clicked
	$(".custom-menu li").on("click", function(){
		$('#selectedProfsNotice').attr('class','').text('');
	    // This is the triggered action name
	    switch($(this).attr("data-action")) {
	        // A case for each action. Your actions here
	        case "regular": assignForm('regular',$('#profSelection option:selected')); break;
	        case "advisory": assignForm('advisory',$('#profSelection option:selected')); break;
	    }
	    // Hide it AFTER the action was triggered
	    $(".custom-menu").hide(100);
	  });
	// Now that the page has loaded, store the first notice that appears and prepare for changes
	// Changes to the notice selection dropdown will cause different notices to appear besides
	// the dropdown
	var prevPollNotice = '';
	var prevPollNoticeId = $("#pollNotices").val();
	//console.log(prevNotice);
	$("#pollNotices").change(function() {
		prevPollNotice = '#notice' + prevPollNoticeId;
		var nextNotice = '#notice' + this.value;

		$(prevPollNotice).removeClass('show').addClass('hidden')
		$(nextNotice).removeClass('hidden').addClass('show').fadeIn("slow");
		prevPollNoticeId = this.value;
	});
	// Make changes to accelerated action button
	$("#accelerated_action_dropdown li a").click(function() {
		// get parent action div
		var actionDiv = $(this).parent().parent().parent().parent().parent().parent();
		// find action btn that will be updated
		var btn = $(actionDiv).find('#accelerated_action_btn');
		// store previous button text and value
		var prevBtnTxt = $(btn).text();
		var prevBtnVal = $(btn).val();
		// update btn text and value
		$(btn).text($(dropdownSelection).text());
		$(btn).val($(dropdownSelection).data('value'));
		// add previous button text and value to the dropdown menu
		$(this).html(prevBtnTxt);
		$(this).data('value',prevBtnVal);
	});
	// Hide options depending on poll type
	$("#pollType").change(function() {
		// Reset form options when poll changes
		var defaultForm = 1;
		$('#assistantForm').val(defaultForm);
		$('#associateForm').val(defaultForm);
		$('#fullForm').val(defaultForm);
		var pollType = $('#pollType option:selected').text();
		//console.log('pollType change: '+pollType);
		if(pollType === "Promotion" || pollType === "Merit" || pollType === "Other") {
				// Hide prof title to allow for custom titles set by action
				$('#profTitleDiv').removeClass('show').addClass('hidden');
				// Show action fields
				$('#actionsDivider').removeClass('hidden').addClass('show');
				$('#createPollActionRow').removeClass('hidden').addClass('show');
				// $('#actionsHeading').removeClass('hidden').addClass('show');
				// $('#action').removeClass('hidden').addClass('show');
				if(pollType === "Other") {
					$('#profNameDiv').attr('class','col-md-4');
					$('#otherPollTypeDiv').removeClass('hidden').addClass('show');
					$('#votingOptionsDivider').removeClass('hidden').addClass('show');
					$('#votingOptionsDiv').removeClass('hidden').addClass('show');
				} else {
					$('#otherPollTypeDiv').removeClass('show').addClass('hidden');
					$('#profNameDiv').attr('class','col-md-8');
					$('#votingOptionsDivider').removeClass('show').addClass('hidden');
					$('#votingOptionsDiv').removeClass('show').addClass('hidden');
				}
				if(pollType === "Promotion") {
					$("#profTitle option[value='Assistant Professor']").hide();
				} else {
					$("#profTitle option[value='Assistant Professor']").show();
				}
		} else { // All other polls
			$('#profNameDiv').attr('class','col-md-4');
			$('#profTitleDiv').removeClass('hidden').addClass('show');
			$("#profTitle option[value='Assistant Professor']").show();
			$('#actionsDivider').removeClass('show').addClass('hidden');
			//$('#actionsHeading').removeClass('show').addClass('hidden');
			$('#actionRow').removeClass('show').addClass('hidden');
			$('#otherPollTypeDiv').removeClass('show').addClass('hidden');
			$('#votingOptionsDivider').removeClass('show').addClass('hidden');
			$('#votingOptionsDiv').removeClass('show').addClass('hidden');
			hideEvaluationDivs();
		}
	});
	//---------------     end of create poll jquery.  ----------------------------------//
    //---------------     add voting option jquery starts here -------------------------//
	$("#title_dropdown li a").click(function() {
	optionsActionChange('title',this);
	});
	$("#poll_type_dropdown  li a").click(function () {
		optionsActionChange('poll_type',this);
	});
	$("#dept_dropdown  li a").click(function () {
		optionsActionChange('dept',this);
	});
	$("#notice_dropdown  li a").click(function () {
		optionsActionChange('notice',this);
	});
	$("#voting_options_dropdown  li a").click(function () {
		optionsActionChange('voting_options',this);
	});
	$('#voteCreateNav li a').click(function () {
		clearOptionsStatuses();
	});
	$('#advisory_form_action_dropdown li a').click(function () {
		// caret icon for dropdown
		var caret = '  <span class="caret"></span>';

		// store previous value to update dropdown
		var prevActionNumTxt = $('#advisory_form_action_dropdown_btn').text();
		var prevActionNumVal = $('#advisory_form_action_dropdown_btn').val();

		// update advisory form action button to represent new user selection
		$('#advisory_form_action_dropdown_btn').html($(this).text() + caret);
		$('#advisory_form_action_dropdown_btn').val($(this).val());

		// update dropdown
		$(this).html(prevActionNumTxt);
		$(this).val(prevActionNumVal);
	});
	$('#title_action_btn').click(function() {
		clearOptionsStatuses();
		optionAction('title');
	});
	$('#poll_type_action_btn').click(function() {
		clearOptionsStatuses();
		optionAction('poll_type');
	});
	$('#dept_action_btn').click(function() {
		clearOptionsStatuses();
		optionAction('dept');
	});
	$('#notice_action_btn').click(function() {
		clearOptionsStatuses();
		optionAction('notice');
	});
	$('#voting_options_action_btn').click(function() {
		clearOptionsStatuses();
		optionAction('voting_options');
	});
	$('#voting_options_remove_select').change(function() {
		updateVotingOptionsInput();
	});
	// Now that the page has loaded, store the first notice that appears and prepare for changes.
	// Changes to the notice selection dropdown will cause different notices to appear besides
	// the dropdown in a text area for the user to see
	var prevNotice = '';
	var prevNoticeId = $("#notice_remove_select").val();
	$("#notice_remove_select").change(function() {
		prevNotice = '#options_notice' + prevNoticeId;
		//console.log(this);
		var nextNotice = '#options_notice' + this.value;
		//console.log('nextNotice:'+nextNotice);
		$(prevNotice).removeClass('show').addClass('hidden')
		$(nextNotice).removeClass('hidden').addClass('show').fadeIn("slow");
		prevNoticeId = this.value;
	});

}); //******************             End (document).ready()                    *******************//
//********************** Start of create poll page javascript/jquery functions *******************//
// If the document is clicked somewhere while custom menu is shown
	$(document).on("mousedown", function (e) {
	    // If the clicked element is not the menu
	    if (!$(e.target).parents(".custom-menu").length > 0) {
	        // Hide the menu
	        $(".custom-menu").hide(100);
	    }
	});
function acceleratedChoiceChange(choice) {
	// $('#')
}
function timeOutReload() {
    	location.reload(true);
};
function removePollAction() {
	var actionDiv = $(this).parent().parent().parent().parent();
	$(actionDiv).remove();
	// rename actions before removing
	renameActions();
};
function renameActions() {
	var actionNum = 1;
	$('div[name^="action"]').each(function(index, elem) {
		$(elem).attr('name','action'+actionNum);
		actionNum++;
	});
}
function setupPoll() {
	storePollId();
	setPollType();
	setDept();
	setProfTitle();
	setForms();
};
function hideEvaluationDivs() {
	$('#assistantEvaluationDiv').hide();
	$('#associateEvaluationDiv').hide();
	$('#fullEvaluationDiv').hide();
}
function createProfField(form,profName) {
	// Create hidden input to store a voting professors's name and comment
		var prof = document.createElement("input");
	prof.setAttribute("type", "hidden");
	prof.setAttribute("name", form+"Form");
	prof.setAttribute("id", profName);
	document.getElementById("votingInfo").appendChild(prof);
};
function checkForSelectedProf(profObj) {
	var professor = profName  = null;
	var alreadySelected = {};
	$(profObj).each(function(index,elem) {
		professor = $(profObj).eq(index);
		profName = $(professor).val();
		$('#votingInfo input[type="hidden"][name$="Form"]').each(function (index,elem) {
			if(profName === $(elem).attr('id') && !alreadySelected[profName]) {
				alreadySelected[profName] = true;
			}
		});
	});
	return alreadySelected;
}
function storePollId() {
	var id = <?php if(isset($pollData['poll_id'])) { echo json_encode($pollData['poll_id']); } else { echo -1; } ?>;
	// console.log("pollId: "+id);
	var pollId = document.createElement("input");
	pollId.setAttribute("type","hidden");
	pollId.setAttribute("id", "pollId");
	pollId.setAttribute("value",id);

	document.getElementById("votingInfo").appendChild(pollId);
};
function changeActionButtons(actionCount) {
	var i = 0;
	var actionDiv = actionBtn = actionIcon = null;
	for(i; i < actionCount; i++) {
		// get action button and action button icon from action div
		actionDiv = $('div[name^="action"]').eq(i);
		actionBtn = $(actionDiv).find('#actionButton');
		console.log(actionBtn);
		actionIcon = $(actionDiv).find('#actionIcon');
		console.log(actionIcon);
		// make changes to reflect change in the number of actions
		$(actionBtn).attr("class","btn btn-danger delAction");
		$(actionIcon).attr("class","glyphicon glyphicon-minus");
		$(actionBtn).off('click',clonePollAction);
		$(actionBtn).on('click',removePollAction);
	}
}
function assignForm(form,profObj) {
	var professor = profName = option = null;
	var notice = 'The following professors have already been assigned forms: ';
	var alreadySelected = checkForSelectedProf(profObj);
	var notAdded = [];
	$(profObj).each(function(index,elem) {
		professor = $(profObj).eq(index);
		profName = $(professor).val();
		if(!alreadySelected[profName]) {
			option = '<option name="'+form+'Form">'+profName+'</option>'
			// $('#votingInfo').append(hiddenOption);
			if(form == 'regular') {
				$('#regularFormGroup').append(option);
			} else {
				$('#advisoryFormGroup').append(option);
			}
			createProfField(form,profName);
		} else {
			notAdded.push(profName);
		}
	});
	if(notAdded.length) {
		var notAddedStr = '';
		var i = 0;
		for(i; i < notAdded.length; i++) {
			if(i == notAdded.length - 1) {
				notAddedStr += notAdded[i];
			} else {
				notAddedStr += notAdded[i] + ', ';
			}
		}
		notice += notAddedStr;
		$('#selectedProfsNotice').attr('class','text-danger').text(notice);
	}
}; // End of addToSelected()
function clonePollAction() {
	var count = $("[name='fromTitle']").length;
	////console.log(count);
	var tmpActionData = [];
	var EMPTY = { fromTitle: "", fromStep: "", toTitle: "", toStep: "", acceleratedVal: 'no', acceleratedTxt: "No", dropdownTxt: 'Yes', dropdownVal: 'yes'};
	var ACTION1_INDEX = 0;
	var ACTION2_INDEX = 1;
	var ACTION3_INDEX = 2;

	if(count < 3) {
		switch(count) {
			case 1:
				// Cloning causes data to be copied as well, so erase
				tmpActionData = getActionData(ACTION1_INDEX);
				$('div[name="action1"]').clone(true).insertAfter('div[name="action1"]');
				$('div[name^="action"]:last').attr('name','action2');
				setActionFields(tmpActionData,ACTION1_INDEX);
				setActionFields(EMPTY,ACTION2_INDEX);
				break;
			case 2:
				// cloning action causes a deep clone (field and data) to be copied
				// So save previous data, clone, then replace clone data with saved data
				tmpActionData = getActionData(ACTION2_INDEX);
				$('div[name="action2"]').clone(true).insertAfter('div[name="action2"]');
				console.log($('div[name^="action"]:last'));
				$('div[name^="action"]:last').attr('name','action3');
				setActionFields(tmpActionData,ACTION2_INDEX);
				setActionFields(EMPTY,ACTION3_INDEX);
				break;
			default:
				$('div[name^="action"]:last').clone(true).insertAfter('div [name^="action"]:last');
		} // End of switch
	} // End of if(count < 3)
	changeActionButtons(count);
}
function getActionCount() {
	var count = 0;
	$('[name="fromTitle"').each(function() {
		count += 1;
	});
	return count;
}
function getActions() {
	var fromStep = fromLevel = toStep = toLevel = accelerated = actions = [];
	$('[name="fromTitle"]').each(function(index) {
		actions[index] = {
			fromTitle : $('[name="fromTitle"]').eq(index).val(),
			fromStep : $('[name="fromStep"]').eq(index).val(),
			toTitle: $('[name="toTitle"]').eq(index).val(),
			toStep: $('[name="toStep"]').eq(index).val(),
			accelerated : $('[name="accelerated_action_btn"]').eq(index).val()
		};
	});
	return actions;
}
// Store action data in array before cloning to prevent data loss
function getActionData(index) {
	actionData = {
		fromTitle: $('[name="fromTitle"]').eq(index).val(),
		fromStep: $('[name="fromStep"]').eq(index).val(),
		toTitle: $("[name='toTitle']").eq(index).val(),
		toStep: $('[name="toStep"]').eq(index).val(),
		accelerated: $('#action [name="accelerated"]').eq(index).val()
	};
	//console.log("index: " + index + " accelerated: " + actionData['accelerated']);
	return actionData;
}
function setProfTitle() {
	var profTitle = <?php if(isset($pollData['profTitle'])) { echo json_encode($pollData['profTitle']); } else { echo 0; } ?>;
	//console.log("profTitle: "+profTitle);
	if(profTitle) { $('#profTitle').val(profTitle); }
};

function setDept() {
	var dept = <?php if(isset($pollData['dept'])) { echo json_encode($pollData['dept']); } else { echo 0; } ?>;
	// console.log("dept: "+dept);
	if(dept) { $('#dept').val(dept); }
};

function setPollType() {
	var type = <?php if($pollType) { echo json_encode($pollType); } else { echo 0; } ?>;
	// console.log("type: "+type);
	if(type) { $('#pollType').val(type); }
};
// Clears the input fields of the newly cloned action fields
function setActionFields(actionData,actionIndex) {
	console.log('index: '+actionIndex+' action: '+JSON.stringify(actionData));
	$('[name="fromTitle"]').eq(actionIndex).val(actionData['fromTitle']);
	$('[name="fromStep"]').eq(actionIndex).val(actionData['fromStep']);
	$("[name='toTitle']").eq(actionIndex).val(actionData['toTitle']);
	$('[name="toStep"]').eq(actionIndex).val(actionData['toStep']);
	$('[name="accelerated_action_dropdown"] li a').eq(actionIndex).html(actionData['dropdownTxt']);
	$('[name="accelerated_action_dropdown"] li a').eq(actionIndex).data('value',actionData['dropdownVal']);
	$('[name="accelerated_action_btn"]').eq(actionIndex).text(actionData['acceleratedTxt']);
	$('[name="accelerated_action_btn"]').eq(actionIndex).val(actionData['acceleratedVal']);
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
		// console.log("asst eval num: "+evaluationNum);
		$('#assistantEvaluationNum').val(evaluationNum);
		$('#assistantEvaluationDiv').show();
	}
	if(associateForm == EVALUATION) {
		evaluationNum = <?php if(isset($pollData['associateEvaluationNum'])) { echo $pollData['associateEvaluationNum']; } else { echo 0; } ?>;
		// console.log("assoc. eval. num: "+evaluationNum);
		$('#associateEvaluationNum').val(evaluationNum);
		$('#associateEvaluationDiv').show();
	}
	if(fullForm == EVALUATION) {
		evaluationNum = <?php if(isset($pollData['associateEvaluationNum'])) { echo $pollData['associateEvaluationNum']; } else { echo 0; } ?>;
		// console.log("full eval. num: "+evaluationNum);
		$('#fullEvaluationNum').val(evaluationNum);
		$('#fullEvaluationDiv').show();
	}
};

function pollAction(sendFlag) {
	var MERIT = "Merit";
	var OTHER = "Other";
	var PROMOTION = "Promotion";
	var REVIEW = "Fifth Year Review";
	var APPRAISAL = "Fifth Year Appraisal";
	var REAPPOINTMENT = "Reappointment";

	var CONFIDENTIAL_EVAL = "Confidential Evaluation";
	var ACTION_NUM_TOO_LARGE_ERROR = "Evaluation action number greater than number of actions.";
	var SELECT_ACTION_NUM_ERROR = "Please select an action number";
	//alert("in savePoll")
	var actions = [];
	// Grab all input field data
	var title = $('#title').val();
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
	var otherPollTypeInput = $('#otherPollTypeInput').val();
	var advisoryFormAction = $('#advisory_form_action_dropdown_btn').val();
	var numActions = getActionCount();
	// Setting All flags
	var validTitle = validDescr = validAct = validDeact = validDateEff = false;
	var validPollType = validDept = validData = validEvaluationNum = false;
    var validTitle = validDescr = validAct = validDeact = validEffDate = false;
    var validName = validPollType = validData = validOtherPollType = false;
    var validAssistantEvalNum = validAssociateEvalNum = validFullEvalNum = formError = false;

    if(pollTypeText != REVIEW || pollTypeText != APPRAISAL || pollTypeText != REAPPOINTMENT) {
    	if(advisoryFormAction <= numActions) {
    		$('#assignFormsNotice').attr('class','').html('');
    		validEvaluationNum = true;
    	} else {
    		$('#assignFormsNotice').attr('class','text-danger').html(' * Advisory form action is greater than number of actions.');
    	}
    	actions = getActions();
    }

    if(title.length == 0) {
        $("#titleErr").attr('class','text-danger').html('* Title required');
    } else {
    	$("#titleErr").attr('class','').html('');
    	validTitle = true;
    }
    if(dateActive.length == 0) {
        $("#dateActErr".attr('class','text-danger').html('* Date required');
    } else {
    	$("#dateActErr").attr('class','').html('');
    	validActDate = true;
    }
    if(dateDeactive.length == 0) {
        $("#dateDeactErr").attr('class','text-danger').html('* Date required');
    } else {
    	$("#dateDeactErr").attr('class','').html('');
    	validDeactDate = true;
    }
    if(effDate.length == 0) {
        $("#effDateErr").attr('class','text-danger').html('* Date required');
    } else {
    	$("#effDateErr").attr('class','').html('');
    	validEffDate = true;
    }
    if(pollTypeTxt.length == 0) {
        $("#pollTypeErr").attr('class','text-danger').html('* Poll type required');
    } else {
    	$("#pollTypeErr").attr('class','').html('');
    	validPollType = true;
    }
    // If dept = other then there is additional input available
    if(profName.length == 0) {
        $('#profNameErr').attr('class','text-danger').html('* Name required');
    } else {
    	$('#profNameErr').attr('class','').html('');
    	validName = true;
    }
	if(pollTypeTxt == 'Other') {
		if(otherPollTypeInput.length == 0) {
			$('#otherPollTypeError').attr('class','text-danger').html('* Poll type required');
		} else {
			$('#otherPollTypeError').attr('class','').html('');
			validOtherPollType = true;
		}
	}
	// if(pollTypeTxt == 'Promotion' || pollTypeTxt == 'Merit' || pollTypeTxt == 'Other') {
	// 	actions = getActions();
	// }

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
    	$('#formNotice').attr('class','text-danger').html('* An error occured while processing this poll.');
    	console.log("valid title: "+ validTitle + " actDate: " + validActDate + " validDeactDate: " + validDeactDate);
    	console.log("validEffDate: " + validEffDate + "pollType: " + validPollType + " validName: " + validName);
    	console.log("validEvaluationNum: " + validEvaluationNum);
    } else {
    	$('#formNotice').attr('class','').html('');
    }

    if(validData) {
        // Create pollData object
        var _pollData = { title: title,
            actDate: dateActive,
            deactDate: dateDeactive,
			effDate: effDate,
			pollNotice: pollNotice,
			profTitle: profTitle,
            pollType: pollTypeVal,
			otherPollTypeInput: otherPollTypeInput,
			votingOptions: voteOptions,
			dept: dept,
            name: profName,
            advisoryAction: advisoryFormAction,
            sendFlag: sendFlag
	     }; // end of _pollData

	    var _votingInfo = { };
	    var regularFormGroup = advisoryFormGroup = [];
		var id = val = name = '';

		// Iterate through hidden input fields and store the input field into
		// an associative array for posting
		$('#votingInfo').children('input:hidden').each(function() {
				// Store all hidden input field data into an votingInfo object
				id = $(this).attr("id");
				val = $(this).val();
				name = $(this).attr('name');

				if(id == "pollId") {
					_pollData[id] = val;
				} else if (name == 'profIds') {
					_votingInfo[name] = val;
				} else {
					if(name == 'regularForm') {
						regularFormGroup.push(id);
					} else if(name == 'advisoryForm') {
						advisoryFormGroup.push(id);
					}
				}
		});
		_votingInfo['regularFormGroup'] = regularFormGroup;
		_votingInfo['advisoryFormGroup'] = advisoryFormGroup;
	}

	// Post data
	console.log(_votingInfo );
	console.log( _pollData );
	validData = false;
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
//********************** End of Create poll page javascript/jquery functions *********************//
//********************** Start of Add option page javascript/jquery functions ********************//

// creates an array of voting options <input type="text">'s specified by the user
// on the user on the vote.php#options page
function getNewVotingOptions() {
	var options = [];
	var selector = option = null;
	for(var i=1; i <= 4; i++) {
		selector = '#voting_options_' + i;
		option = $(selector).val();
		option = option.trim();
		if(option && option.length > 0) {
			options[i] = option;
		}
	}
	return options;
}
// these are the id's of the <select> elements on the create polls page (vote.php#)
function getCreatePollSelector(optionType) {
	switch (optionType) {
		case 'poll_type':
			return '#pollType';
			break;
		case 'dept':
			return '#dept';
			break;
		case 'title':
			return '#profTitle';
			break;
		case 'notice':
			return '#pollNotices';
			break;
		case 'voting_options':
			return '#voteOptions';
			break;
	}
}
// these are the id's of the <select> elements on the options page (vote.php#options)
function getOptionsSelector(optionType) {
	switch (optionType) {
		case 'poll_type':
			return '#poll_type_remove_select';
			break;
		case 'dept':
			return '#dept_remove_select';
			break;
		case 'title':
			return '#title_remove_select';
			break;
		case 'notice':
			return '#notice_remove_select';
			break;
		case 'voting_options':
			return '#voting_options_remove_select';
			break;
	}
}
// clear the 4 voting options <inputs>'s
function clearVotingOptionsInput() {
	var selector = '';
	for(var i=1; i <= 4; i++) {
		selector = '#voting_options_' + i;
		$(selector).val('');
	}
}
function clearOptionsStatuses() {
	var options = ['title','poll_type','dept','notice','voting_options'];
	for(var i=0; i < options.length; i++) {
		$('#options_'+options[i]+'_status').text('');
	}
}
// display the current voting options that are displayed int eh dropdown on the options page: vote.php#options
// the voting options are diplayed, one option in every input
function updateVotingOptionsInput() {
	var options = $('#voting_options_remove_select option:selected').text();
	options = options.split(",");
	// console.log(options);

	var optionIndex = 1;
	var optionSelector = '';
	for(var i=0; i < options.length; i++) {
		optionSelector = '#voting_options_' + optionIndex;
		// console.log(optionSelector);
		$(optionSelector).val(options[i]);
		optionIndex++;
	}
	if(options.length < 4) {
		$('#voting_options_4').val('');
	}
}
// combines the text of the 4 voting options <input>'s
// into one string for storage and for displaying to user
function newVotingOptionsToString(options) {
	var optionsStr = '';
	//console.log('voting options: '+options+' length: '+options.length);
	for(var i=1; i <= options.length; i++) {
		if(options[i] && options[i].length > 0) {
			//console.log('options['+i+']: '+options[i]);
			if(i == options.length-1) {
				optionsStr += options[i];
			} else {
				optionsStr += options[i] + ',';
			}
		}
	}
	return optionsStr;
}
// displays either an error or a success message to the user regarding
// adds or removals of options: titles, departments, poll types, notices, voting options
function displayOptionActionStatus(action,error=null) {
	var option = optionType = successfulAddMsg = successfulRemoveMsg = null;
	var statusMsg = optionStatusSelector = null;

	// create status selector
	optionType = action['optionType'];
	optionStatusSelector = '#options_' + optionType + '_status';

	if(error) {
		$(optionStatusSelector).attr('class','text-danger');
		$(optionStatusSelector).text(error);
	} else {
		// console.log(option);
		option = action['option'];
		optionType = action['optionType'];

		// clear previous status
		$(optionStatusSelector).text('');

		// create part of generic success msg
		successfulAddMsg = 'Successfully added '+ optionType + ': ';
		successfulRemoveMsg = 'Successfully removed ' + optionType + ': ';

		// create status msg
		statusMsg;
		if(action['actionType'] === 'add') {
			if(optionType === 'notice') {
				statusMsg = successfulAddMsg + option['noticeName'] + ' -> ' + option['notice'];
			} else {
				statusMsg = successfulAddMsg + option;
			}
		} else {
			statusMsg = successfulRemoveMsg + action['removedOptionText'];
		}
		// display error if there is one, else display success msg to give user feedback
		$(optionStatusSelector).attr('class','text-success');
		switch(optionType) {
			case 'dept':
			case 'title':
			case 'poll_type':
				$(optionStatusSelector).text(statusMsg);
				break;
			case 'notice':
				$(optionStatusSelector).text(statusMsg);
				break;
			case 'voting_options':
				$(optionStatusSelector).text(statusMsg);
				break;
			default:
				alert('Error: displayOptionActionSuccess; Please contact systems@engr.ucr.edu if problem persists');
		}
	}
}
// optionType = {title, dept, poll_type,notice,voting_options}
// action = {add, remove}
// This function changed the inputs of each option type on the options page: vote.php#options
// When the user decides to remove an option, then the "add" button will be replaced with "remove"
// and the "+" button replaced with "-". Similarly when switching from "add" to "remove", the <input>
// will change to a <select> for the user to decide which options to remove.
function optionsActionChange(optionType,action) {
	// caret icon for dropdown
	var caret = '  <span class="caret"></span>';

	// create selectors for buttons
	var button = '#' + optionType + '_dropdown_btn';
	var actionButton = '#' + optionType + '_action_btn';
	var actionGlyphicon = '#' + optionType + '_action_glyphicon';

	// create selectors for the inputs
	var optionInput = '#new_' + optionType;
	var optionSelect = '#' + optionType + '_remove_select';

	// console.log('actionButton: '+actionButton+' actionGlyphicon: '+actionGlyphicon);
	// store previous button text and values that
	// will replace the action text and value
	var oldActionTxt = $(button).text();
	var oldActionVal = $(button).val();
	console.log('old text: '+oldActionTxt+' val: '+oldActionVal);

	// store action value for changing button
	var actionVal = $(action).data('value');
	var actionTxt = $(action).text();
	console.log('action text: '+actionTxt+' val: '+actionVal);
	// update button with user action
	$(button).html(actionTxt + caret);
	$(button).val(actionVal);

	// createPoll_action is just used to switch the button's text from No/Yes i.e all there is no actionVal = 'add' or 'remove'
	if(optionType != 'createPoll_action') {
		if(actionVal == 'add') {
			// console.log(actionButton+' '+actionGlyphicon);
			$(actionButton).removeClass('btn-danger').addClass('btn-success');
			$(optionSelect).removeClass('show').addClass('hidden');
			if(optionType == 'voting_options') {
				$('#votingOptionsHelpText').removeClass('hidden').addClass('show');
				clearVotingOptionsInput();
			} else if(optionType == 'notice') {
				$('#new_notice_name').removeClass('hidden').addClass('show');
				$('#optionsNoticeTextDiv').removeClass('show').addClass('hidden');
				$('#new_notice_text').removeClass('hidden').addClass('show');
			} else {
				$(optionInput).removeClass('hidden').addClass('show');
			}
			$(actionGlyphicon).removeClass('glyphicon-minus').addClass('glyphicon-plus');
		} else if(actionVal == 'remove') {
			$(actionButton).removeClass('btn-success').addClass('btn-danger');
			if(optionType == 'notice') {
				$('#new_notice_name').removeClass('show').addClass('hidden');
				$(optionSelect).removeClass('hidden').addClass('show');
				$('#new_notice_text').removeClass('show').addClass('hidden');
				$('#optionsNoticeTextDiv').removeClass('hidden').addClass('show');
			} else {
				if(optionType == 'voting_options') {
					updateVotingOptionsInput();
					$('#votingOptionsHelpText').removeClass('show').addClass('hidden');
				} else {
					$(optionInput).removeClass('show').addClass('hidden');
				}
				$(optionSelect).removeClass('hidden').addClass('show');
			}
			$(actionGlyphicon).removeClass('glyphicon-plus').addClass('glyphicon-minus');
		}
	}
	// update dropdown
	$(action).html(oldActionTxt);
	$(action).data('value',oldActionVal);
}
// either add or remove <select> options (<option>) on the create page (vote.php#)
// as well as the options page (vote.php#options)
function editSelectOptions(action,id=null) {
	var createPollSelector = getCreatePollSelector(action['optionType']);
	var optionsSelector = getOptionsSelector(action['optionType']);
	var newOption = removeOption = option = newOptionHtml = null;

	if(action['actionType'] === 'add') {
		if(action['optionType'] === 'notice') {
			option = action['option'];
			// create html codes for the new notice name and notice text
			// new option name html belongs to the notice_remove_select on the options page: vote.php#options
			newOptionNameHtml = '<option value="'+id+'">'+option['noticeName']+'</option>';

			// new option textarea html belongs on the options page
			newOptionTextareaHtml = '<textarea id="options_notice' + id + '" class="form-control hidden">';
			newOptionTextareaHtml += option['notice'] + '</textarea>';

			// new option text belongs on the create polls page
			newOptionTextHtml = '<p id="notice'+id+'" class="hidden">'+option['notice']+'</option>';

			// update the create poll page: vote.php#
			$('#pollNotices').append(newOptionNameHtml);
			$('#noticeTextDiv').append(newOptionTextHtml);

			// update the options page: vote.php#options
			$('#notice_remove_select').append(newOptionNameHtml);
			$('#optionsNoticeTextDiv').append(newOptionTextareaHtml);
		} else {
			// create new option to add to the selections
			newOption = '<option id="'+id+'">' + action['option'] + '</option>';

			//console.log('create selector: '+createPollSelector);
			//console.log('options selector: '+optionsSelector);
			//console.log('new option: '+newOption);

			// add option to the selections
			$(createPollSelector).append(newOption);
			$(optionsSelector).append(newOption);
		}
	} else {
		// this is the option to remove, identified by value
		removeOption = ' option[value="'+action['option']+'"]';

		// removing option from select on vote.php#
		$(createPollSelector+removeOption).remove();
		// removing option from select on vote.php#options page
		$(optionsSelector+removeOption).remove();

		// update voting options input to reflect the voting_option that is currently selected
		// after removing he previous options selected
		if(action['optionType'] === 'voting_options') {
			updateVotingOptionsInput();
		} else if(action['optionType'] === 'notice') {
			// remove notice text, if removing a notice
			// removing notice text from vote.php
			$('#notice'+action['option']).remove();
			// removing notice text from vote.php#options page
			$('#options_notice'+action['option']).removeClass('show').addClass('hidden');
			$('#options_notice'+action['option']).remove();
			// replace the textarea on the options page (vote.php#options) with the
			// current notice on focus
			option = $('#notice_remove_select').val();
			$('#options_notice'+option).removeClass('hidden').addClass('show');
		}
	}
}
function optionAction(optionType) {
	// create action option button selectors
	var actionButton = '#' + optionType + '_dropdown_btn';
	var actionType = $(actionButton).val();
	var option = removedOptionText = null;
	var validOptionAction = false;

	var action = {actionType: actionType, optionType: optionType, option: option};

	if(actionType == 'add') {
		// get user input
		if(optionType == 'notice') {
			// get notice name and notice text
			var noticeName = $('#new_notice_name').val();
			noticeName = noticeName.trim();
			var notice = $('#new_notice_text').val();
			notice = notice.trim();

			// make sure there is valid input
			if(notice.length == 0 || noticeName.length == 0) {
				displayOptionActionStatus(action,'Missing: Notice name or Notice text');
			} else {
				option = {noticeName: noticeName, notice: notice};
				validOptionAction = true;
			};
		} else if(optionType == 'voting_options') {
			option = getNewVotingOptions();
			option = newVotingOptionsToString(option);
			option = option.trim();

			if(option.length == 0) {
				displayOptionActionStatus(action,'Missing: at least 2 voting options');
			} else {
				validOptionAction = true;
			}
		} else {
			option = $('#new_' + optionType).val();
			option = option.trim();
			if(option.length == 0) {
				var optionTypeUpper = optionType.charAt(0).toUpperCase() + optionType.slice(1);
				displayOptionActionStatus(action,'Missing: input for '+optionTypeUpper);
			} else {
				validOptionAction = true;
			}
		}
	} else {
		// action is valid when removing since information is from database and not user
		validOptionAction = true;
		// user is removing option
		option = $('#'+optionType+'_remove_select').val();
		removedOptionText = $('#'+optionType+'_remove_select option[value="'+option+'"]').text();
	}
	// package all required action info.
	if(validOptionAction) {
		action['option'] = option;
		// console.log('action: '); console.log(action);
		$.post('event/optionAction.php', { action: action }
			, function (data) {
				if(data) {
					var returnData = JSON.parse(data);
					if(returnData.status === "success") {
						// begin updating select options
						if(actionType === 'add') {
							// a new option in database means a new id is created
							// so use id when adding <option>
							//console.log('add return id: '+returnData.id);
							editSelectOptions(action,returnData.id);
						} else {
							// removing an option; action has all necessary info.
							editSelectOptions(action);
							// add the text of the removed option to the action
							// removed text will be displayed to user as
							// a signal that the removal was success
							action['removedOptionText'] = removedOptionText;
						}
						// display successful action msg
						displayOptionActionStatus(action);
						clearOptionInput(action);
					} else {
						// display action error msg
						displayOptionActionStatus(action,data);
					}
				}
			})  // if there are any server issues, this will capture the error
				.fail(function(error,status) {
					var error = {error: error, status: status};
					error = JSON.stringify(error);
					displayOptionActionStatus(action,error);
			});
	}
}
//*********************** End of Add options javascript/jquery functions *************************//
</script>
<!-- End of javascript/jquery -->
</body>
</html>
