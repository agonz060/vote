<?php session_start(); ?>
<?php 
	# Check if post was correctly made before accessing post variables	
	if($_POST["comment"]) {
		# Set session variable to store professor comment
		$name = $_POST["name"];
		$cmt = $_POST["comment"];
	
		$profCmt = array($name => $cmt);
		if(empty($_SESSION["profCmts"])) {
			$_SESSION["profCmts"] = $profCmt;
		} else {
			$_SESSION["profCmts"] = array_merge($_SESSION["profCmts"],$profCmt);	
		}
	}
?>
