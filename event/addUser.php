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
    
    echo "F: $firstName L: $lastName E: $email T: $title P: $pass";

    # Name should only contain letters and space character
    if(!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
        $_SESSION["fNameErr"] = 1;
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

echo "before validity checks";

# Check for existing user if all user data is valid
if($validEmail && $validFirstName && $validLastName) {
    echo "inside valid block";

    $selectUserCmd = "SELECT * FROM Users WHERE fName='$firstName' and lName='$lastName'";
   
    # Execute selection command
    $results = $conn->query($selectUserCmd);

    # Check for successful sql execution
    if(!$results) {
        echo "in !results";
        $_SESSIONS["dbError"] = 1;
    } else {
        # Number of rows returned = 0 if user information not found, 
        # otherwise, returns >= 1
        echo "Num rows: ".$results->num_rows;
        if($results->num_rows == 0) {
            echo "User does not exist";

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
                $_SESSION['userCreated'] = 1;
            } else {
                $_SESSION['userCreated'] = 0;
            }
        } else {
            echo "in user exists";
            $_SESSION["userExists"] = 1;
        }
    }
}

# Call function session_write_close() before exiting file
session_write_close();
?>
