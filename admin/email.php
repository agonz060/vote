<?php

	require_once "mailer/autoload.php";

	//PHPMailer Object
	$mail = new PHPMailer;

	// Enable SMTP debugging
	$mail->SMTPDebug = false;

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
	$mail->addAddress("agonztest@gmail.com", "Armando Gonzalez");
	//$mail->addAddress("recepient1@example.com"); //Recipient name is optional

	//Address to which recipient will reply
	$mail->addReplyTo("agonztest@gmail.com", "Reply");

	//CC and BCC
	//$mail->addCC("cc@example.com");
	//$mail->addBCC("bcc@example.com");

	//Send HTML or Plain Text email
	$mail->isHTML(true);
	$mail->Subject = "Sending email test";
	$mail->Body = "<i>This is the body of the test that will check whether it is possible to send email from a script</i>";
	$mail->AltBody = "Testing plain text body of a message sent from a script";

	if(!$mail->send()) {
	    echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	    echo "Message has been sent successfully";
	}
?>