function _get_page(page){
    $('#more-info').html('<div class="ajax-loader"><img src="all-images/images/ajax-loader.gif"/></div>').fadeIn('fast');
    action='get_page';
    var dataString ='action='+ action+'&page='+ page;
    $.ajax({
    type: "POST",
    url: "config/page-content.php",
    data: dataString,
    cache: false,
    success: function(html){
        $('#more-info').html(html);
    }
    });
}

function _next_page(next_id) {
$('.login-div').hide();
$('#'+next_id).fadeIn(1000);
}

function show_caption() {
    $('#doctor_prompt').show(); 
    _next_page('next_2');
}


function show_patient_sign_in(){
   _next_page('next_3');
}


function checkPasswordStrength() {
    let password = $('#sign-p-password').val();
    let strengthBar = $('.pswd_info .strength-bar');
    let strengthText = $('.pswd_info .strength-text');
    let requirements = $('.pswd_info .strength-requirements');

    if (password.length === 0) {
        $('.pswd_info').fadeOut(300);
        strengthBar.css('width', '0%');
        strengthText.text('');
        requirements.fadeIn(300); // show again when empty
        return;
    }

    $('.pswd_info').fadeIn(300);

    let strength = 0;
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;

    strengthBar.css('width', strength + '%');

    if (strength <= 25) {
        strengthBar.css('background', 'red');
        strengthText.text('Password strength: Very Weak');
        requirements.fadeIn(300);
    } else if (strength <= 50) {
        strengthBar.css('background', 'orange');
        strengthText.text('Password strength: Weak');
        requirements.fadeIn(300);
    } else if (strength <= 75) {
        strengthBar.css('background', 'yellowgreen');
        strengthText.text('Password strength: Good');
        requirements.fadeIn(300);
    } else {
        strengthBar.css('background', 'green');
        strengthText.text('Password strength: Strong');
        requirements.fadeOut(300); // fade away smoothly when password is strong
    }
}

function checkDocPasswordStrength() {
    let password = $('#sign-D-password').val();
    let strengthBar = $('.pswd_info2 .strength-bar2');
    let strengthText = $('.pswd_info2 .strength-text2');
    let requirements = $('.pswd_info2 .strength-requirements2');

    if (password.length === 0) {
        $('.pswd_info2').fadeOut(300);
        strengthBar.css('width', '0%');
        strengthText.text('');
        requirements.fadeIn(300); // show again when empty
        return;
    }

    $('.pswd_info2').fadeIn(300);

    let strength = 0;
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;

    strengthBar.css('width', strength + '%');

    if (strength <= 25) {
        strengthBar.css('background', 'red');
        strengthText.text('Password strength: Very Weak');
        requirements.fadeIn(300);
    } else if (strength <= 50) {
        strengthBar.css('background', 'orange');
        strengthText.text('Password strength: Weak');
        requirements.fadeIn(300);
    } else if (strength <= 75) {
        strengthBar.css('background', 'yellowgreen');
        strengthText.text('Password strength: Good');
        requirements.fadeIn(300);
    } else {
        strengthBar.css('background', 'green');
        strengthText.text('Password strength: Strong');
        requirements.fadeOut(300); // fade away smoothly when password is strong
    }
}

function toggleOtherSpeciality() {
    let select = document.getElementById("doctor_speciality");
    let otherDiv = document.getElementById("other_speciality_div");

    if (select.value === "other") {
        otherDiv.style.display = "block";
    } else {
        otherDiv.style.display = "none";
    }

}

function patient_google_sign() {
    $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: 'google_patient_signup_api_init' },
        dataType: "json",
        cache: false,
        success: function(response) {
            if (response.status === "redirect" && response.url) {
                // Redirect to Google login page
                window.location.href = response.url;
              
            } else {
                alert(response.message || "Unable to start Google signup.");
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error (init):", status, error);
            console.error("Response Text:", xhr.responseText);
            alert("Error occurred: " + error + "\nCheck console for details.");
        }
    });
}



