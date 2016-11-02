<?php session_start(); ?>
<?php 
	# Check if post was correctly made before accessing post variables	
	if($_POST["comment"]) {
		# Set session variable to store professor comment
		$email = $_POST["email"];
		
		# Note: $_SESSION["cmts"] = array("email" => "comment")
		# Assign a comment to the professor
		$comments = $_SESSION["cmts"];
		$comments[$email] = $_POST["comment"];
		
		$_SESSION["cmts"] = $comments;	
	}
?>
