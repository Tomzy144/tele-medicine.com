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


function _sign_in(){ 
    var member_id = $('#email_address').val();
    var password = $('#password').val();
    if((member_id!='')&&(password!='')){
        user_login(member_id,password);
    }else{
        $('#warning-div').fadeIn(500).delay(5000).fadeOut(100);
    }
};

function user_login(member_id, password) {
  var action = 'login_api';
  var btn_text = $('#login_btn').html();
  $('#login_btn').html('Authenticating...');
  document.getElementById('login_btn').disabled = true;
  var dataString = {
    action: action,
    member_id: member_id,
    password: password
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
        sessionStorage.setItem('role_id', response.role_id);
        setTimeout(function() {
          window.location.href = response.redirect_url;
        }, 2000);
      } else {
        $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + response.message)
          .fadeIn(500).delay(5000).fadeOut(100);
      }
      $('#login_btn').html(btn_text);
      document.getElementById('login_btn').disabled = false;
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error:', status, error);
      $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.')
        .fadeIn(500).delay(5000).fadeOut(100);
      $('#login_btn').html(btn_text);
      document.getElementById('login_btn').disabled = false;
    }
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









