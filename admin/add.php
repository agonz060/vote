<?php 
    session_start();

   /* require_once "event/sessionHandling.php";
    require_once "event/redirections.php";

    if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
    */
require_once 'event/connDB.php';
#<!-- Define variables -->
$firstName = $lastName = $email = $title = $pass1 = $pass2 = $hashPass = "";
$tmpPass1 = $tmpPass2 = $registrationErr = $errFirstName = "";
$errLastName = $errEmail = $errTitle = $errPass1 = $errPass2 = "";
$validFirstName = $validLastName = $validEmail = $validTitle = $validPass = false;

#Set appropiate error messages if an input field is left empty 
if($_SERVER["REQUEST_METHOD"] == "POST") {
        # Check for correct name format
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
        $title = cleanInput($_POST["title"]);
        $validTitle = true;
    }
        
    if(empty($_POST['pass1'])) {
        $errPass1 = "* Password required";
    } else { $tmpPass1 = cleanInput($_POST['pass1']); }

    if(empty($_POST['pass2'])) {
        $errPass2 = "* Password requried";
    } else { $tmpPass2 = cleanInput($_POST['pass2']); }

    // Check if passwords are identical
    if($tmpPass1 && $tmpPass2) {
        if($tmpPass1 === $tmpPass2) {
            $validPass = true;
            $hashPass = password_hash($tmpPass1, PASSWORD_DEFAULT);
        } else {
            $errPass1 = "* Passwords do not match";
            $errPass2 = "* Passwords do not match";
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

                if(!$result) { // Error executing $addUserCmd line 103,104
                    $registrationErr = "Error: could not create user with email '$email'"; }
                else { 
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
<title>Add User</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
    .error {color: #FF0000;}
</style>
</head>
<body>

<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li><a href="vote.php">Create Poll</a></li>
			<li><a href="edit/editTable.php">Edit Poll</a></li>
			<li><a href="edit/reviewTable.php">Review Poll</a></li>
			<li class="active"><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<!-- Begin form -->
<div style="width: 30%" class="container well">
<form class="form-signin" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<h2 class="form-signin-heading">Register</h2>
	<div class="form-group">
		<?php if(!empty($registrationErr)) { echo "<p class='error'><i>$registrationErr</i><p>"; }?>
		<input class="form-control" name="firstName" id="firstName" placeholder="First Name" value="<?php if(isset($_POST['firstName'])) { echo htmlentities($_POST['firstName']); }?>"> 
		<?php if(!empty($errFirstName)) { echo "<p class='error'><i>$errFirstName</i><p>"; }?>
	</div>
	<div class="form-group">
		 <input class="form-control" name="lastName" id="lastName" placeholder="Last Name" value="<?php if(isset($_POST['lastName'])) { echo htmlentities($_POST['lastName']); }?>"> 
		<?php if(!empty($errLastName)) { echo "<p class='error'><i>$errLastName</i><p>"; }?>
	</div>

	<!-- Select title -->
	<div class="form-group"> 
		<div class="radio">
		<label><input type="radio" name="title" id="Assistant Professor" value="Assistant Professor">Assistant Professor</label>
		</div>
		<div class="radio">
		<label><input type="radio" name="title" id="Associate Professor" value="Associate Professor">Associate Professor</label>
		</div>
		<div class="radio">
		<label>	<input type="radio" name="title" id="Full Professor" value="Full Professor">Full Professor</label>
		<div class="radio">
		<label>	<input type="radio" name="title" id="Administrator" value="Administrator">Administrator</label>
		<?php if(!empty($titleError)) { echo "<p class='error'><i>$titleError</i><p>"; }?>
	</div>
	<div class="form-group">
		 <input class="form-control" name="email" id="email" placeholder="Email" value="<?php if(isset($_POST['email'])) { echo htmlentities($_POST['email']); }?>"> 
		<?php if(!empty($errEmail)) { echo "<p class='error'><i>$errEmail</i><p>"; }?>
	</div>
	<div class="form-group">
		 <input class="form-control" type="password" name="pass1" id="pass1" placeholder="Enter Password"> 
		<?php if(!empty($errPass1)) { echo "<p class='error'><i>$errPass1</i><p>"; }?>
	</div>
	<div class="form-group">
		 <input class="form-control" type="password" name="pass2" id="pass2" placeholder="Confirm Password"> 
		 <?php if(!empty($errPass2)) { echo "<p class='error'><i>$errPass2</i><p>"; }?>
	</div>
<!-- Submit information if all required input is valid -->
<button type="reset" name="cancel" id="cancel" class="btn btn-danger"value="Cancel">Cancel</button>
<button type="submit" class="btn btn-success"  value="Submit">Submit</button<
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
        // Set interval to reload page for user authentication purposes
        setInterval(reloadPage,1200000); //1200000 ms = 1200 s = 20 mins
    }); // End of $(document).ready

    // Redirect user to login page
    $("#cancel").click(function() {
        window.location.href = "../index.php";
    });

    function setTitle() {
        title = <?php if($title) { echo json_encode($title); } else { echo 0; } ?>;
        //alert("title: " + title);

        if(title) {
            var radio = $("#"+title);
            if(radio.is(':checked') === false) {
                radio.prop('checked',true);
            }
        } // End of if
    }; // End of setTitle()

    function reloadPage() {
        location.reload();
    };
// Script ends here
</script>