function patient_google_login() {
    $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: 'patient_google_login_init' },
        dataType: "json",
        cache: false,
        success: function(response) {
            if (response.status === "redirect" && response.url) {
                // Redirect to Google login page
                window.location.href = response.url;
              
            } else {
                alert(response.message || "Unable to start Google signup.");
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error (init):", status, error);
            console.error("Response Text:", xhr.responseText);
            alert("Error occurred: " + error + "\nCheck console for details.");
        }
    });
}

function validateEmail() {
  var email = $('#patient_sign_up_email').val().trim();
  var email_error = $('#email_error');

  // Simple regex for email format
  var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (email === "") {
    email_error.text("Email is required").show();
  } else if (!regex.test(email)) {
    email_error.text("Invalid email format").show();
  } else {
    email_error.hide();
  }
}

function DocValidateEmail(){
  var email = $('#doctor_sign_up_email').val().trim();
   var email_error = $('#doc_email_error');

  // Simple regex for email format
  var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (email === "") {
    email_error.text("Email is required").show();
  } else if (!regex.test(email)) {
    email_error.text("Invalid email format").show();
  } else {
    email_error.hide();
  }
}



function patient_sign_up_() {
  var action = 'patient_sign_up';
  var btn_text = $('#sign_up_btn').html();
  $('#sign_up_btn').html('Processing...');
  document.getElementById('sign_up_btn').disabled = true;

  // Collect inputs
  var patient_firstname = $('#patient_first_name').val().trim();
  var patient_lastname  = $('#patient_last_name').val().trim();
  var patient_email     = $('#patient_sign_up_email').val().trim();
  var patient_phone     = $('#patient_phone').val().trim();
  var patient_dob       = $('#patient_dob').val();
  var patient_gender    = $('#patient_gender').val();
  var patient_country   = $('#country').val();
  var patient_password  = $('#sign-p-password').val().trim();
  var patient_cpassword = $('#patient_cpassword').val().trim();

  // Required fields check
  if (
    patient_firstname == "" ||
    patient_lastname == "" ||
    patient_email == "" ||
    patient_phone == "" ||
    patient_dob == "" ||
    patient_gender == "" ||
    patient_country == ""  ||
    patient_password == "" ||
    patient_cpassword == ""
  ) {
    
    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> All fields are required!')
      .fadeIn(500).delay(5000).fadeOut(100);
     

    
    resetBtn(btn_text);
    return;
  }

  // Email validation
  var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!regex.test(patient_email)) {
    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Enter a valid email address!')
      .fadeIn(500).delay(5000).fadeOut(100);
    resetBtn(btn_text);
    return;
  }

  // Phone validation (basic — Nigerian 11 digits or general international format)
  var phoneRegex = /^[0-9\-\+\s]{7,15}$/;
  if (!phoneRegex.test(patient_phone)) {
    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Enter a valid phone number!')
      .fadeIn(500).delay(5000).fadeOut(100);
    resetBtn(btn_text);
    return;
  }

  // Password strength check
  var strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
  if (!strongPassword.test(patient_password)) {
    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Password must be at least 8 characters long and include uppercase, lowercase, number, and special character!')
      .fadeIn(500).delay(5000).fadeOut(100);
    resetBtn(btn_text);
    return;
  }

  // Confirm password match
  if (patient_password !== patient_cpassword) {
    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Passwords do not match!')
      .fadeIn(500).delay(5000).fadeOut(100);
    resetBtn(btn_text);
    return;
  }

  // Prepare form data
  var form = $('#signupform')[0];
  var formData = new FormData(form);
  formData.append("action", action);

  $.ajax({
    type: "POST",
    url: endPoint,
    data: formData,
    dataType: "json",
    processData: false,
    contentType: false,
    cache: false,
    success: function (response) {
      if (response.status === "success") {
        $('#success-div').html('<div><i class="bi-check"></i></div> ACCOUNT CREATED SUCCESSFULLY!')
          .fadeIn(500).delay(5000).fadeOut(100);
        $('#signupform')[0].reset();
        setTimeout(function () {
           sessionStorage.setItem('session_id', response.session_id);
          window.location.href = response.redirect_url;
        }, 2000);
      } else {
        $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + response.message)
          .fadeIn(500).delay(5000).fadeOut(100);
      }
      resetBtn(btn_text);
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error:', status, error);
      $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.')
        .fadeIn(500).delay(5000).fadeOut(100);
      resetBtn(btn_text);
    }
  });
}

