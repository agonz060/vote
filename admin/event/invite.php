<?php
	# requires
	require_once '../includes/connDB.php';
	require_once "../mailer/autoload.php";

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		# constants
		$TRUE = 1;
		$FALSE = 0;
		$TOKEN_BYTE_LEN = 16;

		# variables
		$emails = $email = $title = $inviteAdmin = null;
		$validEmails = $invalidEmails = $pendingAccounts = array();

		# testing
		#print_r($_POST);

		# get emails
		if(isset($_POST['emails'])) {
			$emails = explode(',',$_POST['emails']);
			#print_r($emails);
			for($x=0; $x < count($emails); ++$x) {
				$email = sanitizeInput($emails[$x]);
				if(strlen($email) > 0) {
					if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$validEmails[] = $email;
					} else {
						$invalidEmails[] = $email;
					}
				}
			}
		}
		# testing
		// print("Valid: "); print_r($validEmails);
		// print("<br>Invalid: "); print_r($invalidEmails);

		if(isset($_POST['title'])) {
			$title = $_POST['title'];
		} else {
			$title = '';
		}

		# add valid emails to pending database
		for($x=0; $x < count($validEmails); ++$x) {
			$email = $validEmails[$x];
			$pendingAccounts[] = addToPendingAccounts($conn,$email,$title,$TOKEN_BYTE_LEN);
		}

		# send emails
		if(count($pendingAccounts)) {
			for($x=0; $x < count($pendingAccounts); $x++) {
				if($pendingAccounts[$x]) {
					sendEmail($pendingAccounts[$x]['token'],$pendingAccounts[$x]['email']);
				}
			}
		}
	}

	function addToPendingAccounts($conn,$email,$title,$tokenLen) {
		$pendingAccount = null;
		if(strlen($email) > 0) {
			$token = createToken($tokenLen);
			$insertStmt = "INSERT INTO pending_accounts (email,title,token)";
			$insertStmt .= " VALUES('$email','$title','$token')";
			if(mysqli_query($conn,$insertStmt)) {
				$pendingAccount = array('email' => $email, 'token' => $token);
			} else {
				$updateStmt = "UPDATE pending_accounts SET token='$token' WHERE email='$email'";


				if(mysqli_query($conn,$updateStmt) == FALSE) {
					echo "Error " . $insertStmt . "<br>" . mysqli_error($conn);
				} else {
					$pendingAccount = array('email' => $email, 'token' => $token);
				}
			}
		}
		return $pendingAccount;
	}

	function createToken($len) {
		return bin2hex(openssl_random_pseudo_bytes($len));
	}

	function sanitizeInput ($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
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
        $mail->addReplyTo("agonzalez@engr.ucr.edu", "Reply");

        //CC and BCC
        //$mail->addCC("cc@example.com");
        //$mail->addBCC("bcc@example.com");

        //Send HTML or Plain Text email
        $mail->isHTML(true);
        $mail->Subject = "BCOE Voting Account Registration";

        // Compose message body
        $bodyMsg = "<h2>This email is intended for ".$email."<br>";
        $bodyMsg .= "<hr><br>Please follow the link to gain site access: ";
        $voteLink = "<a href='www.engr.ucr.edu/intranet/vote/event/register.php?t=".$token."''>Complete Registration</a><br>";
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

