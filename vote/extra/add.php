<?php session_start(); ?>
<?php
	echo "entering";
	print_r($_SESSION["votingProfs"]);
 
	$prof = array($_POST["name"]);
	$_SESSION["votingProfs"] = array_merge($prof,$_SESSION["votingProfs"]);

	#testing
	print_r($_SESSION["votingProfs"]);
?>