// Helper to reset button state
function resetBtn(btn_text) {
  $('#sign_up_btn').html(btn_text);
  document.getElementById('sign_up_btn').disabled = false;
}


function patient_sign_in(){ 
    var patient_email = $('#patient_login_email').val();
    var patient_password = $('#patient_login_password').val();
    if((patient_email!='')&&(patient_password!='')){
        var action = 'patient_login_api';
        var btn_text = $('#patient_login_btn').html();
        $('#patient_login_btn').html('Authenticating...');
        document.getElementById('patient_login_btn').disabled = true;
        var dataString = {
            action: action,
            patient_email: patient_email,
            patient_password: patient_password
        };
        $.ajax({
            type: "POST",
            url: endPoint,
            dataType: "json",
            data: dataString,
            cache: false,
            success: function (response) {
                if (response.success === true) {
                    $('#success-div').html('<div><i class="bi-check"></i></div> LOGIN SUCCESSFUL!').fadeIn(500).delay(5000).fadeOut(100);
                    sessionStorage.setItem('session_id', response.session_id);
                    setTimeout(function() {
                        window.location.href = response.redirect_url;
                    }, 2000);
                } else {
                    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + response.message)
                        .fadeIn(500).delay(5000).fadeOut(100);
                }
                $('#patient_login_btn').html(btn_text);
                document.getElementById('patient_login_btn').disabled = false;
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.')
                    .fadeIn(500).delay(5000).fadeOut(100);
                $('#patient_login_btn').html(btn_text);
                document.getElementById('patient_login_btn').disabled = false;
            }
        });
    }else{
        $('#warning-div').fadeIn(500).delay(5000).fadeOut(100);
    }
}




function doctor_sign_up_() {
    var firstname             = $('#doctor_first_name').val();
    var lastname              = $('#doctor_last_name').val();
    var semail                = $('#doctor_sign_up_email').val();
    var sphone                = $('#doctor_sign_up_phone').val();
    var speciality            = $('#doctor_speciality').val();
    var other_speciality      = $('#other_speciality').val();
    var sub_speciality        = $('#doctor_sub_speciality').val();
    var years_experience      = $('#doctor_years_experience').val();
    var medical_license       = $('#doctor_medical_license').val();
    var license_issuing_state = $('#doctor_license_issuing_state').val();
    var country               = $('#doctor_country').val();
    var password              = $('#sign-D-password').val();
    var cpassword             = $('#doctor_cpassword').val();

    var action = "doctor_sign_up";

        var requiredFields = [
          firstname,
          lastname,
          semail,
          sphone,
          speciality,
          (speciality === "other" ? other_speciality : "ok"), // if "other", must fill it
          sub_speciality,
          years_experience,
          medical_license,
          license_issuing_state,
          country,
          password,
          cpassword
      ];

      // Check if any field is empty
      for (var i = 0; i < requiredFields.length; i++) {
          if (requiredFields[i] === "" || requiredFields[i] === null) {
              $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> All fields must be filled.')
                  .fadeIn(500).delay(5000).fadeOut(100);
              return;
          }
      }

    if (password !== cpassword) {
        $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Passwords do not match.')
            .fadeIn(500).delay(5000).fadeOut(100);
        return;
    }

    var formData = {
        action: action,
        firstname: firstname,
        lastname: lastname,
        semail: semail,
        sphone: sphone,
        speciality: speciality,
        other_speciality: other_speciality,
        sub_speciality: sub_speciality,
        years_experience: years_experience,
        medical_license: medical_license,
        license_issuing_state: license_issuing_state,
        country: country,
        password: password
    };

    var btn_text = $('#doctor_sign_up_btn').html();
    $('#doctor_sign_up_btn').html('Submitting...');
    document.getElementById('doctor_sign_up_btn').disabled = true;

    $.ajax({
        type: "POST",
        url: endPoint,
        data: formData,
        dataType: "json",
        success: function(response) {
            if (response.success === true) {
                $('#success-div').html('<div><i class="bi-check"></i></div> Registration successful!')
                    .fadeIn(500).delay(5000).fadeOut(100);
                    sessionStorage.setItem('session_id', response.session_id);
                setTimeout(function() {

                    window.location.href = response.redirect_url;
                }, 2000);
            } else {
                $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + response.message)
                    .fadeIn(500).delay(5000).fadeOut(100);
            }
            $('#doctor_sign_up_btn').html(btn_text);
            document.getElementById('doctor_sign_up_btn').disabled = false;
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.')
                .fadeIn(500).delay(5000).fadeOut(100);
            $('#doctor_sign_up_btn').html(btn_text);
            document.getElementById('doctor_sign_up_btn').disabled = false;
        }
    });
}



