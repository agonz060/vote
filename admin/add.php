<?php 
    session_start();
    
    /*if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
    */

    function idleLimitReached() {
        if(!(empty($_SESSION['LAST_ACTIVITY']))) {
            if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
                if(isSessionExpired()) {
                    return 1;
                } else { return 0; }
            } else { // Error must have occurred
                    return 1; }
        } else { // Error must have occurred 
            return 1; }
    } // End of isValidSession() 

    function isAdmin() {
        if(!empty($_SESSION['title'])) {
            $ADMIN = "Administrator";

            if($_SESSION['title'] !== $ADMIN) {
                return 0;
            } else return 1;
        }
    }

    // Check for expired activity
    function isSessionExpired() {
        $lastActivity = $_SESSION['LAST_ACTIVITY'];
        $timeOut = $_SESSION['IDLE_TIME_LIMIT'];
        
        // Check if session has been active longer than IDLE_TIME_LIMIT
        if(time() - $lastActivity >= $timeOut) {
            return true;
        } else { false; }   
    }// End of isSesssionExpired()

    function updateLastActivity() {
        $_SESSION['LAST_ACTIVITY'] = time();
        return;
    }

    function saveSessionVars() {
        session_write_close();
        return;
    }

    function signOut() {
        // Destroy previous session
        session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();

        // Save and redirect
        saveSessionVars();
        redirectToLogIn();
    }
    
    function redirectToAddPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='add.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToVotePage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='vote.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='edit.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='review.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../index.php'</script>";
        echo $jsRedirect;
        return;
    }
    
/* Session verification ends here */ 
?>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
    .error {color: #FF0000;}
</style>
</head>
<body>
<!-- Start PHP -->
<?php
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

<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li><a href="vote.php">Create Poll</a></li>
			<li><a href="edit/editTable.php">Edit Poll</a></li>
			<li><a href="edit/reviewTable">Review Poll</a></li>
			<li class="active"><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<!-- Begin form -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<span id="registrationErr" class="error"><?php echo "<i>$registrationErr</i><br>";?></span>
<!-- Enter name and email -->
First Name: <input type="text" id="firstName" name="firstName" value="<?php if(isset($_POST['firstName'])) { echo htmlentities($_POST['firstName']); }?>">
<span id="fNameError" class="error"><?php echo "$errFirstName";?></span>
<br><br>

Last Name: <input type="text" id="lastName" name="lastName" value="<?php if(isset($_POST['lastName'])) { echo htmlentities($_POST['lastName']); }?>">
<span id="lNameError" class="error"><?php echo "$errLastName";?></span>
<br><br>

<!-- Select title -->
Title: 
<input type="radio" name="title" id="Assistant Professor" value="Assistant Professor">Assistant Professor
<input type="radio" name="title" id="Associate Professor" value="Associate Professor">Associate Professor
<input type="radio" name="title" id="Full Professor" value="Full Professor">Full Professor
<input type="radio" name="title" id="Administrator" value="Administrator">Administrator
<span id="titleError" class="error"><?php echo "$errTitle";?></span>
<br><br>

E-mail: <input type="text" id="email" name="email" value="<?php if(isset($_POST['email'])) { echo htmlentities($_POST['email']); }?>">
<span id="emailError" class="error"><?php echo "$errEmail";?></span>
<br><br>

Password: <input type="password" id="pass1" name="pass1" value="<?php if(isset($_POST['pass1'])) { echo htmlentities($_POST['pass1']); }?>">
<span id="passError1" class="error"><?php echo "$errPass1";?></span>
<br><br>

Re-enter password: <input type="password" id="pass2" name="pass2" value="<?php if(isset($_POST['pass2'])) { $_POST['pass2']; }?>">
<span id="passError2" class="error"><?php echo "$errPass2";?></span>
<br><br>

<!-- Submit information if all required input is valid -->
<input type="button" id="cancel" value="Cancel">
<input type="submit" id="createUser" value="Submit">

<!-- Form ends here -->
</form>

<!-- Scripts begin here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    setTitle();

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
}); // End of $(document).ready

// Redirect user to login page
$("#cancel").click(function() {
    window.location.href = "../index.php";
});

<!-- Scripts end here -->
</script>
</body>
