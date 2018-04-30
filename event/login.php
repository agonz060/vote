<?php
	session_start();
	require_once "../includes/connDB.php";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
    	# Constants
		$ERROR = 0;
		$SUCCESS = 1;

		# Login verification
	    $email = $pass = null;
	    $emailErr = $passErr = $loginError = "";
	    $DB_ERROR = "Error: could not get user data";
	    $LOGIN_ERROR_MSG = "* Incorrect email / password combination";
	    $return = array();

        //echo "entering POST";
        if(empty($_POST['email'])) {
            //echo 'in empty(email)'."\n";
            $return = array( 'status'=> $ERROR,
            			'msg'=> "Email is missing." );
        } else { $email = cleanInput($_POST['email']); }

        if(empty($_POST['pass']) && !$_POST['reset']) {
            //echo 'in empty(pass)'."\n";
            $return = array( 'status'=> $ERROR,
            			'msg'=> "Password is missing." );
        } else { $pass = cleanInput($_POST['pass']); }

        if(isset($email) && isset($pass)) {
            //echo "verifying email and password\n";
            if(strlen($email) > 0 && strlen($pass) > 0) {
                $getUserInfoCmd = "SELECT user_id, fName, lName, password, title ";
                $getUserInfoCmd .= "FROM Users WHERE email='$email'";
                $result = mysqli_query($conn,$getUserInfoCmd);

                if($result) { // Verify password
                    if($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $passHash = $row['password'];
                        if(password_verify($pass,$passHash)) {
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
                            // IDLE_TIME_LIMIT set
                            //1200 seconds = 15 mins
                            $_SESSION['IDLE_TIME_LIMIT'] = $IDLE_TIME_LIMIT;
                            $_SESSION['LAST_ACTIVITY'] = time();

                             if($title == $ADMIN) { // Redirect to admin home page
                             	$return = array( 'status'=> $SUCCESS, 'title'=> 'Administrator');
                            } else if($title) {
                               $return = array( 'status'=> $SUCCESS, 'title'=> 'User');
                            }
                        } else { // Incorrect password
                                $return = array( 'status'=> $ERROR,
            						'msg'=> $LOGIN_ERROR_MSG );
                        }
                    } else { // user with $email not found
                            $return = array( 'status'=> $ERROR,
            						'msg'=> $LOGIN_ERROR_MSG );
                    }
                } else { // error executing $getUserInfoCmd
                        $return = array( 'status'=> $ERROR,
            						'msg'=> $DB_ERROR );
                } // Login if credentials are valid
            }
        } // End of if(isset(...))
        // return output
        echo json_encode($return);
    } // End of SERVER post

    function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}