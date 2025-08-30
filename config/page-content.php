
<?php
       


 if($page=='login'){?>


 <!-- select section  -->
   <div class="fill-form-div login-div" id="next_1">
    <div class="input-div animated fadeIn">
        <h2>Select Your Role</h2>
        <p>Please choose the option that best describes what you want to do:</p>
        <button class="patient-btn" id="patient_btn" onclick="show_patient_sign_in();">I am patient looking for a Doctor</button>
        <button class="patient-btn" id="doctor_btn" onclick="show_caption();">I am Doctor looking for Patients</button>
    </div>
</div>



    <!-- doctor login section  -->
    <div class="fill-form-div login-div" id="next_2">
        <div class="input-div animated fadeIn"> <h2>Doctor Log-In </h2>
            <form action='<script>endPoint</script>' id="loginform" enctype="multipart/form-data" method="post">
                <label><i class="bi-key"></i> Please Enter Your Email Address</label><br><br>
                <input class="input-field" type="email" id="email_address" name="member_id" placeholder="Membership Number"><br><br>
                <label><i class="bi-lock"></i> Please Enter Your Password</label><br><br>
                <input class="input-field" type="password" id="password" name="password" placeholder="Password"><br><br>
                <input name="action" value="login_api" type="hidden" />
                <button class="btn" type="button" id="login_btn" title="Login" onclick="_sign_in()"> LOGIN</button>
                <p class="reset" onclick="_next_page('next_4');">Forgot Password? </p>

                <div class="notification-div">
                    <span>New to Us?</span> <span class="in"  onclick="_next_page('next_5');">SIGN UP </span>
                </div>
                <!-- <div class="under-div" onclick="_next_page('next_3');">
                    Create New Account <i class="bi-arrow-right"></i>
                </div> -->
            </form>
        </div> 
    </div>

        <!-- patient login section  -->
    <div class="fill-form-div login-div" id="next_3">
        <div class="input-div animated fadeIn"> <h2>Patient Log-In </h2>
            <form action='<script>endPoint</script>' id="loginform" enctype="multipart/form-data" method="post">
                <label><i class="bi-key"></i> Enter Your Email Address</label><br><br>
                <input class="input-field" type="email" id="email_address" name="member_id" placeholder="Membership Number"><br><br>
                <label><i class="bi-lock"></i> Enter Password</label><br><br>
                <input class="input-field" type="password" id="password" name="password" placeholder="Password"><br><br>
                <input name="action" value="login_api" type="hidden" />
                <button class="btn" type="button" id="login_btn" title="Login" onclick="_sign_in()"> LOGIN</button>
                <p class="reset" onclick="_next_page('next_4');">Forgot Password? </p>
                <div class="notification-div">
                    <span>New to Us?</span> <span class="in"  onclick="_next_page('next_6');">SIGN UP </span>
                </div>
<!-- 
                <div class="under-div" onclick="_next_page('next_3');">
                    Create New Account <i class="bi-arrow-right"></i>
                </div> -->
            </form>
        </div> 
    </div>




    <!-- pateint-sign-up section -->
    <div class="fill-form-div login-div" id="next_6">
        <div class="input-div">
            <h2>Patient Sign-Up</h2>
            <form action="<script>endPoint</script>" id="signupform" enctype="multipart/form-data" method="post">
                <label><i class="bi-user-o"></i> Create Username</label><br><br>
                <input class="input-field" id="fullname" type="text" name="fullname" placeholder="Kindly Enter the name you will like to be called" /><br><br>

                <!-- <label><i class="bi-mobile-phone"></i> Enter Phone Number</label><br><br>
                <input class="input-field" id="phonenumber" type="tel" name="phonenumber" placeholder="Enter Phone Number" /><br><br> -->

                <label><i class="bi-envelope"></i> Enter Email</label><br><br>
                <input class="input-field" type="email" id="semail" name="semail" placeholder="Enter Email" /><br><br>

                <label><i class="bi-envelope"></i> Select Country Email</label><br><br>
               <select>
                <option disabled> Select a Country</option>
               </select>

                <!-- <label><i class="bi-lock"></i> Create Password</label><br><br>
                <input class="input-field" type="password" id="password" name="password" placeholder="Create Password" onkeyup="_check_password()" /><br><br> -->

                <label><i class="bi-lock"></i> Create Password</label><br><br>
                <input class="input-field" type="password" id="sign-p-password" name="password" placeholder="Create Password" onkeyup="checkPasswordStrength()" /><br><br>

                <div class="pswd_info" style="display:none;">
                    <div class="strength-bar-container" style="width:100%; height:5px; background:#eee; border-radius:5px; overflow:hidden;">
                        <div class="strength-bar" style="width:0%; height:100%; border-radius:5px; transition:width 0.3s;"></div>
                    </div>
                    <p class="strength-text" style="font-size:12px; margin-top:6px; color:#fff;">
                        Password strength: Weak
                    </p>
                    <small class="strength-requirements" style="font-size:11px; color:#fff; display:block;">
                        At least 8 characters required including upper & lower cases, numbers, and special characters
                    </small>
                </div>


                <!-- <div id="password-strength" style="width:100%; height:5px; background:#eee; border-radius:5px; margin:8px 0;">
                    <div id="strength-bar" style="width:0%; height:100%; border-radius:5px;"></div>
                </div>
                <p id="strength-text" style="font-size:12px; margin-top:4px; color:#fff;">Password strength: Weak</p> -->


                <label><i class="bi-lock"></i> Confirm Password</label><br><br>
                <input class="input-field" type="password" id="cpassword" name="cpassword" placeholder="Confirm Password" /><br><br>

          



                <input name="action" value="sign_up" type="hidden" />
                <button class="btn" id="sign_up_btn" type="button" onclick="sign_up_()" title="Sign-Up">Sign-Up</button>

            

            </form>
            <hr>
            <p>Or sign up with</p>
            <div class="social-signup">
                <button class="social-btn google-btn" type="button">
                    <i class="bi-google"></i>
                </button>
            </div>

           
        </div>
    </div>



<!-- proceed reset password section -->

    <div class="fill-form-div login-div" id="next_4">
            <div class="input-div"> 
                <h2>RESET PASSWORD </h2>
                <label><i class="bi-envelope"></i> Enter your Email</label><br><br>
                <input class="input-field" type="text" name="resetemail" placeholder="Enter your Email" id="reset_password_email"/><br><br>
                <button class="btn" type="button"  title="Next" id="reset_pwd_btn" onclick="_proceed_reset_password()"> PROCEED <i class="bi-arrow-right"></i></button>
                <div class="notification-div">
                    <span>Password Remembered?</span> <span class="in"  onclick="_next_page('next_1');">LOG-IN </span>
                </div>
            </div> 
        </div> 
        <?php  }?> 
     

