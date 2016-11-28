<?php 
#<!-- Define variables -->
$firstName = $lastName = $email = $title = "";
$errFirstName = $errLastName = $errEmail = "";
$validFirstName = $validLastName = $validEmail = false;

#Set appropiate error messages if an input field is left empty 
if($_SERVER["REQUEST_METHOD"] == "POST") {
    # Check for correct email format
    $firstName = cleanInput($_POST["firstName"]);
    $lastName = cleanInput($_POST["lastName"]);
    $email = cleanInput($_POST["email"]);
    $title = cleanInput($_POST["title"]);

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

?>
<!-- End PHP -->
	
<!-- Scripts begin here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
 
});
</script>

</body>
