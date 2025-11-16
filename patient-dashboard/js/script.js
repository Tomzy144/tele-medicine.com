
function _open_menu(){
    $('.overlay-div').animate({'margin-left':'0'},200);
    $('.slide-side-div').animate({'margin-left':'0'},400);
}

function _close_menu(){
    $('.overlay-div').animate({'margin-left':'-100%'},200);
    $('.slide-side-div').animate({'margin-left':'-250px'},0);

}



function _get_page(page){
    $('#more-info').html('<div class="ajax-loader"><img src="all-images/images/ajax-loader.gif"/></div>').fadeIn('fast');
    action='get_page';
    var dataString ='action='+ action+'&page='+ page;
    $.ajax({
    type: "POST",
    url: "config/code.php",
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




function _expand_link(ids){
    //  $('#'+divid).toggle(500);
      $('#'+ids+'-li').toggle('slow');
}
 
   
function _expand_div(ids){
//  $('#'+divid).toggle(500);
    $('#'+ids+'-lis').toggle('slow');
}
 




function alert_close(){
    $('.overlay-div-in').html('').fadeOut(200);
}



$(function() {
    view = {
       UpdatePreview: function(obj){
          if (!window.FileReader){
             // do nothing
          }else{
          var reader= new FileReader();
          var target= null;
          reader.onload = function(e){
             target = e.target || e.srcElement;
          $('#my_passport').prop("src", target.result);
          };
                reader.readAsDataURL(obj.files[0]);
       }
    }
    };
});


$(function() {
    staff = {
       UpdatePreview: function(obj){
          if (!window.FileReader){
             // do nothing
          }else{
          var reader= new FileReader();
          var target= null;
          reader.onload = function(e){
             target = e.target || e.srcElement;
          $('#passport_staff').prop("src", target.result);
          };
                reader.readAsDataURL(obj.files[0]);
       }
    }
    };
});


$(function(){
    Test = {
        UpdatePreview: function(obj){
          // if IE < 10 doesn't support FileReader
          if(!window.FileReader){
             // don't know how to proceed to assign src to image tag
          } else {
			  _upload_profile_pix();
             var reader = new FileReader();
             var target = null;

             reader.onload = function(e) {
              target =  e.target || e.srcElement;
               $("#passport1,#passport2,#passport3").prop("src", target.result);
             };
              reader.readAsDataURL(obj.files[0]);
          }
        }
    };
});




    function _update_user_profile(staff_id){
        var fullname = $('#fullname').val();
        var email = $('#email').val();
        var phonenumber = $('#phonenumber').val();  
       // var role_id = $('#role_id').val();
        var status_id = $('#status_id').val();
        if((fullname=='')||(email=='')||(phonenumber=='')||(status_id=='')){
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> USER ERROR!<br /><span>Fill the neccessary Fields  to continue</span>').fadeIn(500).delay(5000).fadeOut(100);
                }else{
                

            $('#update-user-btn').html('Updating...');
            document.getElementById('update-user-btn').disabled=true;

        var action ='update_user_profile';
        var dataString ='action='+ action+'&staff_id='+ staff_id+'&fullname='+ fullname+'&email='+ email+'&phonenumber='+ phonenumber+'&status_id='+ status_id+'&passport='+ passport;
         $.ajax({
            type: "POST",
            url: "config/code.php",
            data: dataString,
            cache: false, 
            success: function(){
              $('#success-div').html('<div><i class="bi-check"></i></div> PROFILE UPDATED SUCCESSFULLY').fadeIn(500).delay(5000).fadeOut(100);
              _user_profile('dashboard',staff_id);
            
            $('#update-user-btn').html('<i class="bi-check2"></i> SUBMIT');
            document.getElementById('update-user-btn').disabled=false;
        }
    });
}
}	


function _upload_profile_pix(){
		var action = 'update_profile_pix';
        var file_data = $('#passport').prop('files')[0];
		if (file_data==''){}else{ 
        var form_data = new FormData();                  
        form_data.append('passport', file_data);
        form_data.append('action', action);
        $.ajax({
            url: "config/code.php",
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData:false,
            success: function(html){
		    $('#success-div').html('<div><i class="bi-check"></i></div> PROFILE PICTURE UPDATED SUCCESSFULLY').fadeIn(500).delay(5000).fadeOut(100);
            $('#passport').val('');
			}
        });
		}
}



function get_patient_details(sessionId) {
      var activities = document.querySelector('.activities-div');
    var chat_div = document.querySelector('.chat-div');
    activities.style.display = 'flex';
    chat_div.style.display = 'none';


    $.ajax({
        type: "POST",
        url: endPoint,
        dataType: "json",
        data: {
            action: "get_patient_details",
            patient_id: sessionId
        },
        cache: false,
        success: function (response) {
            if (response.success) {
                var patientDetails = response.data;

                // Only full name and passport are needed
                var patientName = patientDetails.patient_name; 
                var patient_passport = patientDetails.patient_passport;
                document.getElementById('patient_id').value = sessionId;

                // Display the patient's name
                document.getElementById('full_name').value =  patientName;
                document.getElementById('patient-name').textContent = patientName;
                document.getElementById('patient_name').textContent = patientName;
                  

                // Handle passport image(s)
                 var defaultImg = rootUrl + 'uploaded_files/patient_profile_pix/11.png';

                var imageUrl;
                if (patient_passport) {
                    // If it's already a full URL, use it directly
                    if (patient_passport.startsWith("http://") || patient_passport.startsWith("https://")) {
                        imageUrl = patient_passport;
                    } else {
                        // Otherwise assume it's just a filename
                        imageUrl = rootUrl+ 'uploaded_files/patient_profile_pix/' + patient_passport;
                    }
                } else {
                    imageUrl = defaultImg;
                }

                document.getElementById('my_passport').src = imageUrl;
                document.getElementById('my_passport2').src = imageUrl;
                document.getElementById('change-btn').src = imageUrl;



                pop_notification(); // keep if needed
            } else {
                console.log('Failed to fetch patient details:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching patient details:', status, error);
        }
    });
}


function logout() {
    sessionStorage.clear();
    alert("Logging OUT...")
    window.location.href = "../";
   

 
}
function fetch_recently_contacted_doc(patient_id) {
    $.ajax({
        url: endPoint,
        type: "POST",
        data: {  
            action: "fetch_recently_contacted_doc",
            patient_id: patient_id 
        },
        dataType: "json",
        success: function(doctors) {
            let html = "";

            if (doctors.length > 0) {
                doctors.forEach(doc => {
                    html += `
                        <li onclick="open_chat('${doc.doctor_id}')">
                            <img src="${doc.doctor_passport}" alt="Doctor">
                            <div class="doctor-info">
                                <p class="name">${doc.doctor_fullname}</p>
                                <p class="specialty">${doc.speciality}</p>
                                <p class="date">Last contacted: ${doc.last_time_contacted}</p>
                            </div>
                        </li>
                    `;
                });
            } else {
                html = `<li><p>No recent contacts found</p></li>`;
            }

            $(".recent-list").html(html);
        },
        error: function() {
            $(".recent-list").html("<li><p>Error loading recent doctors</p></li>");
        }
    });
}


function loadPrescriptions(patient_id) {
  $.ajax({
    type: "POST",
    url: endPoint,
    data: { action: "fetch_prescriptions", patient_id: patient_id },
    dataType: "json",
    success: function(response) {
      const tbody = $("#all-entries-body");
      tbody.empty();

      if (response.success && response.data.length > 0) {
        response.data.forEach(item => {
          tbody.append(`
            <tr>
              <td>${item.prescribed_at}</td>
              <td>Dr. ${item.doctor_name}</td>
              <td>${item.prescription}</td>
            </tr>
          `);
        });
      } else {
        tbody.append(`<tr><td colspan="3">No prescriptions found</td></tr>`);
      }
    },
    error: function(xhr, status, error) {
      console.error("Error fetching prescriptions:", error);
    }
  });
}


function fetch_all_doctors() {
    $.ajax({
        type: "POST",
        url: endPoint,
        dataType: "json",
        data: { action: "fetch_all_doctors" },
        cache: false,
        success: function (response) {
            var container = document.getElementById("doctors_list");
            container.innerHTML = ""; // clear before adding

            if (response.success) {
                response.data.forEach(function (doctor) {
                    var doctorImg = doctor.doctor_passport
                        ? "../uploaded_files/doctor_profile_pix/" + doctor.doctor_passport
                        : "../uploaded_files/doctor_profile_pix/doc_default.jpeg";

                    var speciality = doctor.speciality 
                        || doctor.other_speciality 
                        || doctor.sub_speciality 
                        || "General Practitioner";

                    var card = document.createElement("div");
                    card.className = "doctor-card";
                    card.innerHTML = `
                        <img src="${doctorImg}" alt="Doctor">
                        <div class="doctor-info">
                            <h4>Dr. ${doctor.doctor_name}</h4>
                            <p class="role">${speciality}</p>
                            <p class="desc">With ${doctor.years_experience}+ years of experience.</p>
                            <div class="rating">★★★★☆</div>
                            <div class="actions">
                                <button class="view-btn" onclick="view_doctor_profile('${doctor.doctor_id}');" data-id="${doctor.doctor_id}">View Profile</button>
                                <button class="chat-btn" onclick="chat_up2('${doctor.doctor_id}');" data-id="${doctor.doctor_id}">Chat Up</button>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = `<p>No doctors found.</p>`;
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching doctors:", error);
        }
    });
}

function chat_up2(doctor_id) {
    _next_page('next_1'), highlite2('first');

    var activities = document.querySelector('.activities-div');
    var chat_div = document.querySelector('.chat-div');
    activities.style.display = 'none';
    chat_div.style.display = 'flex';

    // AJAX call to fetch doctor details
    $.ajax({
        type: "POST",
        url: endPoint,
        dataType: "json",
        data: {
            action: "get_doctor_details",
            doctor_id: doctor_id
        },
        cache: false,
        success: function (response) {
            if (response.success) {
                var doctor = response.data;
                // update the chat header
                document.querySelector(".chat-user strong").textContent = "Dr. " + doctor.firstname + " " + doctor.lastname;
                document.querySelector(".chat-user .status").textContent = doctor.online_status == 1 ? "Online" : "Offline";
                document.getElementById('doctor_id').value = doctor_id;
                refreshChat();
            } else {
                console.log("Doctor not found");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching doctor:", error);
        }
    });
}


function view_doctor_profile(doctor_id) {
    // Example fetch from DB or API
    $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: "get_doctor_profile", doctor_id: doctor_id },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                let d = response.data;

                document.getElementById("doctorName").textContent = "Dr. " + d.firstname + " " + d.lastname;
                document.getElementById("doctorSpeciality").textContent = d.speciality || "N/A";
                document.getElementById("doctorExperience").textContent = d.years_experience + " years";
                document.getElementById("doctorLicense").textContent = d.medical_license;
                document.getElementById("doctorCountry").textContent = d.country;

                let img = d.doctor_passport ? "../uploaded_files/doctor_profile_pix/" + d.doctor_passport : "../uploaded_files/doctor_profile_pix/doc_default.jpeg";
                document.getElementById("doctorImage").src = img;

                document.getElementById("doctorProfileModal").style.display = "block";
            } else {
                alert("Doctor profile not found.");
            }
        },
        error: function() {
            alert("Error loading profile.");
        }
    });
}

function closeDoctorModal() {
    document.getElementById("doctorProfileModal").style.display = "none";
}




function show_account_setting() {
    var option_divs = document.querySelectorAll('.option-div');
    var account_setting_div = document.getElementById('account-setting-div'); 
    var add_on = document.getElementById('add-on'); 

    // Loop through option divs
    option_divs.forEach(function(option_div) {
        if (option_div.style.display === "inline-block") {
            account_setting_div.style.display = "none";  // Hide account setting div
        } else {
            option_div.style.display = "none";  // Hide option div
            account_setting_div.style.display = "block";  // Show account setting div
        }
    });

    // Append " > Account Setting" to the heading
    add_on.innerHTML = add_on.innerHTML + " > Account Setting";
}


function highlite2(id) {
    // Remove the active class from all <p> elements within the list
    document.querySelectorAll("header .logo-div .list-div ul li p").forEach(item => {
        item.classList.remove("active");
    });

    // Add the active class to the clicked element by ID
    const activeElement = document.querySelector(`#${id}`);
    if (activeElement) {
        activeElement.classList.add("active");
    } else {
        console.error(`Element with id "${id}" not found`);
    }
}

  


function restore_div() {
    var search_div = document.getElementById('search_box');
    var search_Rdiv = document.getElementById('setting_id');
    

    search_div.style.display = "none";
    search_Rdiv.style.display = "block";


}

function remove_heading(){
    var search_div = document.getElementById('search_box');
    var search_Rdiv = document.getElementById('setting_id');
    

    search_div.style.display = "block";
    search_Rdiv.style.display = "none";
}



function show_system_setting() {
    var option_divs = document.querySelectorAll('.option-div');
    var system_setting_div = document.getElementById('system-setting-div'); 
    var add_on = document.getElementById('add-on'); 

    // Loop through option divs
    option_divs.forEach(function(option_div) {
        if (option_div.style.display === "inline-block") {
            system_setting_div.style.display = "none";  // Hide account setting div
        } else {
            option_div.style.display = "none";  // Hide option div
            system_setting_div.style.display = "block";  // Show account setting div
        }
    });

    // Append " > Account Setting" to the heading
    add_on.innerHTML = add_on.innerHTML + " > System Setting";
}


function open_chat(doctor_id) {
    document.querySelector('.activities-div').style.display = 'none';
    document.querySelector('.chat-div').style.display = 'flex';

    $.ajax({
        type: "POST",
        url: endPoint,
        dataType: "json",
        data: {
            action: "get_doctor_details",
            doctor_id: doctor_id
        },
        cache: false,
        success: function (response) {
            if (response.success) {
                let doctor = response.data;

                document.getElementById("chatUserPicture").src = doctor.doctor_passport;
                document.querySelector(".chat-user strong").textContent =
                    "Dr. " + doctor.firstname + " " + doctor.lastname;

                document.querySelector(".chat-user .status").textContent =
                    doctor.online_status == 1 ? "Online" : "Offline";

                document.getElementById('doctor_id').value = doctor_id;

                refreshChat();
            } else {
                console.log(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching doctor:", error);
        }
    });
}




//////////////////////////////////////

// Upload image via AJAX
function uploadImageToServer(file) {
    var formData = new FormData();
    var patient_id = document.getElementById('patient_id').value;

    formData.append('patient_id', patient_id);
    formData.append('passport', file);

    $.ajax({
        url: '../config/upload_passport.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            try {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#success-div').html('<div><i class="bi-check"></i></div> ' + data.message)
                        .fadeIn(500).delay(5000).fadeOut(100);
                } else {
                    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + data.message)
                        .fadeIn(500).delay(5000).fadeOut(100);
                }
            } catch (e) {
                console.error('Invalid response:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Image upload failed:', status, error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Image upload failed. Please try again.')
                .fadeIn(500).delay(5000).fadeOut(100);
        }
    });
}

// // Remove/reset image to default
// function removeImage() {
//     var defaultUrl = document.getElementById('my_passport').getAttribute('data-default');

//     document.getElementById('my_passport').src = defaultUrl;
//     document.getElementById('my_passport2').src = defaultUrl;

//     var remove_btn = document.getElementById('remove_btn');
//     remove_btn.disabled = true;
//     remove_btn.style.cursor = "not-allowed";

//     document.getElementById('file-input').value = '';
// }
// //////////////////////////////////////


function update_profile() {
    var patient_id = $('#patient_id').val();
    var full_name = $('#full_name').val().trim();

    if (!full_name) {
          $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Name cannot be empty.')
                .fadeIn(500).delay(5000).fadeOut(100);
        return;
    }

    $.ajax({
        url: endPoint, // your backend endpoint
        type: 'POST',
        data: {
            action: "update_patient_profile",
            patient_id: patient_id,
            full_name: full_name
        },
        success: function(response) {
            try {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#success-div').html('<div><i class="bi-check"></i></div> ' + data.message)
                        .fadeIn(500).delay(3000).fadeOut(100);
                } else {
                    $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + data.message)
                        .fadeIn(500).delay(5000).fadeOut(100);
                }
            } catch (e) {
                console.error("Invalid response:", response);
            }
        },
        error: function(xhr, status, error) {
            console.error("Profile update failed:", status, error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Profile update failed.')
                .fadeIn(500).delay(5000).fadeOut(100);
        }
    });
}







function icon_toggle(){
    const toggle = document.getElementById('togglePassword');
    const toggle2 = document.getElementById('togglePassword2');
    const password = document.getElementById('new_password');
    const cpassword = document.getElementById('confirm_new_password');

    if(password.type === "password"){
        password.type = 'text';
        cpassword.type = 'text';
        toggle.style.display="none";
        toggle2.style.display="block";
    } else {
        password.type = 'password';
        cpassword.type = 'password';
        toggle.style.display="block";
        toggle2.style.display="none";
    }
}

function checkPasswordStrength() {
    const newPassword = document.getElementById('new_password');
    const pswdInfo = document.querySelector('.pswd_info3');
    const strengthBar = document.querySelector('.strength-bar2');
    const strengthText = document.querySelector('.strength-text2');
    const rulesText = document.querySelector('.strength-requirements2');

    const val = newPassword.value;
    pswdInfo.style.display = val.length ? 'block' : 'none';

    let strength = 0;
    if (val.length >= 8) strength += 1;
    if (/[A-Z]/.test(val)) strength += 1;
    if (/[a-z]/.test(val)) strength += 1;
    if (/[0-9]/.test(val)) strength += 1;
    if (/[\W_]/.test(val)) strength += 1;

    const percent = (strength / 5) * 100;
    strengthBar.style.width = percent + '%';

    if (percent <= 40) {
        strengthBar.style.background = 'red';
        strengthText.textContent = 'Password strength: Weak';
        rulesText.style.display = 'block';
    } else if (percent <= 80) {
        strengthBar.style.background = 'orange';
        strengthText.textContent = 'Password strength: Medium';
        rulesText.style.display = 'block';
    } else {
        strengthBar.style.background = 'green';
        strengthText.textContent = 'Password strength: Strong';
        rulesText.style.display = 'none'; // hide rules when strong
    }

    checkPasswordMatch(); // also check confirm password
}

// Confirm password match function
function checkPasswordMatch() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_new_password').value;
    const matchMessage = document.getElementById('matchMessage');

    if(confirmPassword.length === 0 || newPassword === confirmPassword) {
        matchMessage.style.display = 'none'; // hide when matched
    } else {
        matchMessage.style.display = 'block';
        matchMessage.style.color = 'red';
        matchMessage.textContent = 'Passwords do not match';
    }
}




function removeImage() {
    // Reset the image to the default placeholder
    document.getElementById('my_passport').src = "<?php echo $website_url; ?>/uploaded_files/profile_pix/1.jpg";
    document.getElementById('my_passport2').src = "<?php echo $website_url; ?>/uploaded_files/profile_pix/1.jpg";

    // Disable the remove button again after resetting the image
    var remove_btn = document.getElementById('remove_btn');
    remove_btn.disabled = true;
    remove_btn.style.cursor = "not-allowed";
    
    // Optionally, you can clear the file input value if needed
    document.getElementById('file-input').value = '';
}

  

  
function update_password() {
    var old_password = $('#old_password').val();
    var new_password = $('#new_password').val();
    var confirm_new_password = $('#confirm_new_password').val();
    var action = "update_password";
    var patient_id = $('#patient_id').val();

    // Check if old password is empty
    if (old_password == "") {
        $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + "Old password cannot be empty").fadeIn(500).delay(5000).fadeOut(100);
        return;
    }

    // Check if new password matches the confirmation
    if (new_password !== confirm_new_password) {
        $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + "New password and confirm password do not match").fadeIn(500).delay(5000).fadeOut(100);
        return;
    }

    // Prepare data for AJAX request
    var dataString = {
        action: action,
        old_password: old_password,
        patient_id: patient_id,
        new_password: new_password
    };

    // Send AJAX request
    $.ajax({
        type: "POST",
        url: endPoint, 
        data: dataString,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                $('#success-div').html('<div><i class="bi-check"></i></div> Password updated successfully!').fadeIn(500).delay(5000).fadeOut(100);
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            } else {
                $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + response.message1 + ' ' + response.message2).fadeIn(500).delay(5000).fadeOut(100);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', status, error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.').fadeIn(500).delay(5000).fadeOut(100);
        }
    });
}

function entry_search(){
   
    const input = document.getElementById('search_input').value.toLowerCase();
    const table = document.getElementById('table');
    const rows = table.getElementsByTagName('tr');

    // Loop through all rows except the first two (header rows)
    for (let i = 2; i < rows.length; i++) {
        const dateCell = rows[i].getElementsByTagName('td')[0]; // Date is in the first column (index 0)
        if (dateCell) {
            const dateValue = dateCell.textContent || dateCell.innerText;

            // Check if the date matches the input
            if (dateValue.toLowerCase().includes(input)) {
                rows[i].style.display = ''; // Show the row if it matches
            } else {
                rows[i].style.display = 'none'; // Hide the row if it doesn't match
            }
        }
    }
    
    
}
function pop_notification() {
    var phone_number = $('#phone_number').val();
    var address = $('#address').val();
    var gender = $('#gender').val();
    var occupation = $('#occupation').val();
    var date_of_birth = $('#date_of_birth').val();
    var marital_status = $('#marital_status').val();
    var member_email = $('#member_email').val();
    var nin = $('#file_name_display').val(); // Assuming this displays the selected file name
    var passport = $('#my_passport').attr('src'); // Get the src attribute of the image

    var errorMessages = []; // Array to collect all errors

    // Validate each field and push corresponding error messages
    if (phone_number == "") {
        errorMessages.push("Update your Phone Number");
    }
    if (address == "") {
        errorMessages.push("Update your Address");
    }
    if (gender == "") {
        errorMessages.push("Update your Gender");
    }
    if (occupation == "") {
        errorMessages.push("Update your Occupation");
    }
    if (date_of_birth == "") {
        errorMessages.push("Update your Date of Birth");
    }
    if (marital_status == "") {
        errorMessages.push("Update your Marital Status");
    }
    if (member_email == "") {
        errorMessages.push("Update your Email");
    }
    if (nin == "") {
        errorMessages.push("Upload your NIN file");
    }
    if (passport == "../uploaded_files/profile_pix/1.jpg") {
        errorMessages.push("Upload your Passport file");
    }

        // If there are any error messages, display them all in the alert
        if (errorMessages.length > 0) {
            showAlert(errorMessages.join('<br>')); // Join the messages with a line break
            
            // Update the notification count dynamically based on the number of error messages
            updateNotificationCount(errorMessages.length); 
        } else {
           
        }

}

// Function to display the custom alert
function showAlert(message) {
    document.getElementById('alertMessage').innerHTML = message; // Use innerHTML to allow <br> tags
    document.getElementById('customAlert').style.display = 'block';
}

// Function to close the alert
function closeAlert() {
    document.getElementById('customAlert').style.display = 'none';
}

// Function to update the notification count
function updateNotificationCount(count) {
    var notificationBadge = document.getElementById('notification-count');

    // Update the number in the badge
    notificationBadge.innerText = count;

    // Show the badge only if there are notifications
    if (count > 0) {
        notificationBadge.classList.add('show');
    } else {
        notificationBadge.classList.remove('show');
    }
}


function toggleSearchInput() {
    var dashboardHeading = document.getElementById('dashboard_heading');
    var searchInput = document.getElementById('search_input2');

    // Toggle visibility between the heading and the search input
    if (dashboardHeading.style.display === 'none') {
        dashboardHeading.style.display = 'inline-block'; // Show the heading
        searchInput.style.display = 'none'; // Hide the input field
    } else {
        dashboardHeading.style.display = 'none'; // Hide the heading
        searchInput.style.display = 'inline-block'; // Show the input field
        searchInput.focus(); // Automatically focus the input when it is displayed
    }
}



function entry_search2(){
   
    const input = document.getElementById('search_input2').value.toLowerCase();
    const table = document.getElementById('table');
    const rows = table.getElementsByTagName('tr');

    // Loop through all rows except the first two (header rows)
    for (let i = 2; i < rows.length; i++) {
        const dateCell = rows[i].getElementsByTagName('td')[0]; // Date is in the first column (index 0)
        if (dateCell) {
            const dateValue = dateCell.textContent || dateCell.innerText;

            // Check if the date matches the input
            if (dateValue.toLowerCase().includes(input)) {
                rows[i].style.display = ''; // Show the row if it matches
            } else {
                rows[i].style.display = 'none'; // Hide the row if it doesn't match
            }
        }
    }
    
    
}
















    