
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
           <div class="social-signup">
                <button class="social-btn google-btn" type="button">
                    <i class="bi-google"></i> Login with Google
                </button>
            </div>
              <!-- Divider -->
            <div class="divider">
                <span>OR</span>
            </div>

            <form action='<script>endPoint</script>' id="loginform" enctype="multipart/form-data" method="post">
                <label><i class="bi-key"></i> Please Enter Your Email Address</label><br><br>
                <input class="input-field" type="email" id="doctor_login_email_address" name="doctor_login_email_address" placeholder="Email Address"><br><br>
                <label><i class="bi-lock"></i> Please Enter Your Password</label><br><br>
                <input class="input-field" type="password" id="doctor_login_password" name="doctor_login_password" placeholder="Password"><br><br>
                <input name="action" value="login_api" type="hidden" />
                <button class="btn" type="button" id="login_btn" title="Login" onclick="_sign_in()"> LOGIN</button>
                <p class="reset" onclick="_next_page('next_4');">Forgot Password? </p>

                <div class="notification-div">
                    <span>New to Us?</span> <span class="in"  onclick="_next_page('next_5');">SIGN UP </span>
                </div>
            </form>
        </div> 
    </div>

         <!-- doctor sign up section  -->

     <div class="fill-form-div login-div" id="next_5">
        <div class="input-div">
            <h2>Doctor Sign-Up</h2>
            <div class="social-signup">
              <button class="social-btn google-btn" type="button" 
                        onclick="window.location.href='http://localhost/tele-medicine-base-api?action=google_patient_signup_api'">
                    <i class="bi-google"></i> Sign up with Google
                </button>

            </div>
              <!-- Divider -->
            <div class="divider">
                <span>OR</span>
            </div>

            <form action="<script>endPoint</script>" id="signupform" enctype="multipart/form-data" method="post">
               <label><i class="bi-user-o"></i> Your Name</label><br><br>
                <div class="name-fields">
                    <input class="input-field half" id="doctor_first_name" type="text" name="firstname" placeholder="First Name" />
                    <input class="input-field half" id="doctor_last_name" type="text" name="lastname" placeholder="Last Name" />
                </div><br><br>

                <label><i class="bi-envelope"></i>  Email Address</label><br><br>
                <input class="input-field" type="email" id="doctor_sign_up_email" name="semail" placeholder="Enter Your Email Address" /><br><br>

                <label><i class="bi-phone"></i> Phone Number </label><br><br>
                <input class="input-field" type="text" id="doctor_sign_up_phone" name="sphone" placeholder=" Enter Your Phone Number" /><br><br>

                <label><i class="bi-doc"></i> Primary Medical Speciality </label><br><br>
                <select id="doctor_speciality" name="speciality" onchange="toggleOtherSpeciality()">
                    <option value="" disabled selected>Select Your Speciality</option>
                    <option value="cardiology">Cardiology</option>
                    <option value="dermatology">Dermatology</option>
                    <option value="endocrinology">Endocrinology</option>
                    <option value="family medicine">Family Medicine</option>
                    <option value="gastroenterology">Gastroenterology</option>
                    <option value="internal medicine">Internal Medicine</option>
                    <option value="neurology">Neurology</option>
                    <option value="obstetrics and gynecology">Obstetrics/Gynecology</option>
                    <option value="oncology">Oncology</option>
                    <option value="ophthalmology">Ophthalmology</option>
                    <option value="orthopedics">Orthopedics</option>
                    <option value="pediatrics">Pediatrics</option>
                    <option value="psychiatry">Psychiatry</option>
                    <option value="radiology">Radiology</option>
                    <option value="surgery">Surgery</option>
                    <option value="urology">Urology</option>
                    <option value="other">Other</option>
                </select><br><br>

                <!-- Hidden input initially -->
                <div id="other_speciality_div" style="display:none;">
                    <input type="text" id="other_speciality" name="other_speciality" placeholder="Please specify your speciality" class="input-field"/><br><br>
                </div>

                
                <label><i class="bi-person"></i> Sub-Speciality(ies) </label><br><br>
                <input class="input-field" type="text" id="doctor_sub_speciality" name="sub_speciality" placeholder="Sub-Speciality(ies) if any" /><br><br>

                  <label><i class="bi-person"></i> Years of Experience </label><br><br>
                <input class="input-field" type="text" id="doctor_years_experience" name="years_experience" placeholder="Years of Experience" /><br><br>

                  <label><i class="bi-person"></i> Medical License Number </label><br><br>
                <input class="input-field" type="text" id="doctor_medical_license" name="medical_license" placeholder="Medical License Number" /><br><br>

                  <label><i class="bi-person"></i> License Issuing State/Country </label><br><br>
                <input class="input-field" type="text" id="doctor_license_issuing_state" name="license_issuing_state" placeholder="License Issuing State/Country" /><br><br>


                <label><i class="bi-flag"></i> Select Your Current Country</label><br><br>
                 <select id="doctor_country" name="country">
                    <option disabled selected>Detecting your country...</option>
                </select><br><br>

                <label><i class="bi-lock"></i> Create Password</label><br><br>
                <input class="input-field" type="password" id="sign-D-password" name="password" placeholder="Create Password" onkeyup="checkDocPasswordStrength()" /><br><br>

                <div class="pswd_info2" style="display:none;">
                    <div class="strength-bar-container2" style="width:100%; height:5px; background:#eee; border-radius:5px; overflow:hidden;">
                        <div class="strength-bar2" style="width:0%; height:100%; border-radius:5px; transition:width 0.3s;"></div>
                    </div>
                    <p class="strength-text2" style="font-size:12px; margin-top:6px; color:#fff;">
                        Password strength: Weak
                    </p>
                    <small class="strength-requirements2" style="font-size:11px; color:#fff; display:block;">
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
                <button class="btn" id="sign_up_btn" type="button" onclick="sign_up_()" title="Sign-Up">Sign-Up As a Doctor</button>

            

            </form>
          

           
        </div>
    </div>


        <!-- patient login section  -->
    <div class="fill-form-div login-div" id="next_3">
        <div class="input-div animated fadeIn"> <h2>Patient Log-In </h2>
         <div class="social-signup">
                <button class="social-btn google-btn" type="button">
                    <i class="bi-google"></i> Login with Google
                </button>
            </div>
              <!-- Divider -->
            <div class="divider">
                <span>OR</span>
            </div>

            <form action='<script>endPoint</script>' id="loginform" enctype="multipart/form-data" method="post">
                <label><i class="bi-key"></i> Enter Your Email Address</label><br><br>
                <input class="input-field" type="email" id="patient_email_address" name="member_id" placeholder="Email Address"><br><br>
                <label><i class="bi-lock"></i> Enter Password</label><br><br>
                <input class="input-field" type="password" id="patient_login_password" name="password" placeholder="Password"><br><br>
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
            <div class="social-signup">
                <button class="social-btn google-btn" type="button" 
                        onclick="patient_google_sign();">
                    <i class="bi-google"></i> Sign up with Google
                </button>
            </div>
              <!-- Divider -->
            <div class="divider">
                <span>OR</span>
            </div>

            <form action="<script>endPoint</script>" id="signupform" enctype="multipart/form-data" method="post">
               <label><i class="bi-user-o"></i> Your Name</label><br><br>
                <div class="name-fields">
                    <input class="input-field half" id="patient_first_name" type="text" name="firstname" placeholder="First Name" />
                    <input class="input-field half" id="patient_last_name" type="text" name="lastname" placeholder="Last Name" />
                </div><br><br>

                <label><i class="bi-envelope"></i> Email Address</label><br><br>
                <input class="input-field" type="email" id="patient_sign_up_email" name="semail" placeholder="Enter Your Email Address" /><br><br>

                <label><i class="bi-flag"></i> Select Country</label><br><br>
                 <select id="country" name="country">
                    <option disabled selected>Detecting your country...</option>
                </select><br><br>

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
                <button class="btn" id="sign_up_btn" type="button" onclick="sign_up_()" title="Sign-Up">Sign-Up as a Patient</button>

            

            </form>
          

           
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

    <script>
                    // Fetch user's country from IP API
        fetch("https://ipapi.co/json/")
            .then(response => response.json())
            .then(data => {
                let countryName = data.country_name;
                let countrySelect = document.getElementById("country");
                let doctorCountrySelect = document.getElementById("doctor_country");

                // List of all countries
                let countries = [
                    "Afghanistan","Albania","Algeria","Andorra","Angola","Argentina","Armenia","Australia","Austria",
                    "Bahamas","Bahrain","Bangladesh","Barbados","Belgium","Belize","Benin","Bhutan","Bolivia","Botswana","Brazil","Bulgaria","Burkina Faso","Burundi",
                    "Cambodia","Cameroon","Canada","Cape Verde","Chad","Chile","China","Colombia","Costa Rica","Croatia","Cuba","Cyprus","Czech Republic",
                    "Denmark","Dominica","Dominican Republic",
                    "Ecuador","Egypt","El Salvador","Estonia","Eswatini","Ethiopia",
                    "Fiji","Finland","France",
                    "Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guyana",
                    "Haiti","Honduras","Hungary",
                    "Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy",
                    "Jamaica","Japan","Jordan",
                    "Kazakhstan","Kenya","Kuwait","Kyrgyzstan",
                    "Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Lithuania","Luxembourg",
                    "Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Mauritania","Mauritius","Mexico","Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar",
                    "Namibia","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway",
                    "Oman",
                    "Pakistan","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal",
                    "Qatar",
                    "Romania","Russia","Rwanda",
                    "Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Somalia","South Africa","South Korea","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria",
                    "Taiwan","Tajikistan","Tanzania","Thailand","Togo","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan",
                    "Vatican City","Venezuela","Vietnam",
                    "Yemen",
                    "Zambia","Zimbabwe"
                ];

                // Clear previous options
                countrySelect.innerHTML = "";
                doctorCountrySelect.innerHTML = "";

                // Populate countries
                countries.forEach(country => {
                    let option = document.createElement("option");
                    option.value = country;
                    option.textContent = country;
                    if (country === countryName) {
                        option.selected = true;
                    }
                    countrySelect.appendChild(option);
                });

                // Populate doctor countries
                countries.forEach(country => {
                    let option = document.createElement("option");
                    option.value = country;
                    option.textContent = country;
                    if (country === countryName) {
                        option.selected = true;
                    }
                    doctorCountrySelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error("IP detection failed:", error);
                let countrySelect = document.getElementById("country");
                countrySelect.innerHTML = "<option disabled selected>Select a Country</option>";
            })
            .catch(error => {
                console.error("IP detection failed:", error);
                let doctorCountrySelect = document.getElementById("doctor_country");
                doctorCountrySelect.innerHTML = "<option disabled selected>Select a Country</option>";
            });
    </script>





