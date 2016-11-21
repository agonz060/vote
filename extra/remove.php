<?php session_start(); ?>
<?php

	// Store location of element to remove
	$name = $_POST["name"];
	$index = array_search($name, $_SESSION["votingProfs"]);

	// Removes selected element at index and reorders array
	$votingProfs = $_SESSION["votingProfs"];
	unset($votingProfs[$index]);
	$_SESSION["votingProfs"] = array_values($votingProfs);
	
	// Remove any professor comments
	$profCmts = $_SESSION["profCmts"];
	unset($profCmts[$name]);
	$_SESSION["profCmts"] = $profCmts;
?>
