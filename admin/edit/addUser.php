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
<h1 align="center">Add User</h1>
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

E-mail: <input type="text" id="email">
<span id="emailError" class="error"> </span>
<br><br>

<!-- Select title -->
Title: 
<input type="radio" id="assistant" value="Assistant">Assistant Professor
<input type="radio" id="associate" value="Associate">Associate Professor
<input type="radio" id="full" value="Full">Full Professor
<input type="radio" id="admin" value="Admin">Admin
<span id="titleError" class="error"></span>
<br><br>

<!-- Submit information if all required input is valid -->
<input type="button" id="cancel" value="Cancel">
<input type="button" id="createUser" value="Submit">

<!-- Form ends here -->
</form>

<!-- Scripts begin here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
$("#createUser").click(function() {
    var _fName = $('#firstName').val();	
    var _lName = $("#lastName").val();
    var _email = $("#email").val();
    var _title = $("input:checked").val();
    
    if(!(_fName) || _fName.length === 0) {
        $("#fNameError").text("* First name required");        
    } else if(!(_lName) || _lName.length === 0) {
        $("#lNameError").text("* Invalid last name");
    } else if(!(_email) ||_email.length === 0) {
        $("#emailError").text("* Email required");
    } else if(!(_title) || _title.length === 0) {
        $("#titleError").text("* Title selection required");
    } else {
        $.post("event/createUser.php", {fName: _fName, lName: _lName, email: _email, title: _title},
            function(data) {
                if(data) { alert(data); }       
        });
    }
    
});

$("#cancel").click(function() {
    window.location.href = "../../index.php";
});
</script>

</body>
