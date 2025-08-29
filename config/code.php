
<?php include '../../config/connection.php';?>
<?php
// <!-- for checking action and page  -->
  	$action=$_POST['action'];
	  switch ($action){
	  
	case 'get_page':
	$page=$_POST['page'];
	include 'page-content.php';
	break;


	  
 	case 'login_check': // for user login
		$email=trim($_POST['email']);
	///	$temp_password=trim(($_POST['password']));
		$password=trim(($_POST['password']));
			$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE `email`='$email' AND `password`='$password'");
			$usercount = mysqli_num_rows($query);
			if ($usercount>0){
				$usersel=mysqli_fetch_array($query);
				$staff_id=$usersel['staff_id'];
				$status_id=$usersel['status_id'];
				
					if ($status_id==1){
						$check=1; ///// account is active
					}else if($status_id==2){
						$check=2; ///// account is suspended
					}else {
						$check=0;
					}
			}else{
				$check=0;
			}
							
			echo json_encode(array("check" => $check, )); 
	break;


	case 'login': // login from index
		$userquery = mysqli_query ($conn,"SELECT * FROM `staff_tab` WHERE email = '$email' AND `password` = '$spass' AND status_id=1");
				$usersel=mysqli_fetch_array($userquery);
				$staff_id=$usersel['staff_id'];
				$_SESSION['staff_id'] = $staff_id;
				$s_staff_id=$_SESSION['staff_id'];
				mysqli_query($conn,"UPDATE `staff_tab` SET last_login=NOW() WHERE staff_id='$s_staff_id'"); //// update last login
				sleep(1);
		?>
					<script>
					window.parent(location="../a/");
					</script>
		<?php
			
	break;


	case 'proceed_reset_password':
		$email=$_POST['email'];
		/////////// confirm user exitence//////////////////////////////////
		$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE email='$email'");
				$checkemail=mysqli_num_rows($query);
				if ($checkemail>0){
				  $fetch=mysqli_fetch_array($query);
					$staff_id= $fetch['staff_id'];
					$status_id= $fetch['status_id'];
					if ($status_id==1){
						$check=1; /// user  Active
					}else if($status_id==2){
						$check=2; /// user Suspended
				}else{
					$check=0; /// user Not Exist
				}

			}else{
				$check=0; /// user Not Exist
			}
		  ////////sending json///////////////////////////
				  echo json_encode(array("check" => $check,"staff_id" => $staff_id)); 
	break;

	case 'reset_password':
		$staff_id=$_POST['staff_id'];		  
		$user_array=$callclass->_get_staff_details($conn, $staff_id);
		$u_array = json_decode($user_array, true);
		$fullname= $u_array[0]['fullname'];
		$email= $u_array[0]['email'];
  
		  $otp = rand(111111,999999);
		  ////////////////update user OTP///////////////
		  mysqli_query($conn,"UPDATE staff_tab SET otp='$otp' WHERE staff_id ='$staff_id'") or die("cannot update staff_tab");
		  ////////////////send OTP true email///////////////
		  $mail_to_send='send_reset_password_otp';
		  require_once('mail/mail.php');

		
/////////////////////////////////////////////

		$page=$action;
	 require_once('page-content.php');
	break;


	case 'resend_otp':
		$staff_id=$_POST['staff_id'];		  
		$user_array=$callclass->_get_staff_details($conn, $staff_id);
		$u_array = json_decode($user_array, true);
		$fullname= $u_array[0]['fullname'];
		$email= $u_array[0]['email'];
		
		$otp = rand(111111,999999);
		////////////////update user OTP///////////////
		mysqli_query($conn,"UPDATE staff_tab SET otp='$otp' WHERE staff_id ='$staff_id'")or die("cannot update staff_tab");
		////////////////send OTP true email///////////////
		$mail_to_send='send_reset_password_otp';
		require_once('page-content.php');
		require_once('mail/mail.php');

		// require "mail/PHPMailer/phpmaileroauth.php";









	break;	


	case 'finish_reset_password':
		$staff_id=trim($_POST['staff_id']);
		$password=($_POST['password']);
		$otp=trim($_POST['otp']); 
		
		$fetch=$callclass->_get_staff_details($conn, $staff_id);
		$array = json_decode($fetch, true);
		$fullname=$array[0]['fullname'];
		$db_otp=$array[0]['otp'];
		$role_id=$array[0]['role_id'];
		
		  if ($otp==$db_otp){ ///// check 1
		  mysqli_query($conn,"UPDATE staff_tab SET password='$password' WHERE staff_id='$staff_id'")or die (mysqli_error($conn));
		  $check=1;
		  }else{						
		  $check=0;
		  }
		  echo json_encode(array("check" => $check)); 
	  break;
  
	   case 'password_reset_completed':
		$page=$action;
	  	require_once('../config/page-content.php');
	  break;




// to check if user already exists with that email
	case 'sign_up_check':
		$semail = trim($_POST['semail']);
		$password = trim($_POST['password']);
		
		// Use prepared statements to prevent SQL injection
		$stmt = mysqli_prepare($conn, "SELECT * FROM staff_tab WHERE `email` = ? AND `password` = ?");
		mysqli_stmt_bind_param($stmt, "ss", $semail, $password);
		mysqli_stmt_execute($stmt);
		$query_result = mysqli_stmt_get_result($stmt);
		
		$usercount = mysqli_num_rows($query_result);
		
		if ($usercount > 0) {
			$usersel = mysqli_fetch_array($query_result);
			$staff_id = $usersel['staff_id'];
			$status_id = $usersel['status_id'];
			$user_email = $usersel['email'];
			
			if (($status_id == 1) && ($user_email == $semail)) {
				$check = 1; // Account is active
			} else if ($status_id == 2) {
				$check = 2; // Account is suspended
			} else {
				$check = 0;
			}
		} else {
			$check = 0; // Invalid credentials
		}
		
		echo json_encode(array("check" => $check));
		break;
	






		case 'sign_up':
			// Extracting values from the POST data
			$staff_id = trim($_POST['staff_id']);
			$fullname = trim($_POST['fullname']);
			$password = $_POST['password'];
			$phonenumber = trim($_POST['phonenumber']);
			$status_id = trim($_POST['status_id']);
			$semail = trim($_POST['semail']);

			
			// Query to check if email and password combination exists
			$email_query = mysqli_query($conn, "SELECT * FROM staff_tab WHERE `email`='$semail' AND `password`='$password' And `staff_id` ='$staff_id'");
			$email_count = mysqli_num_rows($email_query);
			
		//   // Check if the email already exists
		//   $email_query = mysqli_query($conn, "SELECT * FROM staff_tab WHERE `email`='$semail'");
		//   $email_count = mysqli_num_rows($email_query);
		  
		  if ($email_count > 0) {
			  // Email already exists
			  $check = 1;
		  } else {
			  // Email doesn't exist, proceed with sign-up
			  // ... (rest of your sign-up process)
			
			// Generating a staff ID using a counter
			$counter_id = "STF";
			$counter_query = mysqli_query($conn, "SELECT counter_value+1 AS counter_value FROM counter_tab WHERE counter_id='STF'");
			$fetch_counter_query = mysqli_fetch_array($counter_query);
			$counter_value = $fetch_counter_query['counter_value'];
			$staff_id = "STF" . $counter_value;
			mysqli_query($conn, "UPDATE counter_tab SET counter_value='$counter_value' WHERE counter_id='STF'");
			
			
			  // Insert new staff record into the database
			  mysqli_query($conn, "INSERT INTO `staff_tab` (`staff_id`, `fullname`, `email`, `phonenumber`, `status_id`, `passport`, `password`, `date`)
			   VALUES ('$staff_id', '$fullname', '$semail', '$phonenumber', '1', '', '$password', NOW())");
			  
			  // Set check value to indicate successful sign-up
			  $check = 0;
			sleep(1);
			// Redirecting the user after sign-up
			?>
			<script>
				window.parent(location="../a/");
			</script>
			<?php
		}
			break;
		
		



	
	}


?>