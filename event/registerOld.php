<?php
session_start();


require_once '../includes/connDB.php';

#<!-- Define variables -->
$firstName = $lastName = $email = $title = $pass1 = $pass2 = $hashPass = "";
$tmpPass1 = $tmpPass2 = $registrationErr = $errFirstName = "";
$errLastName = $errEmail = $errTitle = $errPass1 = $errPass2 = "";
$validFirstName = $validLastName = $validEmail = $validTitle = $validPass = false;
$token = $pendingAccount = $pendingAccountError = null;

if(isset($_GET['t'])) {
	// echo $_GET['t'];
	$token = cleanInput($_GET['t']);
	if(strlen($token) > 0) {
		$pendingAccount = getPendingAccountInfo($conn,$token);
	}
}

function getPendingAccountInfo($conn,$token) {
	$row = null;
	$selectStmt = "SELECT email,title,password_reset FROM pending_accounts WHERE token='$token'";
	if($result = mysqli_query($conn,$selectStmt)) {
		$row = mysqli_fetch_assoc($result);
	} else {
		$pendingAccountError = "Error description: " . mysqli_error($conn);
		$row = array('error' => $pendingAccountError);
	}
	return $row;
}

function removePendingAccount($conn,$email) {
	$msg = '';
	$deleteStmt = "DELETE FROM pending_accounts WHERE email='".$email."'";
	if(mysqli_query($conn,$deleteStmt)) {
		$msg = "Error despription: " . mysqli_error($conn);
	}
	return $msg;
}

