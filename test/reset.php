<?php
	# requires
	require_once '../includes/connDB.php';
	require_once '../admin/mailer/autoload.php';

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		# constants
		$TRUE = 1;
		$FALSE = 0;
		$ERROR = 0;
		$SUCCESS = 1;
		$INVALID_EMAIL_MSG = 'Email not found. Please try another email.';
		$TOKEN_BYTE_LEN = 16;

		# variables
		$return  = null;
		$email = cleanInput($_POST['email']);

		if(strlen($email) > 0) {
			$selectStmt = "SELECT email FROM users WHERE email='$email'";
			if($result = mysqli_query($conn, $selectStmt)) {
				if($row = mysqli_fetch_row($result)) {
					$pendingAccount = addToPendingAccounts($conn,$email,$TOKEN_BYTE_LEN);
					if($pendingAccount['status'] == $SUCCESS) {
						sendEmail($pendingAccount['token'],$email);
						$return = array('status'=>$SUCCESS);
						echo json_encode($return);
					} else {
						$return = array('status'=>$ERROR, 'msg' => $pendingAccount['msg']);
						echo json_encode($return);
					}
				} else {
					# invalid email
					$return = array('status' => $ERROR, 'msg' => $INVALID_EMAIL_MSG);
					echo json_encode($return);
				}
			} else {
				# database error
				$errorMsg = "Error " . $selectStmt . "<br>" . mysqli_error($conn);
				$return = array('stauts' => $ERROR, 'msg' => $errorMsg);
				echo json_encode($return);
			}
		}
	}

	function addToPendingAccounts($conn,$email,$tokenLen) {
		# constants
		$ERROR = 0;
		$SUCCESS = 1;
		$EMPTY = '';
		$PASSWORD_RESET = 1;

		# variables
		$pendingAccount = null;

		if(strlen($email) > 0) {
			$token = createToken($tokenLen);
			$insertStmt = "INSERT INTO pending_accounts (email,title,password_reset,token)";
			$insertStmt .= " VALUES('$email','$EMPTY',$PASSWORD_RESET,'$token')";
			if(mysqli_query($conn,$insertStmt)) {
				echo "insert successful";
				$pendingAccount = array('status' => $SUCCESS, 'email' => $email, 'token' => $token);
			} else {
				// echo "failed inserting pending account";
				// $errorMsg = "Error " . $insertStmt . "<br>" . mysqli_error($conn);
				// $pendingAccount = array('status' => $ERROR, 'msg' => $errorMsg);
				// echo "updating pending account";
				$updateStmt = "UPDATE pending_accounts SET token='$token' WHERE email='$email'";

				if(mysqli_query($conn,$updateStmt) == FALSE) {
					$errorMsg = "Error " . $insertStmt . "<br>" . mysqli_error($conn);
					$pendingAccount = array('status' => $ERROR, 'msg' => $errorMsg);
				} else {
					$pendingAccount = array('status' => $SUCCESS, 'email' => $email, 'token' => $token);
				}
			}
		}
		return $pendingAccount;
	}

	function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
	}

	function createToken($len) {
		return bin2hex(openssl_random_pseudo_bytes($len));
	}

		// Function that sends emails, function call below this function
    function sendEmail($token,$email) {
    	$name = explode('@',$email);
    	$name = $name[0];

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
        $mail->Username = "bcoevotingnotification@gmail.com";
        $mail->Password = "compSci99";

        // If SMTP requires TLS encryption then set it
        $mail->SMTPSecure = "tls";

        // Set TCP port to connect to
        $mail->Port = 587;

        //From email address and name
        $mail->From = "bcoevotingnotification@gmail.com";
        $mail->FromName = "BCOE Voting";

        //To address and name
        $mail->addAddress($email,$name);
        //$mail->addAddress("recepient1@example.com"); //Recipient name is optional

        //Address to which recipient will reply
        $mail->addReplyTo("systems@engr.ucr.edu", "Reply");

        //CC and BCC
        //$mail->addCC("cc@example.com");
        //$mail->addBCC("bcc@example.com");

        //Send HTML or Plain Text email
        $mail->isHTML(true);
        $mail->Subject = "BCOE Voting Account Password Reset";

        // Compose message body
        $bodyMsg = "<h2>This email is intended for ".$email."<br>";
        $bodyMsg .= "<hr><br>Please follow the link to reset your password: ";
        $voteLink = "<a href='www.engr.ucr.edu/intranet/vote/event/register.php?t=".$token."''>Reset password</a><br>";
        //$voteLink = "<a href='localhost/vote/admin/test/register.php?t=$token'>Complete Registration</a><br>";
        $bodyMsg .= $voteLink;

        $mail->Body = $bodyMsg;
        //$mail->AltBody = "Testing plain text body of a message sent from a script";
		$mail->SMTPOptions = array(
			'ssl' => array (
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			)
		); // End of $mail->SMTPOptions()

        if(!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return;
        }

        return;
    } // End of function sendMail()