function doctor_sign_in() {
    let email = document.getElementById("doctor_login_email_address").value.trim();
    let password = document.getElementById("doctor_login_password").value.trim();
    let loginBtn = document.getElementById("doctor_login_btn");

    if (email === "" || password === "") {
        $('#warning-div')
            .html('<div><i class="bi-exclamation-triangle"></i></div> Email and password are required.')
            .fadeIn(500).delay(5000).fadeOut(100);
        return;
    }

    loginBtn.disabled = true;
    loginBtn.innerText = "Logging in...";

    let formData = new FormData();
    formData.append("action", "doctor_login_api");
    formData.append("doctor_login_email_address", email);
    formData.append("doctor_login_password", password);

    fetch(endPoint, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#success-div')
                .html('<div><i class="bi-check"></i></div> WELCOME BACK!')
                .fadeIn(500).delay(2000).fadeOut(100);

            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 2000);
        } else {
            $('#warning-div')
                .html('<div><i class="bi-exclamation-triangle"></i></div> ' + (data.message || "Login failed. Try again."))
                .fadeIn(500).delay(5000).fadeOut(100);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        $('#warning-div')
            .html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.')
            .fadeIn(500).delay(5000).fadeOut(100);
    })
    .finally(() => {
        loginBtn.disabled = false;
        loginBtn.innerText = "LOGIN";
    });
}








function _proceed_reset_password() {
  var input = $('#reset_password_email').val();
  var isEmail = input.includes('@') && input.includes('.');
  if (input === '' || !isEmail) {
      $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Please enter a valid Email Address<br /><span></span>')
          .fadeIn(500).delay(5000).fadeOut(100);
  } else {
      var btn_text = $('#reset_pwd_btn').html();
      $('#reset_pwd_btn').html('Processing...');
      document.getElementById('reset_pwd_btn').disabled = true;
      var action = 'proceed_reset_password';
      var dataString = 'action=' + action + '&contact=' + input;
      $.ajax({
          type: "POST",
          url: endPoint,
          data: dataString,
          cache: false,
          dataType: 'json',
          success: function(response) {
              var scheck = response.status_id;
              var admin_email = response.admin_email;
              if (scheck == 1 && response.medium == "email") {
                  _reset_password(admin_email);
              } else if (scheck == 2) {
                  $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Account Suspended<br /><span>Contact the admin for help</span>')
                      .fadeIn(500).delay(5000).fadeOut(100);
              } else {
                  $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div>Error: Email NOT found!<br /><span>Invalid Email Address</span>')
                      .fadeIn(500).delay(5000).fadeOut(100);
              }
              $('#reset_pwd_btn').html(btn_text);
              document.getElementById('reset_pwd_btn').disabled = false;
          },
          error: function(xhr, status, error) {
              console.error("AJAX Error: ", error);
              $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again later.<br /><span></span>')
                  .fadeIn(500).delay(5000).fadeOut(100);
              $('#reset_pwd_btn').html(btn_text);
              document.getElementById('reset_pwd_btn').disabled = false;
          }
      });
  }
}

