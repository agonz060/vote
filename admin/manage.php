<?php
	session_start();
	require_once "includes/connDB.php";
	require_once "includes/sessionHandling.php";
	// if(!isAdmin()) {
 //        signOut();
 //    }
?>
<html>
<head>
<title>BCOE Voting - Manage Users</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<style>
    .error {color: #FF0000;}
    .center-div {
    	margin: 0 auto;
    	width: xx %;
    }
</style>
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="home.php">BCOE Voting</a>
			</div>
			<ul class="nav navbar-nav">
				<li><a href="home.php">Home</a></li>
				<li><a href="vote.php">Create Poll</a></li>
				<li><a href="edit.php">Edit Poll</a></li>
				<li><a href="review.php">Review Poll</a></li>
				<li class="active"><a href="#">Manage Users</a></li>
			</ul>
		</div>
	</nav>

	<div class="container well">
		<div class="center-block" style="width: 80%">
			<ul class="nav nav-tabs nav-justified">
				<li class="active"><a data-toggle="tab" href="#invite">Invite Users</a></li>
				<li><a data-toggle="tab" href="#addUser">Add User</a></li>
			</ul>
		</div>

		<div class="tab-content center-block" style="width: 70%;">
			<!-- invite tab content -->
			<div id="invite" class="tab-pane fade in active">
					<br>
					<p> <span id="inviteProfSuccessSpan" class="label label-success" ></span>
						<span id="inviteProfErrorSpan" class="label label-danger"></span>
						<span id="inviteProfTestingSpan" class="label label-info"></span>
					</p>
					<p> Email recipient will receive a link to complete their registration. </p>
					<div class="form-group">
				      <label for="inviteEmail">Email:</label>
				      <input type="email" class="form-control" id="inviteEmail" placeholder="email@ucr.edu, ..." name="inviteEmail">
				    </div>
				    <div class="form-inline">
				    	<div class="form-group actionGroup">
					    	<label for="inviteTitle">Title:</label>
						    <select class="form-control" id="inviteTitle">
						        <?php
									$selectStmt = "SELECT title from titles";
									if($result = mysqli_query($conn,$selectStmt)) {
										while($row = mysqli_fetch_assoc($result)) {
											echo "<option value=\"".$row['title']."\">".$row['title']."</option>";
										}
										echo "<option value=\"Administrator\">Administrator</option>";
									}
						        ?>
						    </select>
						</div>
						<!-- &nbsp;&nbsp;
						<div class="form-group actionGroup">
							<button type="button" class="btn btn-success addAction"><span id="actionIcon" class="glyphicon glyphicon-plus"></span></button>
						</div> -->
						&nbsp;&nbsp;
						<div class="form-group actionGroup">
				    		<button type="button" class="btn btn-success" id="inviteSendButton">Send</button>
				    	</div>
				    </div>
			</div> <!-- End of invite pane -->
			<!-- addUser tab content -->
			<div id="addUser" class="tab-pane fade in">
				<br>
				<p> <span id="addUserSuccessSpan" class="label label-success" ></span>
					<span id="addUserErrorSpan" class="label label-danger"></span>
					<span id="addUserTestingSpan" class="label label-info"></span>
				</p>
				<h2 class="form-signin-heading">Register</h2>
				<div class="form-group">
					<input class="form-control" id="addUserFirstName" placeholder="First Name" value="">
				</div>
				<div class="form-group">
					 <input class="form-control" id="addUserLastName" placeholder="Last Name" value="">
				</div>

				<!-- Select title -->
				<div class="form-group actionGroup">
			    	<label class="sr-only" for="addUserTitle"></label>
				    <select class="form-control" id="addUserTitle">
						<?php
							$selectStmt = "SELECT title from titles";
							if($result = mysqli_query($conn,$selectStmt)) {
								while($row = mysqli_fetch_assoc($result)) {
									echo "<option value=\"".$row['title']."\">".$row['title']."</option>";
								}
								echo "<option value=\"Administrator\">Administrator</option>";
							}
					    ?>
					</select>
				</div>
				<div class="form-group actionGroup">
					 <input class="form-control" id="addUserEmail" placeholder="Email" value="">
				</div>
				<div class="form-group actionGroup">
					 <input class="form-control" id="addUserPass1" placeholder="Enter Password">
				</div>
				<div class="form-group actionGroup">
					 <input class="form-control" type="password" id="addUserPass2" placeholder="Confirm Password">
				</div>
				<!-- Submit information if all required input is valid -->
				<button type="button" id="addUserButton" class="btn btn-success">Submit</button>
				<!-- Form ends here -->
				</form>
				</div>
			</div><!-- End of add user pane -->
		</div><!-- End of tab content -->
	</div><!-- End of container -->
</body>
</html>

<script>
$("#addUserButton").click(function() {
	var fName = $("#addUserFirstName").val();
	var lName = $("#addUserLastName").val();
	var title = $("#addUserTitle option:selected").text();
	var email = $("#addUserEmail").val();
	var pass1 = $("#addUserPass1").val();
	var pass2 = $("#addUserPass2").val();

	// add.php contains functions that adds user by passing along the following parameters
	$.post("add.php", { firstName: fName, lastName: lName, email: email,
							title: title, pass1: pass1, pass2: pass2 },
			function(data) {
				if(data) {
					$("#addUserErrorSpan").text(data);
					$("#addUserSuccessSpan").text("");
				} else {
					$("#addUserErrorSpan").text("");
					$("#addUserSuccessSpan").text("User added!");
				}
			}
	);
});


$("#inviteSendButton").click(function() {
	var emails = $("#inviteEmail").val();
	var title = $("#inviteTitle option:selected").text();

	if(emails && emails.length > 0 && title && title.length > 0) {
		$.post("event/invite.php", { emails: emails, title: title },
				function(data) {
					if(data) {
						$("#inviteProfTestingSpan").text(data);
						$("#inviteProfSuccessSpan").text("");
					} else {
						$("#inviteProfTestingSpan").text();
						$("#inviteProfSuccessSpan").text("Invitation(s) sent!");
					}

				}
		);
	}
});
</script>