<?php session_start(); ?>

<head>
<style>
    .error {color: #FF0000;}
</style>
</head>
<body>
<!-- Start PHP -->
<?php

require 'connDB.php';

#<!-- Define variables -->
$firstName = $lastName = $email = $title = $pass1 = $pass2 = "";
$errFirstName = $errLastName = $errEmail = $errTitle = $errPass1 = $errPass2 = "";
$validFirstName = $validLastName = $validEmail = $validTitle = $validPass = false;

#Set appropiate error messages if an input field is left empty 
if($_SERVER["REQUEST_METHOD"] == "POST") {
		# Check for correct name format
	if(empty($_POST["firstName"])){
	  	$errFirstName = "* First name is required";
	} else {
		$firstName = cleanInput($_POST["firstName"]);
		
		# Name should only contain letters and space character
		if(!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
			$errFirstName = "* Only letters and white space allowed";		
		} else { 
		    # First name is valid 
		    $validFirstName = true;
                }
	}

	if(empty($_POST["lastName"])) {
		$errLastName = "* Last name required";
	} else {
		$lastName = cleanInput($_POST["lastName"]);
		
		# Name should only contain letters and space character
 		if(!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
			$errLastName = "* Only letters and white space allowed";
		} else {
		    #Last name is valid
		    $validLastName = true;
                }
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
        
        if(empty($_POST['pass1'])) {
            $errPass1 = "* Password required";
        } 

        if(empty($_POST['pass2'])) {
            $errPass2 = "** requried";
        }

        if(!empty($_POST['pass1'] && $_POST['pass2'])) {
            $tmp1 = strtolower(cleanInput($_POST['pass1'])); 
            $tmp2 = strtolower(cleanInput($_POST['pass2']));

            if($tmp1 === $tmp2) {
                $validPass = true;
            } else {
                $errPass1 = "* Passwords do not match";
                $errPass2 = "**";
            }
        }

        if($validFirstName && $validLastName &&  $validEmail && $validTitle && $validPass) {
           $selectCmd = "SELECT * FROM Users WHERE email='$email'";

           $result = $conn->query($selectCmd);

           if(!$result) {
                $errorDivMsg = "register.php: error accessing database";
           } else {

           }
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

<!-- Title -->
<h1 align="center">User Registration</h1>
<hr><br>

<!-- Begin form -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<div id="errorDiv"><?php echo "$errorDivMsg"; ?></div>

<!-- Enter name and email -->
First Name: <input type="text" id="firstName" value="<?php if(isset($_POST['firstName'])) { echo htmlentities($_POST['firstName']); }?>">
<span id="fNameError" class="error"><?php echo "$errFirstName";?></s$validTitle &&pan>
<br><br>

Last Name: <input type="text" id="lastName" value="<?php if(isset($_POST['lastName'])) { echo htmlentities($_POST['lastName']); }?>">
<span id="lNameError" class="error"><?php echo "$errLastName";?></span>
<br><br>

<!-- Select title -->
Title: 
<input type="radio" id="assistant" value="Assistant">Assistant Professor
<input type="radio" id="associate" value="Associate">Associate Professor
<input type="radio" id="full" value="Full">Full Professor
<span id="titleError" class="error"><?php echo "$errTitle";?></span>
<br><br>

E-mail: <input type="text" id="email" value="<?php if(isset($_POST['email'])) { echo htmlentities($_POST['email']); }?>">
<span id="emailError" class="error"><?php echo "$errEmail";?></span>
<br><br>

Password: <input type="password" id="pass1" value="<?php if(isset($_POST['pass1'])) { echo htmlentities($_POST['pass1']); }?>">
<span id="passError1" class="error"><?php echo "$errPass1";?></span>
<br><br>

Re-enter password: <input type="password" id="pass2" value="<?php if(isset($_POST['pass2'])) { $_POST['pass2']; }?>">
<span id="passError2" class="error"><?php echo "$errPass2";?></span>
<br><br>

<!-- Submit information if all required input is valid -->
<input type="button" id="cancel" value="Cancel">
<input type="button" id="createUser" value="Submit">

<!-- Form ends here -->
</form>

<!-- Scripts begin here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">

// Redirect user to login page
$("#cancel").click(function() {
    window.location.href = "../index.php";
});

<!-- Scripts end here -->
</script>
</body>