<?php ?>
<!--reset password section  -->


   <!--reset password section  -->


   <div class="fill-form-div reset-pass-form">
            <div class="input-div"> <h2> RESET PASSWORD </h2> 
                <p>HI <span id="admin_name"></span>, kindly enter the OTP sent to your mail (<span id="admin_email"></span>).</p>
                <label><i class="fa fa-envelope"></i> Enter OTP</label><br><br>
                <input class="input-field" type="OTP" name="otp" placeholder="Enter OTP"  input id="cotp" title-div="Enter OTP"/><br><br>
                <div class="notification-div alert-div" style="margin-bottom:0px;">
                    <span><strong>OTP</strong></span> not received? 
                    <span id="admin_id" style="display:none;"></span> <!-- Hidden span for member_id -->
                    <span id="resend" onclick='_resend_otp("resend", document.getElementById("admin_email").innerText)'>
                        <i class="bi-send"></i> <strong>RESEND OTP</strong>
                    </span>
                </div>
                

                <label><i class="fa fa-lock"></i> Create new Password</label><br><br>
                <input class="input-field" type="password" placeholder="Enter New Password"  id="r_password" name="createpassword" onkeyup="_check_password()" title="Confirm Password"/><br><br>
                <label><i class="fa fa-lock"></i> Confirm new Password </label><br><br>
                <input class="input-field" type="password" placeholder="Confirm New Password" id="r_cpassword" name="confirmpassword" title="Confirm Password"/><br><br>


            <!-- <div class="pswd_info">At least 8 charaters required including upper & lower cases and special characters and numbers</div>
            <div id="pswd_info"><span>password not accepted</span></div> -->
            <button class="btn" type="button"  title="Reset" id="finish-reset-btn" onclick="_finish_reset_password()"><i class="bi-check2"></i> Reset Password </button>
        </div>
    </div>


    <!-- reset password complete -->
    <div class="reset-complete-div" id="reset-complete-div" >
        <br /><br /><br /><br /><br />
        <div class="alert-success"><i class="bi-check"></i> PASSWORD RESET SUCCESSFUL!</div>
        <button class="btn" type="button"  title="Log-In" onclick="_next_page('next_1');"><i class="bi-check"></i> Log-In </button>
        
    </div>