#Set appropiate error messages if an input field is left empty
if($_SERVER["REQUEST_METHOD"] == "POST") {
	// print_r($_POST);
	if(isset($_POST['pendingAccount'])) {
		$pendingAccount = json_decode($_POST['pendingAccount'],true);
	}

	if(empty($_POST["firstName"])){
	  	$errFirstName = "* required";
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
		$errLastName = "* required";
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

		if(isset($pendingAccount)) {
			$email = $pendingAccount['email'];
			$validEmail = true;
		} else {
			# Check for valid email
			$regEmailCheck = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
			if(!preg_match($regEmailCheck, $email)) {
				$errEmail = "* Invalid email";
			} else {
				$validEmail = true;
			}
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
    } else { $tmpPass1 = cleanInput($_POST['pass1']); }

    if(empty($_POST['pass2'])) {
        $errPass2 = "** requried";
    } else { $tmpPass2 = cleanInput($_POST['pass2']); }

    // Check if passwords are identical
    if($tmpPass1 && $tmpPass2) {
        if($tmpPass1 === $tmpPass2) {
            $validPass = true;
            $hashPass = password_hash($tmpPass1, PASSWORD_DEFAULT);
        } else {
            $errPass1 = "* Passwords do not match";
            $errPass2 = "**";
        }
    }


    if($validFirstName && $validLastName &&  $validEmail && $validTitle && $validPass) {
       	$selectCmd = "SELECT * FROM Users WHERE email='$email'";
		$result = $conn->query($selectCmd);

       	if(!$result) {
        	$registrationErr = "register.php: error accessing database";
       	} else {
       		// Check if user already exists in database
       		if($result->num_rows > 0) { // User already in Users table
       			//echo "user exists\n";
       			$registrationErr = "* User with email '$email' is already registered";
       		} else { // insert new user into Users table
       			$addUserCmd = "INSERT INTO Users(email,fName,lName,password,title)";
       			$addUserCmd .= " VALUES('$email','$firstName','$lastName','$hashPass','$title')";
       			//echo "Cmd: $addUserCmd";
       			$result = mysqli_query($conn,$addUserCmd);

       			if(!$result) {
                            $registrationErr = "Database error: could not create user with email '$email'"; }
       			else {
       				if(isset($pendingAccount)) {
       					echo (removePendingAccount($conn,$email));
       				}
       				echo "<script type='text/javascript'>
       						alert('Registration complete!');
       						window.location.href = '../index.php'
       						</script>";
       			} // End if
       		} // End if
       	} // End if
    } // End of update to Users table
} // End of $_POST

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
<html>
<head>
<title>Register</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
    .error {color: #FF0000;}
</style>
</head>
<body>
<!-- Start PHP -->

<!-- Title -->
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="../index.php">BCOE Voting</a>
		</div>
	</div>
</nav>
<!-- Begin form -->
<div style="width: 30%" class="container well">
<form class="form-signin" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<?php
	$FALSE = 0;
	$passwordReset = $FALSE;
	// echo 0;
	
	if(isset($pendingAccount['password_reset'])) {
		// echo 1;
		// print_r($pendingAccount);

		if($pendingAccount['password_reset']) {
			// echo 2;
			// password reset heading
			echo "<p>
					<h2>Password Reset</h2>
				</p>";

			// capture passwords
			echo "<div class=\"form-group\">
					 <input class=\"form-control\" type=\"password\" id=\"pass1\" placeholder=\"Enter Password\">
				</div>
				<div class=\"form-group\">
					 <input class=\"form-control\" type=\"password\" id=\"pass2\" placeholder=\"Confirm Password\">
				</div>";
				echo "<button type=\"submit\" class=\"btn btn-success\"  value=\"reset\">Register</button>";
		} else {
			$passwordReset = $FALSE;
		} 
	}

	if($passwordReset == $FALSE) {
		// echo 3;
		$TRUE = 1;
		$FALSE = 0;
		// variables
		$firstName = $lastName = $title = $error = $pendingAccountMsg = "";
		$titleSet = $displayRegisterButton = $FALSE;

		if(isset($pendingAccount['title'])) {
			$titleSet = $TRUE;
		}

		if(isset($pendingAccount['error'])) {
			$error = $pendingAccount['error'];
		}
		if(isset($pendingAccount)) {
			$pendingAccountMsg = "<p> Account registration for ".$pendingAccount['email']."</p>";
		} 
		
		// registration heading
		echo "<div><h2>Registration</h2>".$pendingAccountMsg."</div>";	

		// registration errors
		echo "<p>
				<span id=\"registrationErrors\" class=\"label label-danger\">" . $error . "</span>
				<p>
			";

		// start displaying registration form
		if(isset($_POST['firstName'])) {
			$firstName = htmlentities($_POST['firstName']);
		}
		if(isset($_POST['lastName'])) {
			$lastName = htmlentities($_POST['lastName']);
		}

		// output first and last name inputs
		echo "<div class=\"form-group\">
				<input class=\"form-control\" name=\"firstName\" id=\"firstName\" placeholder=\"First Name\" value=\"".$firstName."\">
				</div>";

		echo "<div class=\"form-group\">
		 		<input class=\"form-control\" name=\"lastName\" id=\"lastName\" placeholder=\"Last Name\" value=\"".$lastName."\">
		 		</div>";

		// if pending account is set then title and email should be provided, so 
		// dont display title inputs
		// display administrator radio input to make them feel special
		if($pendingAccount['title'] == 'Administrator') {
			echo "<div class=\"radio\">
						<label><input type=\"radio\" name=\"title\" id=\"Admin\" value=\"Administrator\" CHECKED>Administrator</label>
					</div>";
		} else if (isset($pendingAccount)) {
			$titleSet = $TRUE;
		}

		if($titleSet == $FALSE) {
			echo "
				<div class=\"form-group\">
					<div class=\"radio\">
						<label><input type=\"radio\" name=\"title\" id=\"Assistant Professor\" value=\"Assistant Professor\">Assistant Professor</label>
					</div>
					<div class=\"radio\">
						<label><input type=\"radio\" name=\"title\" id=\"Associate Professor\" value=\"Associate Professor\">Associate Professor</label>
					</div>
					<div class=\"radio\">
						<label>	<input type=\"radio\" name=\"title\" id=\"Full Professor\" value=\"Full Professor\">Full Professor</label>
					</div>
				</div>
			";
		}

		// display email, if available
		$displayEmail = '';
		if(isset($_POST['email'])) {
			$displayEmail = $_POST['email'];
			echo "
				<div class=\"form-group\">
					 <input class=\"form-control\" name=\"email\" id=\"email\" placeholder=\"Email\"
					 value=\"$displayEmail\">
				 </div>";
		} else if(isset($pendingAccount['email'])) {
			$displayEmail = $pendingAccount['email'];
			echo "
					<input type=\"hidden\" name=\"email\" value=\"$displayEmail\">
				";
		}

		// capture passwords
		echo "<div class=\"form-group\">
				 <input class=\"form-control\" type=\"password\" name=\"pass1\" id=\"pass1\" placeholder=\"Enter Password\">
			</div>
			<div class=\"form-group\">
				 <input class=\"form-control\" type=\"password\" name=\"pass2\" id=\"pass2\" placeholder=\"Confirm Password\">
			</div>";

		
		if(isset($pendingAccount['password_reset'])) {
			if($pendingAccount['password_reset']) {
				echo "<button type=\"submit\" class=\"btn btn-success\" value=\"reset\">Reset</button";
			} else {
				$displayRegisterButton = $TRUE;
			}
		} 

		if($displayRegisterButton) {
			echo "<button type=\"submit\" class=\"btn btn-success\"  value=\"register\">Register</button>";
		}
	} 

	// send account information along with form
	if(isset($pendingAccount)) {
		$encodedAccount = htmlentities(json_encode($pendingAccount));
		echo "<input type=\"hidden\" name=\"title\" value=\"".$pendingAccount['title']."\">";
		echo "<input type=\"hidden\" name=\"pendingAccount\" value=\"$encodedAccount\">";

	}
?>
<!-- Form ends here -->
</form>
</div>
</body>
</html>
<!-- Scripts begin here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	setTitle();

	// Redirect user to login page
		$("#cancel").click(function() {
		    window.location.href = "../index.php";
		});

	function setTitle() {
		var title = <?php if($title) { echo json_encode($title); } else { echo 0; } ?>;
		//alert("title: " + title);

		if(title) {
			var radio = $("#"+title);
			if(radio.is(':checked') === false) {
				radio.prop('checked',true);
			}
		} // End of if
	} // End of setTitle()
}); // End of $(document).ready
	

</script>