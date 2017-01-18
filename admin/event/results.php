<?php
	require_once 'connDB.php';

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	function intToRoman($int) {
		$romanArr = array();
		$romanArr[1] = "I"; $romanArr[2] = "II"; $romanArr[3]= "III";
		$romanArr[4] = "IV"; $romanArr[5] = "V";
		return $romanArr[$int];
	}
	session_start();
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["poll_id"])) {
			$pollId = cleanInput($_POST["poll_id"]);
		}
	}
		
	
?>