function _reset_password(admin_email) {
  var action = 'reset_password';
  var originalContent = $('#next_2').html();
  $('#next_2').html('<div class="loading" style="display:block;"><div class="length"></div><div class="length"></div><div class="length"></div><div class="length"></div><div class="length"></div><div class="length last"></div></div>').fadeIn(500);
  var dataString = 'action=' + action + '&admin_email=' + admin_email;
  $.ajax({
      type: "POST",
      url: endPoint,
      data: dataString,
      cache: false,
      success: function(response) {
          try {
              var result = typeof response === "string" ? JSON.parse(response) : response;
              if (result.success) {
                  $('#next_2').html("");
                  $('#admin_name').text(result.admin_name);
                  $('#admin_email').text(result.admin_email);
                  $('#success-div').html('<div><i class="bi-check"></i></div>' + result.message + '!').fadeIn(500).delay(5000).fadeOut(100);
                  var show = document.querySelector(".reset-pass-form");
                  if (show) {
                      show.style.display = "block";
                  }
              } else {
                  $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div>' + result.message + '<br /><span></span>').fadeIn(500).delay(5000).fadeOut(100);
                  setTimeout(function() {
                      $('#next_2').html(originalContent).fadeIn(500);
                  }, 4000);
              }
          } catch (e) {
              console.error("Error parsing response: ", e);
              $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div>Error processing request.<br /><span></span>').fadeIn(500).delay(5000).fadeOut(100);
              setTimeout(function() {
                  $('#next_2').html(originalContent).fadeIn(500);
              }, 4000);
          }
      },
      error: function(xhr, status, error) {
          console.error("AJAX Error: ", error);
          $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div>An error occurred. Please try again later.<br /><span></span>').fadeIn(500).delay(5000).fadeOut(100);
          setTimeout(function() {
              $('#next_2').html(originalContent).fadeIn(500);
          }, 4000);
      }
  });
}

