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
.heading-options, .heading-actions, .align-create-poll-buttons {
	padding-left: 15px;
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
a.options {
	color: black !important;
}
a:hover.options   {
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
			<h2 class="form-heading text-left">Create Poll<small id="votingInfo"></small>
			</h2>
			<hr class="hr-title">
			<form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<!-- Row 1 -->
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
				<!-- Row 2 -->
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
				<!-- Row 3 -->
				<div class="row">
					<h4 id="actionsHeading" class="heading-actions hidden"><label><u>Actions</u></label><small name="actionError"></small></h4>
					<div id="action" class="hidden">
						<div class="form-group form-inline col-md-12">
							<label for="fromTitle"><u>From</u></label>
							<label class="col-md-offset-4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>To</u></label>
							<label class="col-md-offset-4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>Accelerated</u></label>
							<div class="form-group actionGroup">
								<input class="form-control" name="fromTitle" type="text" maxlength="100" placeholder="Title">
								<label for="fromStep" class="sr-only">From step</label>
								<input class="form-control" name="fromStep" type="text" maxlength="3" placeholder="Step">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="toTitle" class="sr-only">To</label>
								<input class="form-control" name="toTitle" type="text" maxlength="100" placeholder="Title">
								<label for="toStep" class="sr-only">To step</label>
								<input class="form-control" name="toStep" type="text" maxlength="3" placeholder="Step">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="accelerated" class="sr-only">Accelerated</label>
								<select class="form-control" name="accelerated">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
								<button id="actionButton" type="button" class="btn btn-success addAction"><span id="actionIcon" class="glyphicon glyphicon-plus"></span></button>
							</div>
						</div> <!-- end div form-inline -->
					</div> <!-- end div action -->
				</div> <!-- end row 3  -->
				<!-- start row 7 -->
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
				<!-- <hr class="hr-body">
				 Row 4
				<div class="row hidden">
					<div class="col-md-12">
						<div class="form-group">
							<h4><label for="description"><u>Description</u></label></h4>
							<?php
								// $description = '';
								// $textareaOpen = '<textarea class="form-control" id="description" name="description" maxlength="300" rows="5" cols="70">';
								// $textareaClose = '</textarea>';
								// if(isset($_POST["description"])) {
								// 	$description = trim(htmlentities($_POST["description"]));
								// }
								// echo $textareaOpen . $description . $textareaClose;
						 	?>
						</div>
					</div>
				</div> end row 4 -->
				<hr class="hr-body">
				<!-- start row 5 -->
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
				<!-- start row 6 -->
				<div class="row">
					<div class="col-md-4">
						<div id="pollNoticesDiv" class="form-group" >
							<h4><label for="pollNotices"><u>Notice</u></label></h4>
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
				<!-- Selection displays the names and titles of professors -->
				<hr class="hr-body">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-5">
						<h4><u>Select Professors</u></h4>
						<div class="form-group">
							<label for="profSel">All Professors</label>
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
					</div>
					<div class="col-xs-12 col-sm-12 col-md-7">
						<h4 class="col-md-offset-1"><a id="assignFormsHeading" name="assignFormsHeading" class="options"><u>Assign Forms</u></a></h4>
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
												<option>Professor 1GoesHere</option>
												<option>Professor 2GoesHere</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group col-xs-12 col-sm-12 col-md-12">
									<div class="row">
										<div class="form-group col-xs-1 col-sm-1 col-md-1">
											<div class="selectProfs-btn-container1 flex-container">
												<button type="button" class="btn btn-success selectProfs-btn"><span class="glyphicon glyphicon-arrow-right"></span></button>
											</div>
										</div>
										<div class="form-group col-xs-10 col-sm-10 col-md-10">
											<div class="form-group assignForms-advisoryDiv">
												<p class="assignForms-p"><b>Advisory Form</b><lable for="regularFormGroup" class="sr-only">Advisory Form</lable></p>
												<select multiple id="regularFormGroup" name="regularFormGroup" class="form-control" size="6">
													<option>Professor 1GoesHere</option>
													<option>Professor 2GoesHere</option>
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
<script>
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
$('#voting_options_remove_select').change(function() {
	updateVotingOptionsInput();
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

function clearOptionsStatuses() {
	var options = ['title','poll_type','dept','notice','voting_options'];
	for(var i=0; i < options.length; i++) {
		$('#options_'+options[i]+'_status').text('');
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
			if(!(notice && noticeName) || notice.length == 0 || noticeName.length == 0) {
				displayOptionActionStatus(action,'Missing: Notice name or Notice text');
			} else {
				option = {noticeName: noticeName, notice: notice};
				validOptionAction = true;
			};
		} else if(optionType == 'voting_options') {
			option = getNewVotingOptions();
			option = newVotingOptionsToString(option);
			option = option.trim();

			if(!option || option.length == 0) {
				displayOptionActionStatus(action,'Missing: at least 2 voting options');
			} else {
				validOptionAction = true;
			}
		} else {
			option = $('#new_' + optionType).val();
			option = option.trim();
			if(!option || option.length == 0) {
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

function clearOptionInput(action) {

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
function updateCreatePollVoteOptions(option) {

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
// clear the 4 voting options <inputs>'s
function clearVotingOptionsInput() {
	var selector = '';
	for(var i=1; i <= 4; i++) {
		selector = '#voting_options_' + i;
		$(selector).val('');
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

// Now that the page has loaded, store the first notice that appears and prepare for changes.
// Changes to the notice selection dropdown will cause different notices to appear besides
// the dropdown in a text area for the user to see
var prevNotice = '';
var prevNoticeId = $("#notice_remove_select").val();
$("#notice_remove_select").change(function() {
	prevNotice = '#options_notice' + prevNoticeId;
	//console.log(this);
	nextNotice = '#options_notice' + this.value;
	//console.log('nextNotice:'+nextNotice);
	$(prevNotice).removeClass('show').addClass('hidden')
	$(nextNotice).removeClass('hidden').addClass('show').fadeIn("slow");
	prevNoticeId = this.value;
});

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

	// store action value for changing button
	var actionVal = $(action).data('value');
	var actionTxt = $(action).text();

	// update button with user action
	$(button).html(actionTxt + caret);
	$(button).val(actionVal);

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
	// update dropdown
	$(action).html(oldActionTxt);
	$(action).data('value',oldActionVal);
}

$(document).ready(function() {
	// Hide evaluation divs until correct poll type is selected
	hideEvaluationDivs();
	// Populate form fields if user is editing a poll
    setupPoll();

	// Hide input unless selected
	$('#otherPollTypeDiv').removeClass('show').addClass('hidden');
	$('#actionsDivider').removeClass('show').addClass('hidden');
	$('#action').removeClass('show').addClass('hidden');
	$('#votingOptionsDivider').removeClass('show').addClass('hidden');
	$('#votingOptionsDiv').removeClass('show').addClass('hidden');


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

	function storePollId() {
		var id = <?php if(isset($pollData['poll_id'])) { echo json_encode($pollData['poll_id']); } else { echo -1; } ?>;
		// console.log("pollId: "+id);
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
			accelerated : $('#action select[name="accelerated"]').eq(index).val()
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
 		//console.log(1);
    	var numActions = getActionCount();
    	if(assistantFormTxt == CONFIDENTIAL_EVAL) {
    		if(assistantEvaluationNum > numActions) {
    			$('#evaluationFormError').text(ACTION_NUM_TOO_LARGE_ERROR);
    		} else if(assistantEvaluationNum == 0) {
    			$('#evaluationFormError').text(SELECT_ACTION_NUM_ERROR);
    		} else {
    			//console.log(2);
    			validAssistantEvalNum = true;
    		}
    	}
    	if(associateFormTxt == CONFIDENTIAL_EVAL) {
    		if(associateEvaluationNum > numActions) {
    			$('#evaluationFormError').text(ACTION_NUM_TOO_LARGE_ERROR);
    		} else if(associatetEvaluationNum == 0) {
    			$('#evaluationFormError').text(SELECT_ACTION_NUM_ERROR);
    		} else {
    			//console.log(3);
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
    			//console.log(4);
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
function setActionFields(actionData,actionIndex) {
	console.log('index: '+actionIndex+' action: '+JSON.stringify(actionData));
	$('[name="fromTitle"]').eq(actionIndex).val(actionData['fromTitle']);
	$('[name="fromStep"]').eq(actionIndex).val(actionData['fromStep']);
	$("[name='toTitle']").eq(actionIndex).val(actionData['toTitle']);
	$('[name="toStep"]').eq(actionIndex).val(actionData['toStep']);
	$('#action [name="accelerated"]').eq(actionIndex).val(actionData['accelerated']);
};
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
	//console.log(prevNotice);
	$("#pollNotices").change(function() {
		prevNotice = '#notice' + prevNoticeId;
		nextNotice = '#notice' + this.value;

		$(prevNotice).removeClass('show').addClass('hidden')
		$(nextNotice).removeClass('hidden').addClass('show').fadeIn("slow");
		prevNoticeId = this.value;
	});
});
function clonePollAction() {
	var count = $("[name='fromTitle']").size();
	////console.log(count);
	var tmpActionData = [];
	var EMPTY = { fromTitle: "", fromStep: "", toTitle: "", toStep: "", accelerated: 0 };
	var ACTION1_INDEX = 0;
	var ACTION2_INDEX = 1;
	var ACTION3_INDEX = 2;

	if(count < 3) {
		switch(count) {
			case 1:
				// Cloning causes data to be copied as well, so erase
				tmpActionData = getActionData(ACTION1_INDEX);
				$('#action').clone(true).insertAfter("#action:last");
				setActionFields(tmpActionData,ACTION1_INDEX);
				setActionFields(EMPTY,ACTION2_INDEX);
				break;
			case 2:
				// cloning action causes a deep clone (field and data) to be copied
				// So save previous data, clone, then replace clone data with saved data
				tmpActionData = getActionData(ACTION2_INDEX);
				//console.log('case 2: '+JSON.stringify(tmpActionData));
				$('#action').clone(true).insertAfter("#action:last");
				setActionFields(tmpActionData,ACTION2_INDEX);
				setActionFields(EMPTY,ACTION3_INDEX);
				break;
			default:
				$('#action').clone(true).insertAfter("#action:last");
		} // End of switch
	} // End of if(count < 3)
	$('#actionButton').each(function(index) {
		//console.log("index: "+index+" count:"+count);
		//console.log(this);
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
	//console.log($(this).parent().parent());
	//console.log($(this));
	$(this).parent().parent().parent().remove();
};
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
			$('#profTitleDiv').removeClass('show').addClass('hidden');
			$('#actionsDivider').removeClass('hidden').addClass('show');
			$('#actionsHeading').removeClass('hidden').addClass('show');
			$('#action').removeClass('hidden').addClass('show');
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
		$('#actionsHeading').removeClass('show').addClass('hidden');
		$('#action').removeClass('show').addClass('hidden');
		$('#otherPollTypeDiv').removeClass('show').addClass('hidden');
		$('#votingOptionsDivider').removeClass('show').addClass('hidden');
		$('#votingOptionsDiv').removeClass('show').addClass('hidden');
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
