<html>
<head>
<title>BCOE Voting - Manage Users</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<style>
    .error {color: #FF0000;}
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

	<div class="container">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#addUser">Add</a></li>
			<li><a data-toggle="tab" href="#inviteAdmin">Invite Admin</a></li>
		</ul>

		<div class="tab-content">
			<div id="addUser" class="tab-pane fade in active">
				# to do
			</div>
			<div id="inviteAdmin" class="tab-pane fade">
				<br>
				<p> Email recipient will receive a link to complete their registration. </p>
				<div class="form-group">
			      <label for="inviteAdminEmail">Email:</label>
			      <input type="email" class="form-control" id="inviteAdminEmail" placeholder="Enter email" name="inviteAdminEmail">
			    </div>
			    <button type="button" class="btn btn-default" id="inviteAdminSendButton">Send</button>
			</div>
		</div>
	</div>


</body>
</html>