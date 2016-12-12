<?php session_start(); ?>
<?php 

require 'connDB.php';

#<!-- Define variables -->
$firstName = $lastName = $email = $title = "";
$errFirstName = $errLastName = $errEmail = "";
$validFirstName = $validLastName = $validEmail = false;

#Set appropiate error messages if an input field is left empty 
if($_SERVER["REQUEST_METHOD"] == "POST") {
    # Check for correct email format
    $firstName = cleanInput($_POST["fName"]);
    $lastName = cleanInput($_POST["lName"]);
    $email = cleanInput($_POST["email"]);
    $title = cleanInput($_POST["title"]);
    $pass = cleanInput($_POST["pass"]);

    # Name should only contain letters and space character
    if(!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
        $_SESSION["fNameErr"] = 1;
        $_SESSION["fNameErrMsg"] = " * Only letters and white space allowed";
    } else {
        # First name is valid 
	$validFirstName = true; 
    }
		
    # Name should only contain letters and space character
    if(!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
        $_SESSION["lNameErr"]  = 1;
        $_SESSION["lNameErrMsg"] = "* Only letters and white space allowed";
    } else {	
	#Last name is valid
	$validLastName = true;
    }
    
		
    # Check for valid email
    $regEmailCheck = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
    if(!preg_match($regEmailCheck, $email)) {
        $_SESSION["emailErr"] = 1;
        $_SESSION["emailErrMsg"] = "* Error invalid email format"; 
    } else {
	$validEmail = true; 	
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

# Check for existing user if all user data is valid
if($validEmail && $validFirstName && $validLastName) {
    $selectUserCmd = "SELECT * FROM Users WHERE fName='$firstName' and lName='$lastName'";
   
    # Execute selection command
    $results = $conn->query($selectUserCmd);

    # Check for successful sql execution
    if(!$results) {
        $_SESSIONS["dbError"] = "Error in addUser.php: could not connect to database";
    } else {
        # Number of rows returned = 0 if user information not found, 
        # otherwise, returns >= 1
        if($results->num_rows == 0) {
            # Format names to include proper puncuation
            $firstName[0] = strtoupper($firstName[0]);
            $lastName[0] = strtoupper($lastName[0]);
            
            # Use hashing function to store passwords
            $hashPass = password_hash($pass, PASSWORD_DEFAULT);
            
            # Insert user into database
            $addUserCmd = "INSERT INTO Users(email,fName,lName,password,type) values('$email','$firstName','$lastName','$hashPass','$title')";
            $results = $conn->query($addUserCmd);
            
            # Setting variable value
            
            # Check for error when executing mysql command that inserts user
            if($results) {
                echo "Success: user added";
                $_SESSION["regComplete"] = 1;
            } else {
                echo "Error executing addUserCmd";
                $_SESSION["regComplete"] = 0;
                $_SESSION["regErr"] = "Error in addUser.php: could not execute addUserCmd";
            }
        } else {
            $_SESSION["userExists"] = 1;
        }
    }
}

# End php
?>
