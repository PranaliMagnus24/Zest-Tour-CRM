<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    
	$mail->isSMTP();    
	/*$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);*/                                  // Set mailer to use SMTP
	$mail->Host = 'Localhost';                       // Specify main and backup server
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'help@zesttourcrm.in';                   // SMTP username
	$mail->Password = 'XA4A24nTS';               // SMTP password
	$mail->setFrom('help@zesttourcrm.in', 'Zest Enquiry');     //Set who the message is to be sent from
	$mail->addReplyTo('help@zesttourcrm.in', 'Zest Enquiry');  //Set an alternative reply-to address
 	$mail->addAddress('swapnil91991@gmail.com');   
            // Name is optional
	$mail->isHTML(true);                                  // Set email format to HTML
	 
	$mail->Subject = "sub";
	$mail->Body    = "mess";
	$mail->AltBody = "dasda";
	 
	if(!$mail->send()) {
	   echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	   exit;
	}
	echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}