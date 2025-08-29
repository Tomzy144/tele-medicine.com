<?php
	//   $array=$callclass->_get_setup_backend_settings_detail($conn, 'BK_ID001');
	//   $fetch = json_decode($array, true);
	//   $smtp_host=$fetch[0]['smtp_host'];
	//   $smtp_username=$fetch[0]['smtp_username'];
	//   $smtp_password=$fetch[0]['smtp_password'];
	//   $smtp_port=$fetch[0]['smtp_port'];
	//   $sender_name=$fetch[0]['sender_name'];
	//   $support_email=$fetch[0]['support_email'];
	//   $currentDate=date("l, d F Y");




//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
// require 'vendor/autoload.php';




require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer;




include '../code.php';
	
	// $otp = 11122;
	// $email = 'oyemadetomiwa@gmail.com';
	
	$fullname ="Tomiwa oyemade";
	$smtp_username = 'oyemadetomiwa77@gmail.com';
	$smtp_password="rrlu gvws fmug wgwd";
	$sender_name = "oyemadetomiwa77@gmail.com";
	$smtp_port = 587;//465
	$smtp_host='smtp.gmail.com';



	// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    // $mail->isSMTP();                                            //Send using SMTP
    // $mail->Host       = 'smtp.gmail.com';                    //Set the SMTP server to send through
    // $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    // $mail->Username   = 'oyemadetomiwa77@gmail.com';                     //SMTP username
    // $mail->Password   = 'rrlu gvws fmug wgwd';                               //SMTP password
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS ;            //Enable implicit TLS encryption
    // $mail->Port       = 587;    




		// require 'mail/PHPMailer/PHPMailerAutoload.php';
		
		// $mail = new PHPMailer;
		$mail->SMTPDebug = 0;                               // Enable verbose debug output
		
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $smtp_host;  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $smtp_username;                 // SMTP username
		$mail->Password = $smtp_password;                           // SMTP password
		$mail->SMTPSecure =  PHPMailer::ENCRYPTION_STARTTLS;                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $smtp_port;                                    // TCP port to connect to
		
		// $mail->SMTPOptions = array(
		// 	'ssl' => array(
		// 	'verify_peer' => false,
		// 	'verify_peer_name' => false,
		// 	'allow_self_signed' => true
		// 	)
		// );
		$mail->setFrom($smtp_username, $sender_name);
		$mail->WordWrap = 50;   
		$mail->isHTML(true);                                  // Set email format to HTML
?>

















<?php 
if ($mail_to_send=='send_reset_password_otp'){
$reciever_name=$fullname;			  
$message='
<div style="width:90%; max-width:500px; margin:auto; height:auto;">
	<img src="cid:logo" >
	<div style="padding:15px; font-family:16px;">
	<p>
		Dear <strong >'.$reciever_name.'</strong> ('.$email.'),<br>
		Your One Time Password (OTP) is <span style="color:#F00;">'.$otp.'</span>.<br><br>
	</p>
	
	<p>Please  note that this OTP works for you with 10min from the time you recieve it. Thanks.</p>
	
	<p>
	An Online Server Monitoring System. We ProvideFast & Secure
	Server and Domain Monitoring!
	</p>
	<p>
	<strong>'.$thename.' Management.</strong><br> Mail Sent '.$currentDate.'. 
	</p>
	</div>
	<div  style="min-height:30px;background:#333;text-align:left;color:#FFF;line-height:20px; padding:20px 10px 20px 50px;">
	&copy; All Right Reserve. <br>'.$thename.'.</div>
</div>
';

///

$send_to=$email;
$subject="Reset Password OTP";

$mail->AddAddress($send_to, $reciever_name);
$mail->addAddress($support_email, $sender_name);// Name is optional
$mail->addAddress('oyemadetomiwa77@gmail.com', 'Oyemade Tomiwa');// Name is optional
$mail->addReplyTo($smtp_username, $sender_name); // reply to the sender email

$mail->Subject = $subject;
$mail->addEmbeddedImage('mail/img/logo.png', 'logo');
$mail->Body = $message;
$mail->AltBody = strip_tags($message);

if(!$mail->send()){
	echo 'OTP cannot be sent via gmail due to no or stable internet.';
}
}
?>

