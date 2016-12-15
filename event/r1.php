<?php session_start(); 
    
    # Set session variables 
    $_SESSION['fNameErr'] = 0;
    $_SESSION['lNameErr'] = 0;
    $_SESSION['emailErr'] = 0;
    $_SESSION['dbError'] = 0;
    $_SESSION['userExists'] = 0;
    $_SESSION['userCreated'] = 0;
?>

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

<!-- Title -->
<h1 align="center">User Registration</h1>
<hr><br>

<!-- Begin form -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<!-- Enter name and email -->
First Name: <input type="text" id="firstName">
<span id="fNameError" class="error"></span>
<br><br>

Last Name: <input type="text" id="lastName">
<span id="lNameError" class="error"></span>
<br><br>

<!-- Select title -->
Title: 
<input type="radio" id="assistant" value="Assistant">Assistant Professor
<input type="radio" id="associate" value="Associate">Associate Professor
<input type="radio" id="full" value="Full">Full Professor
<span id="titleError" class="error"></span>
<br><br>

E-mail: <input type="text" id="email">
<span id="emailError" class="error"> </span>
<br><br>

Password: <input type="password" id="pass1">
<span id="passError1" class="error"></span>
<br><br>

Re-enter password: <input type="password" id="pass2">
<span id="passError2" class="error"></span>
<br><br>

<!-- Submit information if all required input is valid -->
<input type="button" id="cancel" value="Cancel">
<input type="button" id="createUser" value="Submit">

<!-- Form ends here -->
</form>

<!-- Scripts begin here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
$("#createUser").click(function() {
    var _fName = $('#firstName').val();	
    var _lName = $("#lastName").val();
    var _email = $("#email").val();
    var _title = $("input:checked").val();
    var _pass1 = $("#pass1").val();
    var _pass2 = $("#pass2").val();
    var tmpPass1 = _pass1.toLowerCase();
    var tmpPass2 = _pass2.toLowerCase();
    var error = 0;
    
    //alert("F: "+_fName+" L: "+_lName+" E: "+_email+" T: "+_title+" P: "+_pass1);

    // Check for valid user input
    if(!(_fName) || _fName.length === 0) {
        error = 1;
        $("#fNameError").text("* First name required");        
    } else { $("#fNameError").text(""); }
    
    if(!(_lName) || _lName.length === 0) {
        error = 1;
        $("#lNameError").text("* Invalid last name");
    } else { $("#lNameError").text(""); }
    
    if(!(_email) ||_email.length === 0) {
        error = 1;
        $("#emailError").text("* Email required");
    } else { $("#emailError").text(""); }
    
    if(!(_title) || _title.length === 0) {
        error = 1;
        $("#titleError").text("* Title selection required");
    } else { $("#titleError").text(""); }
    
    if(!(_pass1 || _pass2) || _pass1.length === 0 || _pass2.length === 0) {
        error = 1;
        $("#passError1").text("* Password required");
        $("#passError2").text("**");
    } else { 
        $("#passError1").text("");
        $("#passError2").text(""); 
    }

    if(tmpPass1.localeCompare(tmpPass2) != 0) {
        error = 1;
        $("#passError1").text("* Passwords do not match");
        $("#passError2").text("**");
    } else {
        $("#passError1").text("");
        $("#passError2").text(""); 
    }

    if(error === 0) {
        $.post("addUser.php", {fName: _fName, lName: _lName, email: _email, title: _title, pass: _pass1},
                function(data) {
                    if(data) { alert(data); }
                    
                    // Post variables
                    var dbError = <?php if(isset($_SESSION['dbError'])) { echo json_encode($_SESSION['dbError']); } ?>;
                    var userExists = <?php if(isset($_SESSION['userExists'])) { echo json_encode($_SESSION['userExists']); } ?>;
                    var fNameErr = <?php if(isset($_SESSION['fNameErr'])) { echo json_encode($_SESSION['fNameErr']); }?>;
                    var lNameErr = <?php if(isset($_SESSION['lNameErr'])) { echo json_encode($_SESSION['lNameErr']); }?>;
                    var emailErr = <?php if(isset($_SESSION['emailErr'])) { echo json_encode($_SESSION['emailErr']); }?>;
                    var userCreated = <?php if(isset($_SESSION['userCreated'])) { echo json_encode($_SESSION['userCreated']); }?>;

                    // Display error then reset session variable value
                    if(dbError == 1) {
                        dbErrorMsg = "addUser.php: Error could not connect to database";
                        alert(dbErrorMsg);
                        <?php $_SESSION['dbError'] = 0; ?>
                    }
                    
                    // Notify user account information is already stored in database
                    if(userExists == 1) {
                        alert("A user was found matching the information provided.");
                        <?php $_SESSION['userExists'] = 0; ?>
                    } else {
                        // Output errors 
                        if(fNameErr == 1) {
                            errorMsg = "* Only letters and white space allowed";
                            $("#fNameError").text(errorMsg);
                            <?php $_SESSION['fNameErr'] = 0; ?>
                        }
                    
                        if(lNameErr == 1) {
                            errorMsg = "* Only letters and white space allowed";
                            $("#lNameError").text(errorMsg);
                            <?php $_SESSION['lNameErr'] = 0; ?>
                        }
                    
                        if(emailErr == 1) {
                            errorMsg = "* Invalid email format: example@mail.com";
                            $("#emailError").text(errorMsg);
                            <?php $_SESSION['emailErr'] = 0; ?>
                        }
                        
                        if(userCreated == 0) {
                            errorMsg = "addUser.php: Error executing addUserCmd";
                            alert(errorMsg);
                        } else {
                            alert("User registration complete!");
                        } 
                    }
            }); // End of $.post function call
    } // End of if statement
        
});// End of jquery command

$("#cancel").click(function() {
    window.location.href = "../index.php";
});

<!-- Scripts end here -->
</script>
</body>
