<?php 
    session_start();

    require_once 'event/connDB.php';
    
    //var_dump($_SESSION);
    // Redirect user to correct page if already logged in
    // and cookie is still valid
    if(!(empty($_SESSION['LAST_ACTIVITY']))) {
        if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
            if(isSessionExpired()) {
                signOut();
            } else { 
                redirectUser();
            }
        }
    }

    # Login verification 
    $email = $pswd = "";
    $emailErr = $pswdErr = $loginError = "";
    $DB_ERROR = "<font color='red'><i>* Error: could not get user data</i></font>";
    $LOGIN_ERROR_MSG = "<font color='red'><i>* Incorrect email / password combination</i></font>"; 

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //echo "entering POST";
        if(empty($_POST['email'])) {
            //echo 'in empty(email)'."\n";
            $emailErr = "<font color='red'>* email required</font>";
        } else { $email = cleanInput($_POST['email']); }

        if(empty($_POST['pswd'])) {
            //echo 'in empty(pswd)'."\n";
            $pswdErr = "<font color='red'>* password required</font>";
        } else { $pswd = cleanInput($_POST['pswd']); }

        //echo "Email: $email Pass: $pswd\n";
    } // End of $_SERVER_REQUEST

    if(!(empty($email) && empty($pswd))) { // Login if credentials are valid
        //echo "verifying email and password\n";

        $getUserInfoCmd = "SELECT user_id, fName, lName, password, title ";
        $getUserInfoCmd .= "FROM Users WHERE email='$email'";
        $result = mysqli_query($conn,$getUserInfoCmd);

        if($result) { // Verify password
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $pswdHash = $row['password'];
                if(password_verify($pswd,$pswdHash)) { 
                    //echo 'password verified'."\n";
                    $IDLE_TIME_LIMIT = 1200; // 1200 seconds = 20 mins
                    $ADMIN = "Administrator";
                    $id = $row['user_id'];
                    $title = $row['title'];
                    $name = $row['fName'];
                    $name .= ' '.$row['lName'];

                    $_SESSION['user_id'] = $id;
                    $_SESSION['userName'] = $name;
                    $_SESSION['title'] = $title;
                    // IDLE_TIME_LIMIT set line 53
                    //1200 seconds = 15 mins
                    $_SESSION['IDLE_TIME_LIMIT'] = $IDLE_TIME_LIMIT; 
                    $_SESSION['LAST_ACTIVITY'] = time();
                    $_SESSION['REAL_PATH_DOC_ROOT'] = realpath($_SERVER['DOCUMENT_ROOT']);
                    saveSessionVars();

                     if($title == $ADMIN) { // Redirect to admin home page
                        redirectToAdminPage();
                    } else if($title ) { 
                        // Redirect to user profile
                        redirectToUserPage();
                    }
                } else { // Incorrect password
                        $loginError = $LOGIN_ERROR_MSG;      
                } 
            } else { // user with $email not found
                    $loginError = $LOGIN_ERROR_MSG; 
            } 
        } else { // error executing $getUserInfoCmd
                $loginError = $DB_ERROR; 
            }
    } // End of login authentication

    // Check for idle time limit has been reached
    function isSessionExpired() {
        $lastActivity = $_SESSION['LAST_ACTIVITY'];
        $timeOut = $_SESSION['IDLE_TIME_LIMIT'];
        
        // Check if session has been active longer than IDLE_TIME_LIMIT
        if(time() - $lastActivity >= $timeOut) {
            return true;
        } else { false; }   
    }// End of isSesssionExpired()

    function redirectUser() {
        // Session still valid, might have used the "back arrow"
        // to navigate to this page, redirect appropiately
        $ADMIN = "Administrator";
        if(!empty($_SESSION['title'])) {
            if($_SESSION['title'] == $ADMIN) {
                updateLastActivity();
                saveSessionVars();
                redirectToAdminPage();
            } else if($_SESSION['title']) { 
                updateLastActivity();
                saveSessionVars();
                redirectToUserPage();
            } else { // Error log out user and remain on page
                logOut();
            }
        }
    }

    function updateLastActivity() {
        $_SESSION['LAST_ACTIVITY'] = time();
        return;
    }

    function saveSessionVars() {
        session_write_close();
        return;
    }

    function redirectToUserPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='user/home.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToAdminPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='admin/home.php'</script>";
        echo $jsRedirect;
        return;
    }

    function signOut() {
        // Destroy previous session
        session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();
        return;
    } 

    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
// End of PHP ?>

<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
 	<tr>
		<form  method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
 		<td>
 		<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
            <tr>
 				<td colspan="3" algin="center">
                    <span align="center"><strong>Member Login</strong></span>
                    <?php echo $loginError; ?>
                </td>
			</tr>
			<tr>
				<td colspan="3"> <p id="loginError"></p>
				</td>
			</td>
 			<tr>
 				<td width="78">Email</td>
				<td width="6">:</td>
 				<td width="294"><input type="text" name="email" id="email">
                    <?php echo $emailErr; ?>
                </td>
 			</tr>
 			<tr>
 				<td>Password</td>
 				<td>:</td>
 				<td><input type="password" id="pswd" name="pswd">
                    <?php echo $pswdErr; ?>
                </td>
 			</tr>
 			<tr>
 				<td>&nbsp;</td>
 				<td>&nbsp;</td>
				<td><input type="submit" id="loginButton" value="Login"></td>
				<td><a href="event/register.php">Register</a></td>
 			</tr>
 		</table>
 		</td>
 		</form>
 	</tr>
</table>