function _check_password(){
    var password = $('#r_password').val();
    if (password==''){
    $('#pswd_info').hide();
    $('.pswd_info').fadeIn(500);
    }else{
    $('.pswd_info').hide();
        if(password.length<8){
             $('#pswd_info').fadeIn(500);
        }else{
            if (password.match(/^(?=[^A-Z]*[A-Z])(?=[^!"#$%&'()*+,-.:;<=>?@[\]^_`{|}~]*[!"#$%&'()*+,-.:;<=>?@[\]^_`{|}~])(?=\D*\d).{8,}$/)) {
                $('#pswd_info').hide();
              } else {
                 $('#pswd_info').fadeIn(500);
              }
        }
    }
}

function _resend_otp(ids, admin_email) {
  var btn_text = $('#' + ids).html();
  $('#' + ids).html('SENDING...');
  var action = 'resend_otp';
  var dataString = 'action=' + action + '&admin_email=' + admin_email;
  $.ajax({
      type: "POST",
      url: endPoint,
      data: dataString,
      cache: false,
      success: function(response) {
          try {
              var result = typeof response === "string" ? JSON.parse(response) : response;
              if (result.success) {
                  $('#success-div').html('<div><i class="bi-check"></i></div> OTP SENT<br /><span>Check your email inbox or spam</span>').fadeIn(500).delay(5000).fadeOut(100);
              } else {
                  $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div>' + result.message + '<br /><span></span>').fadeIn(500).delay(5000).fadeOut(100);
              }
          } catch (e) {
              console.error("Error parsing response: ", e);
              $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Error sending OTP. Please try again.<br /><span></span>').fadeIn(500).delay(5000).fadeOut(100);
          }
          $('#' + ids).html(btn_text);
      },
      error: function(xhr, status, error) {
          console.error("AJAX Error: ", error);
          $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Network error. Please try again later.<br /><span></span>').fadeIn(500).delay(5000).fadeOut(100);
          $('#' + ids).html(btn_text);
      }
  });
}

function _finish_reset_password(){
  var otp = $('#cotp').val();
  var password = $('#r_password').val();
  var cpassword = $('#r_cpassword').val();
  var admin_email= document.getElementById("admin_email").innerText;
  if((otp=='')||(password=='')||(cpassword=='')){
      $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Please Fill All Fields<br /><span>Fields cannot be empty</span>').fadeIn(500).delay(5000).fadeOut(100);
  }else{
          if(password!=cpassword){
              $('#not-success-div').html('<div><i class="bi-x-circle"></i></div> Password NOT Match<br /><span>Check the password and try again</span>').fadeIn(500).delay(5000).fadeOut(100);
          }else{
          if ((password.match(/^(?=[^A-Z]*[A-Z])(?=[^!"#$%&'()*+,-.:;<=>?@[\]^_`{|}~]*[!"#$%&'()*+,-.:;<=>?@[\]^_`{|}~])(?=\D*\d).{8,}$/))&&(password.length>=8)) {
          var btn_text=$('#finish-reset-btn').html();
          $('#finish-reset-btn').html('PROCESSING...');
          document.getElementById('finish-reset-btn').disabled=true;
      var action='finish_reset_password';
      var dataString ='action='+ action+'&admin_email='+ admin_email+'&otp='+ otp+'&password='+ password;
          $.ajax({
          type: "POST",
          url: endPoint,
          data: dataString,
          cache: false,
          dataType: 'json',
          cache: false,
          success: function(data){
          var scheck = data.check;
          if(scheck==1){
            $('#success-div').html('<div><i class="bi-check"></i></div> Password RESET Complete').fadeIn(500).delay(5000).fadeOut(100);
            var remove_div = document.querySelector(".reset-pass-form");
            remove_div.style.display="none";
            _next_page('next_1');
            setTimeout(function() {
              window.location.reload();
          }, 4000);
          }else{
              $('#not-success-div').html('<div><i class="bi-x-circle"></i></div> INVALID OTP<br /><span>Check the OTP and try again</span>').fadeIn(500).delay(5000).fadeOut(100);
          $('#finish-reset-btn').html(btn_text);
          document.getElementById('finish-reset-btn').disabled=false;
          }
          }
      });
          }else{
          $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Password Error!<br><span>Check your password and try again</span>').fadeIn(500).delay(5000).fadeOut(100);
            }
          }
  }
}

function _password_reset_completed(){
  var div = document.getElementById("reset-complete-div");
  div.style.display="block";
}

function detectInputType() {
  const input = document.getElementById('reset_password_email').value;
  const contactTypeSpan = document.getElementById('contact_type');
  const memberContactSpan = document.getElementById('member_contact');
  if (input.includes('@') && input.includes('.')) {
      contactTypeSpan.innerText = 'mail';
  } else if (/^\d+$/.test(input)) {
      contactTypeSpan.innerText = 'phone number';
  }
  memberContactSpan.innerText = input;
}

function isNumber_Check() {
    var e = window.event;
    var key = e.keyCode && e.which;
    if (!((key >= 48 && key <= 57) || key == 43 || key == 45)) {
      if (e.preventDefault) {
        e.preventDefault();
        $("#loan_info").fadeIn(300);
        document.getElementById("loanamount").style.border =
          "rgb(245, 142, 58) 1px solid";
      } else {
        e.returnValue = false;
      }
    } else {
      $("#loan_info").fadeOut(300);
      document.getElementById("loanamount").style.border =
        "rgba(0, 0, 0, .1) 1px solid";
    }
  }
  
  function isNumber_Check2() {
    var e = window.event;
    var key = e.keyCode && e.which;
    if (!((key >= 48 && key <= 57) || key == 43 || key == 45)) {
      if (e.preventDefault) {
        e.preventDefault();
        $("#duration_info").fadeIn(300);
        document.getElementById("loanduration").style.border =
          "rgb(245, 142, 58) 1px solid";
      } else {
        e.returnValue = false;
      }
    } else {
      $("#duration_info").fadeOut(300);
      document.getElementById("loanduration").style.border =
        "rgba(0, 0, 0, .1) 1px solid";
    }
  }
  
  function isAlphabetic_Check() {
    const key = event.keyCode || event.which;
    const alphabetKeys = Array.from({length: 26}, (_, i) => i + 65).concat(Array.from({length: 26}, (_, i) => i + 97));
    if (!alphabetKeys.includes(key)) {
      event.preventDefault();
      $("#fullname_info").fadeIn(300);
      document.getElementById("fullname").style.border = "rgb(245, 142, 58) 1px solid";
    } else {
      $("#fullname_info").fadeOut(300);
      document.getElementById("fullname").style.border = "rgba(0, 0, 0, .1) 1px solid";
    }
  }









