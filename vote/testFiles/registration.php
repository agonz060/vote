<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<!-- Start PHP -->
<?php

#<!-- Define variables -->
$rewiredfirstName = $lastName = $email = $title = "";
$errFirstName = $errLastName = $errEmail = $errTitle = "";
$validFirstName = $validLastName = $validEmail = $validTitle = false;

#Set appropiate error messages if an input field is left empty 
if($_SERVER["REQUEST_METHOD"] == "POST") {
		# Check for correct email format
	if(empty($_POST["firstName"])){
	  	$errFirstName = "* First name is required";
	} else {
		$firstName = cleanInput($_POST["firstName"]);
		
		# Name should only contain letters and space character
		if(!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
			$errFirstName = "Only letters and white space allowed";		
		}
		
		# First name is valid 
		$validFirstName = true; 
	}

	if(empty($_POST["lastName"])) {
		$errLastName = "* Last name required";
	} else {
		$lastName = cleanInput($_POST["lastName"]);
		
		# Name should only contain letters and space character
 		if(!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
			$errLastName = "Only letters and white space allowed";
		}
		
		#Last name is valid
		$validLastName = true;
	}

	if(empty($_POST["email"])) {
		$errEmail = "* Email required";
	} else {
		$email = cleanInput($_POST["email"]);

		# Check for valid email
		$regEmailCheck = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
		if(!preg_match($regEmailCheck, $email)) {
			$errEmail = "*Error invalid email format"; 
		} else {
			$validEmail = true;
		} 
	}
	
	if(empty($_POST["title"])) {
		$errTitle = "* Title required";
	} else {
		$title = $_POST["title"];
		$validTitle = true;
	}
}

#<!-- Input validation for security reasons: removes special characters that could -->
#<!-- be used to inject malicious code -->
function cleanInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>
<!-- End PHP -->
	
<!-- Begin form -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<!-- Enter name and email -->
First Name: <input type="text" name="firstName">
<span class="error"> <?php echo "$errFirstName";?> </span>
<br><br>

Last Name: <input type="text" name="lastName">
<span class="error"> <?php echo "$errLastName";?> </span>
<br><br>

E-mail: <input type="text" name="email">
<span class="error"> <?php echo "$errEmail";?> </span>
<br><br>

<!-- Select title -->
Title: 
<input type="radio" name="title" value="Assistant">Assistant Professor
<input type="radio" name="title" value="Associate">Associate Professor
<input type="radio" name="title" value="Full">Full Professor
<span class="error"> <?php echo $errTitle;?> </span>
<br><br>

<!-- Submit information if all required input is valid -->
<input name="submit" type="submit" value="Submit" />
<?php 
	if($validFirstName && $validLastName && $validEmail && $validTitle) {
		// Setup variables for connection with db
		$serverName = "localhost";
		$userName = "root";
		$pwd = "on^yp6Ai";
		
		// Establish connection with database 
		$conn = new mysqli($serverName, $userName, $pwd);
		
		// Check connection
		if($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		echo "Connection with db successful!";	
	}

?> 

<!-- Form ends here -->
</form>


</body>
</html>

