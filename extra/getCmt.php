<?php session_start(); ?>
<?php 
	// Check if a comment is associated with a professor
	$name = $_POST["name"];
	$cmts = $_SESSION["profCmts"];

	if(array_key_exists($name,$cmts)) {
		echo $cmts[$name];	
	} else { echo ""; }
?>
