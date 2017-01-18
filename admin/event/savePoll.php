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
    
    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../index.php'</script>";
        echo $jsRedirect;
        return;
    }
    
/* Session verification ends here */ 
?>
<?php 
	//echo "Entering savePoll.php\n";
	require_once 'connDB.php';
    require_once "../mailer/autoload.php";

	// Poll data
	$pollId = $title = $descr = $actDate = $deactDate = "";
    $effDate = $pollType = $dept = $name = $reason = $sendFlag = "";
	$emailInfo = $removeList = [];
	// Voting data
	$profName = $fName = $lName = $profId = $pollData = $votingInfo = "";
    $voters = [];
	
	//Set Timezone
	date_default_timezone_set('America/Los_Angeles');

	// Check if data is set before accessing
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["pollData"])) {
			$pollData = $_POST["pollData"];
			//echo "pollData: "; print_r($pollData);
			//print_r(array_keys($pollData));
			//echo "pollId: " + $pollData['pollId'];
		} else { echo "savePoll.php: error pollData not set\n"; }

		if(isset($_POST["votingInfo"])) {
			$votingInfo = $_POST["votingInfo"];
			//echo " votingInfo: "; print_r($votingInfo);
		} //else { echo "savePoll.php: error votingInfo not set"; }
	        
        if(isset($_POST["reason"])) {
            $reason = $_POST["reason"];
        } else { echo "savePoll.php: error reason not set"; }
    }
    
    if(isset($pollData['title'])) {
        $title = $pollData['title'];
    } else { echo "savePoll.php: title not set\n"; }

    if(isset($pollData['descr'])) {
        $descr = $pollData['descr'];
    } else { echo "savePoll.php: description not set\n"; }


    if(isset($pollData['actDate'])) {
        $actDate = $pollData['actDate'];
    } else { echo "savePoll.php: activation date not set\n"; }


    if(isset($pollData['deactDate'])) {
        $deactDate = $pollData['deactDate'];
    } else { echo "savePoll.php: deactivation date not set\n"; }

    if($pollData['effDate']) {
        $effDate = $pollData['effDate'];
    }

    if(isset($pollData['pollType'])) {
        $pollType = $pollData['pollType'];
    } else { echo "savePoll.php: poll type not set\n"; }


    if(isset($pollData['name'])) {
        $name = $pollData['name'];
    } else { echo "savePoll.php: professor name not set\n"; }


    if(isset($pollData['dept'])) {
        $dept = $pollData['dept'];
    } else { echo "savePoll.php: department not set\n"; }


    if(isset($pollData['pollId'])) {
        $pollId= $pollData['pollId'];
    } else { echo "savePoll.php: poll ID not set\n"; }

    if(isset($pollData['sendFlag'])) {
        $sendFlag = $pollData['sendFlag'];
    } else { $sendFlag = 0; } // Only save poll if no flag

    //echo "pollId set to 30\n";
    //$pollId = 36;
	$name = $_SESSION['userName'];
    if($pollId > 0) { // Update Polls database if pollId exists
        //echo 'Updating existing poll id: '.$pollId."\n";
        // Update modification history

		$history=":edit:" . "$name" . ":" . date("Y-m-d") . ":" . $reason;
		
        // Mysql command to update Poll information
        $cmd = "UPDATE Polls SET title='$title', description='$descr', actDate='$actDate', ";
		$cmd .= "deactDate='$deactDate' , history=CONCAT(history,'$history'), effDate='$effDate',";
        $cmd .= " name='$name', dept='$dept', pollType='$pollType' WHERE poll_id='$pollId'";	
        //echo "updating cmd: $cmd\n";
		//echo "Update Polls cmd: $cmd";
		$result = mysqli_query($conn, $cmd);
			
		if(!$result) { echo "savePoll.php: could not update Polls table\n"; }
		
	} else { // Create new Poll in database
        //echo "Poll id not found. Creating new poll\n";
		// Update modification history
        $history="create:" ."$name" . ":" . date("Y-m-d") . ":" . $reason; 

        // Mysql command to create new Poll
        $cmd = "INSERT INTO Polls(title,description,actDate,deactDate,effDate,name,pollType,dept,history)";
<<<<<<< HEAD:admin/event/savePoll.php
		$cmd .= " VALUES('$title','$descr','$actDate','$deactDate','$effDate','$name','$pollType','$dept','$history')";
=======
	$cmd .= " VALUES('$title','$descr','$actDate','$deactDate','$effDate','$name','$pollType','$dept','$history')";
>>>>>>> b5c712b8d17bcd5a956fa833b572b577a7aae048:admin/event/savePoll.php
	    //echo "$cmd";

        $result = mysqli_query($conn,$cmd);	
		// Create new poll
		if($result) {
            // Order table by id from highest ID number to the lowest ID number and  
            // limit the result to the first ID number to get the most recently added poll
            $getIDCmd = "SELECT * FROM Polls ORDER BY poll_id DESC LIMIT 1";
            $result = mysqli_query($conn, $getIDCmd);
            
            if($result) {
                $row = $result->fetch_assoc();
                $pollId = $row['poll_id'];
                //echo 'New pollId: '.$pollId."\n";
                //echo "Finished updating Polls table\n";
            } else { echo "savePoll.php: could not fetch poll ID\n"; }
        } else { echo "savePoll.php: could not execute INSERT command $cmd\n"; } 
	}// End of updating Polls table
        
    if($votingInfo) {// Update Voters table
        //echo "Updating Voters table\n";
        // votingInfo = { "profName" => "comment" } 
        $profNames = array_keys($votingInfo);
        //echo 'profNames: '; print_r($profNames);

        // Get all previously saved participants of the current vote
        $getProfsCmd = "SELECT user_id FROM Voters WHERE poll_id='$pollId'";
        $result = mysqli_query($conn, $getProfsCmd);

        if($result) {
           if($result->num_rows > 0) {
                // Keep list of professor ID's 
                $idList = [];

                while($row = $result->fetch_assoc()) {
                    $idList[] = $row['user_id'];
                }
                //echo 'Ids assoc with current poll: '.$pollId." : "; print_r($idList);
                $tmpProfNames = array_map('strtolower',$profNames);
                //echo 'profNames converted to lower: ';print_r($tmpProfNames);

                // Check if the previous participants are still in the current vote 
                // (may have been removed if admin edits a saved poll)
                foreach ($idList as $id) {
                    $getNameCmd = "SELECT fName, lName FROM Users WHERE user_id='$id'";
                    $result = mysqli_query($conn, $getNameCmd);

                    if($result->num_rows > 0) {
                        $removeList = [];
                       
                        while($row = $result->fetch_assoc()) {
                            $name = $row['fName'];
                            $name .= " ".$row['lName'];
                            $name = strtolower($name);
                            //echo "profId: ".$id." name: ".$name." Searching through current prof list\n";

                            // Add user id to removeList if the professor name is not found
                            // in the most current poll data
                            if(in_array($name,$tmpProfNames) == false) {
                                //echo 'name: '.$name." not found in currently voting professors\n";
                                $removeList[] = $id;
                            } else { 
                                // if user is found then user remains in the current vote
                                $voters[] = $id; }
                        }
                    }
                    
                    // Remove professors from current poll
                    foreach($removeList as $id) {
                        //echo 'removing from vote id: '.$id."\n";
                    
                        $delCmd = "DELETE FROM Voters WHERE user_id='$id'";
                        $result = mysqli_query($conn, $delCmd);

                        if(!$result) {
                            echo "savePoll.php: could not delete user_id='$id'\n";
                        }
                    } // End of foreach
                }// End of foreach
           } // End of if
        } else { echo "savePoll.php: error executing getProfsCmd\n"; }
        
        if($profNames) {
            // Add professors to current poll by inserting prof's 
            // id and comment into the Voters table
            $e = $email = "";
            //echo 'ProfNames: '; print_r($profNames);
            foreach($profNames as $name) {
                // Get user id of each professor 
                //echo 'Name:'.$name."\n" ;
                $nameSplit = explode(' ',$name);
                $fName = $nameSplit[0];
                $lName = $nameSplit[1];
                
                $selectCmd = "SELECT user_id, email FROM Users WHERE fName='$fName' and lName='$lName'";
                $result = mysqli_query($conn,$selectCmd);
                
                if($result) {
                    $row = $result->fetch_assoc();
                    $id = $row['user_id'];
                    $email = $row['email'];

                    if(!$id) { echo "savePoll.php: error getting user_id"; }
                    if(!$email) { echo "savePoll.php: error getting email"; }

                    $cmt = $votingInfo[$name];
                    
                    // Store information for sending emails 
                    $e = array( "name" => $name,
                                "comment" => $cmt,
                                "email" => $email );
                    $emailInfo[] = $e;
                    
                    // Voter already part of current vote then UPDATE voters data 
                    if(in_array($id, $voters)) {
                        //echo 'update voter: '.$id." comment\n";
                        $updateVoter = "UPDATE Voters SET comment='$cmt' WHERE user_id='$id' AND poll_id='$pollId'";
                        $result = mysqli_query($conn, $updateVoter);

                        if(!$result) { echo "savePoll.php: could not update voter: $id info"; } 
                    } else { // Voter is new to poll, i.e INSERT voter into poll
                        //echo "inserting new voter: $id into Voters table\n";
                        //echo "voters: ";print_r($voters);
                        // Add id to voters[] for use when sending emails
                        $addToPollCmd = "INSERT INTO Voters(user_id, poll_id, pollEndDate, comment, voteFlag) ";
                        $addToPollCmd .= "VALUES('$id','$pollId','$deactDate','$cmt','0')";
                        $result = mysqli_query($conn,$addToPollCmd);
                        
                        if(!$result) { echo "savePoll.php: could not insert user_id='$id' into poll using: $addToPollCmd\n"; }
                    }
                } else { echo "savePoll.php: could not get user_id for user='$name'\n"; };
            }// End of foreach    
        }// End of adding profs to poll
    } // End of updating Voters table

    // Function that sends emails, function call below this function
    function sendEmail($name, $cmt, $email) { 
            //PHPMailer Object
        $mail = new PHPMailer;

        // Enable SMTP debugging
	$mail->SMTPDebug = false;
	//html friendly debug output
	//$mail->Debugoutput = 'html';
        // Set PHPMailer to use SMTP
        $mail->isSMTP();

        // Set SMTP host name
        $mail->Host = "smtp.gmail.com";

        // Set this to true if SMTP host requires authentication to send email
        $mail->SMTPAuth = true;

        // Enter credentials
        $mail->Username = "benderthesender@gmail.com";
        $mail->Password = "bendSend1";

        // If SMTP requires TLS encryption then set it
        $mail->SMTPSecure = "tls";

        // Set TCP port to connect to
        $mail->Port = 587;

        //From email address and name
        $mail->From = "benderthesender@gmail.com";
        $mail->FromName = "Armando Gonzalez";

        //To address and name
        $mail->addAddress($email, $name);
        //$mail->addAddress("recepient1@example.com"); //Recipient name is optional

        //Address to which recipient will reply
        $mail->addReplyTo("agonztest@gmail.com", "Reply");

        //CC and BCC
        //$mail->addCC("cc@example.com");
        //$mail->addBCC("bcc@example.com");

        //Send HTML or Plain Text email
        $mail->isHTML(true);
        $mail->Subject = "Sent from vote.php";
        
        // Compose message body
        $bodyMsg = "Comment: <br>" . $cmt . "<br>";
        $bodyMsg .= "<hr><br><h1>Success</h1><br><hr><br>Please follow the link to access your "; 
        $voteLink = "<a href='localhost/vote/index.php'>voting documents</a><br>";
        $bodyMsg .= $voteLink;

        $mail->Body = $bodyMsg;
        //$mail->AltBody = "Testing plain text body of a message sent from a script";
	$mail->SMTPOptions = array(
		'ssl' => array (
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);
        if(!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return;
        }

        return;
    } // End of function sendMail() 

    if($sendFlag) { // Send email to all voters participating in poll
        // User info variables
        $name = $cmt = $email = "";

        foreach($emailInfo as $userInfo) {
            $name = $userInfo["name"];
            $cmt = $userInfo["comment"];
            $email = $userInfo["email"];

            sendEmail($name,$cmt,$email);
        }   
    } // End of sending emails

    
?>
