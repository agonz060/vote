<?php session_start(); ?>
<?php 
	$_SESSION["profEmails"] = array();
	$test = array();

?>


<?php 
	print_r($_SESSION["profEmails"]);
	#print_r($test); 
	$test2 = array("test");
	$_SESSION["profEmails"] = array_merge($test2,$_SESSION["profEmails"]);
	$test = array_merge($test,$test2);
	print_r($_SESSION["profEmails"]);
	echo "<br>";
	print_r($test);
?>
