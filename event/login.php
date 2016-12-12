<?php session_start(); ?>
<?php 
    //echo "in login.php"; 
    require 'connDB.php';

    // Remove any specail characters to prevent mysql injection
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $type = "";

    // Get encrypted pass
    $getUserInfo = "Select password,user_id,type from Users WHERE email='$email'";
    $result = mysqli_query($conn, $getUserInfo);

    // return to login page with error if user email not found
    if(!$result) {
	$_SESSION['emailError'] = 1;
    } else {
	$row = $result->fetch_assoc();
	$passHash = $row['password'];
			
	$verified = password_verify($pass, $passHash);
			
	if($verified) {
	    $_SESSION['uId'] = $row['user_id'];
            if (strtolower($row['type']) == "admin" ) {
                $_SESSION['uType'] = 1;
            } else { $_SESSION['uType'] = 0; }
	} else { 
	    $_SESSION['pswdError'] = 1; 
	}
    }
?>	
