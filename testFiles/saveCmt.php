<?php session_start(); ?>
<?php
	if($_POST["comment"]) {
		$_SESSION["cmts"] =  $_POST["comment"];
	}
?>
