
function _open_menu(){
    $('.overlay-div').animate({'margin-left':'0'},200);
    $('.slide-side-div').animate({'margin-left':'0'},400);
}

function _close_menu(){
    $('.overlay-div').animate({'margin-left':'-100%'},200);
    $('.slide-side-div').animate({'margin-left':'-250px'},0);

}


function updateDateTime() {
  const dateElement = document.getElementById('currentDateTime');
  const now = new Date();

  const options = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  };

  const dateString = now.toLocaleDateString('en-US', options);
  const timeString = now.toLocaleTimeString('en-US', { hour12: true }); // shows hh:mm:ss AM/PM

  dateElement.textContent = `${dateString} | ${timeString}`;
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



function get_doctor_details(sessionId) {
    var activities = document.querySelector('.activities-div');
    var chat_div = document.querySelector('.chat-div');
    activities.style.display = 'flex';
    chat_div.style.display = 'none';

    $.ajax({
        type: "POST",
        url: endPoint,
        dataType: "json",
        data: {
            action: "get_doctor_real_details",
            doctor_id: sessionId
        },
        cache: false,
        success: function (response) {
            if (response.success) {
                var doctorDetails = response.data;

                // Use the "name" field we created in PHP
                var doctorName = doctorDetails.name; 
                var doctor_passport = doctorDetails.doctor_passport;

                document.getElementById('doctor_id').value = sessionId;

                // Display the doctor's name
                document.getElementById('full_name').value = doctorName;
                document.getElementById('doctor-name').textContent = doctorName;
                document.getElementById('doctor_name').textContent = doctorName;
                document.getElementById('doctorFirstName').textContent = doctorName; // Set only the first name in the span with id
                

                // Handle passport image(s)
                var defaultImg = rootUrl + 'uploaded_files/doctor_profile_pix/doc_default.jpeg';
                var imageUrl;

                if (doctor_passport) {
                    if (doctor_passport.startsWith("http://") || doctor_passport.startsWith("https://")) {
                        imageUrl = doctor_passport;
                    } else {
                        imageUrl = rootUrl + 'uploaded_files/doctor_profile_pix/' + doctor_passport;
                    }
                } else {
                    imageUrl = defaultImg;
                }

                document.getElementById('my_passport').src = imageUrl;
                document.getElementById('my_passport2').src = imageUrl;
                document.getElementById('change-btn').src = imageUrl;

                pop_notification(); // keep if needed
            } else {
                console.log('Failed to fetch doctor details:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching doctor details:', status, error);
        }
    });
}



function logout() {
    sessionStorage.clear();
    alert("Logging OUT...")
    window.location.href = "../";
   

 
}

function fetch_recently_contacted_pat(doctor_id) {
    $.ajax({
        url: endPoint,
        type: "POST",
        data: {  
            action: "fetch_recently_contacted_pat",
            doctor_id: doctor_id 
        },
        dataType: "json",
        success: function(patients) {
            let html = "";

            if (patients.length > 0) {
                patients.forEach(pat => {
                    html += `
                        <li onclick="open_chat('${pat.patient_id}')">
                            <img src="${pat.patient_passport}" alt="Patient">
                            <div class="doctor-info">
                                <p class="name">${pat.patient_fullname}</p>
                                <p class="date">Last contacted: ${pat.last_time_contacted}</p>
                            </div>
                        </li>
                    `;
                });
            } else {
                html = `<li><p> No recent contacts found</p></li>`;
            }

            $(".recent-list").html(html);
        },
        error: function() {
            $(".recent-list").html("<li><p>Error loading recent patients</p></li>");
        }
    });
}


function loadPrescriptions(doctor_id) {
  $.ajax({
    type: "POST",
    url: endPoint,
    data: { action: "fetch_doc_prescriptions", doctor_id: doctor_id },
    dataType: "json",
    success: function(response) {
      const tbody = $("#all-entries-body");
      tbody.empty();

      if (response.success && response.data.length > 0) {
        response.data.forEach(item => {
          tbody.append(`
            <tr>
              <td>${item.prescribed_at}</td>
              <td> ${item.patient_name}</td>
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


function total_appointments(doctor_id) {
    $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: "get_total_appointments", doctor_id: doctor_id },
        dataType: "json",
        cache: false,
        success: function (response) {
            if (response.success) {
                document.getElementById("total_appointments").textContent = response.total_appointments;
            } else {
                document.getElementById("total_appointments").textContent = "0";
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching total appointments:", error);
        }
    });
}

function fetch_total_consultants(doctor_id){
  $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: "get_total_patients", doctor_id: doctor_id },
        dataType: "json",
        cache: false,
        success: function (response) {
            if (response.success) {
                document.getElementById("total_patients").textContent = response.total_patients;
            } else {
                document.getElementById("total_patients").textContent = "0";
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching total appointments:", error);
        }
    });

}

function fetch_all_appointments(doctor_id) {
    $.ajax({
        url: endPoint,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'fetch_all_appointments',
            doctor_id: doctor_id
        },
        success: function(response) {
            const tbody = $('#appointments_body');
            tbody.empty(); // clear table body

            if (response.success && response.data.length > 0) {
                $.each(response.data, function(index, row) {
                    const appointmentDate = row.appointment_date;
             
                    const appointmentTime = row.appointment_time || '-';

                    const tr = `
                        <tr onclick="openAppointmentModal('${row.appointment_id}')">
                            <td>${row.patient_name}</td>
                            <td>${appointmentDate}</td>
                            <td>${appointmentTime}</td>
                        </tr>
                    `;
                    tbody.append(tr);
                });
            } else {
                tbody.html('<tr><td colspan="3">No appointments found</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching appointments:', error);
            $('#appointments_body').html('<tr><td colspan="3">Error loading appointments</td></tr>');
        }
    });
}

function save_appointment() {
    const doctor_id = document.getElementById('doctor_id').value;
    const appointment_date = document.getElementById('appointment_date').value;

    const patientNames = [];
    const appointmentTimes = [];
    const reasons = [];
    const statuses = [];

    // Collect data from all rows dynamically
    document.querySelectorAll('#appointmentFields .appointment-row').forEach(row => {
        const patient = row.querySelector('input[name="patient[]"]').value.trim();
        const time = row.querySelector('input[name="time[]"]').value;
        const reason = row.querySelector('input[name="reason[]"]').value.trim();
        const status = row.querySelector('select[name="status[]"]').value;

        if (patient && time && reason) {
            patientNames.push(patient);
            appointmentTimes.push(time);
            reasons.push(reason);
            statuses.push(status);
        }
    });

    if (patientNames.length === 0) {
        alert('Please enter at least one appointment.');
        return;
    }

    $.ajax({
        url: endPoint,
        type: 'POST',
        data: {
            action: 'save_appointment',
            doctor_id: doctor_id,
            appointment_date: appointment_date,
            patient_name: patientNames,
            appointment_time: appointmentTimes,
            reason: reasons,
            status: statuses
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Appointment(s) saved successfully!');
                fetch_all_appointments(doctor_id);
                fetch_total_consultants(doctor_id);
                total_appointments(doctor_id);
                fetchDoctorAppointments(doctor_id);
            } else {
                alert('Failed to save appointment: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('An error occurred while saving the appointment.');
        }
    });
}


function openAppointmentModal(appointment_id) {
    const modal = $('#viewAppointmentModal');
    modal.show();

    $.ajax({
        url: endPoint,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'fetch_single_appointment',
            appointment_id: appointment_id
        },
        success: function(data) {
            if (data.success) {
                $('#view_patient_name').text(data.data.patient_name);
                $('#view_appointment_date').text(data.data.appointment_date);
                $('#view_appointment_time').text(data.data.appointment_time);
                $('#view_reason').text(data.data.reason);
                $('#view_status').val(data.data.status);
                $('#view_appointment_id').val(appointment_id);
            } else {
                $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + data.message)
                    .fadeIn(500).delay(5000).fadeOut(100);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Error fetching appointment.')
                .fadeIn(500).delay(5000).fadeOut(100);
        }
    });
}

function closeAppointmentModal() {
    $('#viewAppointmentModal').hide();
}

function updateAppointmentStatus() {
    const appointment_id = $('#view_appointment_id').val();
    const status = $('#view_status').val();

    $.ajax({
        url: endPoint,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'update_appointment_status',
            appointment_id: appointment_id,
            status: status
        },
        success: function(data) {
            if (data.success) {
                $('#success-div').html('<div><i class="bi-check"></i></div> ' + data.message)
                    .fadeIn(500).delay(5000).fadeOut(100);
                closeAppointmentModal();
            } else {
                $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + data.message)
                    .fadeIn(500).delay(5000).fadeOut(100);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Error updating status.')
                .fadeIn(500).delay(5000).fadeOut(100);
        }
    });
}


function fetchDoctorAppointments(doctor_id) {
    $.ajax({
        url: endPoint,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'fetch_doctor_appointments',
            doctor_id: doctor_id
        },
        success: function(data) {
            const container = $('#dynamicCards');
            container.empty();

            if (data.success) {
                data.data.forEach(app => {
                    const statusClass =
                        app.status === 'pending' ? 'pending-status' :
                        app.status === 'approved' ? 'approved-status' :
                        app.status === 'completed' ? 'completed-status' : 'cancelled-status';

                    const card = `
                        <div class="appointment-card">
                            <p><strong>Patient:</strong> ${app.patient_name}</p>
                            <p><strong>Date:</strong> ${app.appointment_date}</p>
                            <p><strong>Time:</strong> ${app.appointment_time}</p>
                            <p><strong>Reason:</strong> ${app.reason}</p>
                            <p class="status ${statusClass}"><strong>Status:</strong> ${app.status}</p>
                            <button class="btn view-btn" onclick="openAppointmentModal('${app.appointment_id}')">View</button>
                        </div>
                    `;
                    container.append(card);
                });
            } else {
                container.html('<p class="no-data">No appointments found.</p>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> Error fetching appointments.')
                .fadeIn(500).delay(5000).fadeOut(100);
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



function chat_up2(patient_id) {
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
            patient_id: patient_id
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



function get_doctor_total_profile(doctor_id) {
    // Example fetch from DB or API
    $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: "get_doctor_profile", doctor_id: doctor_id },
        dataType: "json",
        success: function(response) {
            if (response.success) {

        let d = response.data || {};

        // === TOP PROFILE ===
        $("#doctor_profile_passport").attr("src", d.passport || "../uploaded_files/doctor_profile_pix/doc_default.jpeg");
        $("#doctor_name3").text(d.full_name || "N/A");
        $("#doctor_title").text(d.title || "N/A");
        $("#doctor_status").text(d.status || "N/A");

        // === STATS ===
        $("#appointments_count").text(d.total_appointments || 0);
        $("#patients_count").text(d.total_patients || 0);
        $("#experience_years").text(d.years_experience || 0);

        // === PERSONAL INFO ===
        $("#info_first_name").html(`<strong>First Name:</strong> ${d.first_name || "N/A"}`);
        $("#info_last_name").html(`<strong>Last Name:</strong> ${d.last_name || "N/A"}`);
        $("#info_age").html(`<strong>Age:</strong> ${d.age || "N/A"}`);
        $("#info_position").html(`<strong>Position:</strong> ${d.position || "N/A"}`);
        $("#info_email").html(`<strong>Email:</strong> ${d.email || "N/A"}`);
        $("#info_phone").html(`<strong>Phone:</strong> ${d.phone || "N/A"}`);
        $("#info_location").html(`<strong>Location:</strong> ${d.location || "N/A"}`);

        // === SPECIALTIES ===
        $("#specialty_list").empty();
        if (Array.isArray(d.specialties) && d.specialties.length > 0) {
            d.specialties.forEach((sp, i) => {
                $("#specialty_list").append(`<li id="specialty_${i+1}">${sp}</li>`);
            });
        } else {
            $("#specialty_list").append(`<li>No specialty listed</li>`);
        }

        // === NOTIFICATIONS ===
        $("#notifications_list").empty();
        if (Array.isArray(d.notifications) && d.notifications.length > 0) {
            d.notifications.forEach((n, i) => {
                $("#notifications_list").append(`<li id="notif_${i+1}">${n}</li>`);
            });
        } else {
            $("#notifications_list").append(`<li>No notifications</li>`);
        }

        // === SCHEDULE ===
        let s = d.schedule || {};
        $("#schedule_row1 td:nth-child(1)").text(s.weekdays_label || "Mon - Fri");
        $("#schedule_row1 td:nth-child(2)").text(s.weekdays_time || "N/A");

        $("#schedule_row2 td:nth-child(1)").text(s.sat_label || "Sat");
        $("#schedule_row2 td:nth-child(2)").text(s.sat_time || "N/A");

        $("#schedule_row3 td:nth-child(1)").text(s.sun_label || "Sun");
        $("#schedule_row3 td:nth-child(2)").text(s.sun_time || "N/A");

        // === NOTES ===
        $("#patient_notes").empty();
        if (Array.isArray(d.notes) && d.notes.length > 0) {
            d.notes.forEach((note, i) => {
                $("#patient_notes").append(`<blockquote id="note_${i+1}">${note}</blockquote>`);
            });
        } else {
            $("#patient_notes").append(`<blockquote>No notes available</blockquote>`);
        }

        // === EDUCATION ===
        let edu = d.education || {};
        $("#edu_year").html(`<strong>Year:</strong> ${edu.year || "N/A"}`);
        $("#edu_degree").html(`<strong>Degree:</strong> ${edu.degree || "N/A"}`);
        $("#edu_institute").html(`<strong>Institute:</strong> ${edu.institute || "N/A"}`);
        $("#edu_result").html(`<strong>Result:</strong> ${edu.result || "N/A"}`);

        // === EXPERIENCE ===
        let exp = d.experience || {};
        $("#exp_year").html(`<strong>Year:</strong> ${exp.year || "N/A"}`);
        $("#exp_department").html(`<strong>Department:</strong> ${exp.department || "N/A"}`);
        $("#exp_position").html(`<strong>Position:</strong> ${exp.position || "N/A"}`);
        $("#exp_hospital").html(`<strong>Hospital:</strong> ${exp.hospital || "N/A"}`);
        $("#exp_feedback").html(`<strong>Feedback:</strong> ${exp.feedback || "N/A"}`);

        
        }




        },
        error: function() {
            alert("Error loading profile.");
        }
    });
}

function populate_doctor_form() {
doctor_id = document.getElementById("doctor_id").value;
    $.ajax({
        type: "POST",
        url: endPoint,
        data: { action: "get_doctor_profile", doctor_id: doctor_id },
        dataType: "json",
        success: function(response) {
            if (response.success) {

                let d = response.data || {};

                $("#profile_photo").attr("src", d.profile_photo || "../uploaded_files/doctor_profile_pix/doc_default.jpeg");
                $("#full_name").val(d.full_name || "");
                $("#dob").val(d.dob || "");
                $("#email").val(d.email || "");
                $("#phone").val(d.phone || "");
                $("#address").val(d.address || "");
                $("#city").val(d.city || "");
                $("#state").val(d.state || "");
                $("#zip").val(d.zip || "");

                $("#license_number").val(d.license_number || "");
                $("#license_country").val(d.license_country || "");
                $("#license_expiry").val(d.license_expiry || "");
                $("#board_cert").val(d.board_cert || "");
                $("#primary_specialty").val(d.primary_specialty || "");
                $("#sub_specialties").val(d.sub_specialties || "");
                $("#medical_school").val(d.medical_school || "");
                $("#grad_year").val(d.grad_year || "");
                $("#residency").val(d.residency || "");
                $("#experience").val(d.experience || "");

                $("#hospital_affiliations").val(d.hospital_affiliations || "");
                $("#practice_name").val(d.practice_name || "");
                $("#practice_address").val(d.practice_address || "");
                $("#languages").val(d.languages || "");
                $("#expertise").val(d.expertise || "");
                $("#consultation_fee").val(d.consultation_fee || "");

                $("#device").val(d.device || "");
                $("#webcam").val(d.webcam || "");
                $("#microphone").val(d.microphone || "");

                $("#hipaa").prop("checked", d.hipaa == 1);
                $("#telemedicine_rules").prop("checked", d.telemedicine_rules == 1);
                $("#background_check").prop("checked", d.background_check == 1);

                $("#timezone").val(d.timezone || "");
                $("#consultation_hours").val(d.consultation_hours || "");

                let consultTypes = d.consultation_types || [];
                $("#consult_video").prop("checked", consultTypes.includes("Video"));
                $("#consult_phone").prop("checked", consultTypes.includes("Phone"));
                $("#consult_message").prop("checked", consultTypes.includes("Messaging"));

                $("#max_patients").val(d.max_patients || "");
                $("#emergency_contact").val(d.emergency_contact || "");

                $("#bank_name").val(d.bank_name || "");
                $("#account_number").val(d.account_number || "");
                $("#routing_number").val(d.routing_number || "");
                $("#tax_id").val(d.tax_id || "");
                $("#insurance").val(d.insurance || "");

                if (d.id_upload_name) $("#id_upload").attr("data-existing", d.id_upload_name);
                if (d.license_upload_name) $("#license_upload").attr("data-existing", d.license_upload_name);
                if (d.board_upload_name) $("#board_upload").attr("data-existing", d.board_upload_name);

                $("#terms").prop("checked", d.terms == 1);
                $("#privacy").prop("checked", d.privacy == 1);
                $("#signature").val(d.signature || "");
                $("#agreement_date").val(d.agreement_date || "");
            }
        }
    });
}



function save_progress(step) {

    let form = document.getElementById("profile-form");
    let formData = new FormData(form);

    formData.append("action", "save_progress");
    formData.append("doctor_id", doctor_id); // must be defined globally (from session)
    formData.append("step", step);

    $.ajax({
        type: "POST",
        url: endPoint,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {

            // Try parsing JSON
            let res;
            try {
                res = typeof response === "string" ? JSON.parse(response) : response;
            } catch (e) {
                console.log("Invalid JSON response:", response);
                $('#warning-div')
                    .html('<div><i class="bi-exclamation-triangle"></i></div> Unexpected server response')
                    .fadeIn(500).delay(5000).fadeOut(100);
                return;
            }

            if (res.success) {
                $('#success-div')
                    .html('<div><i class="bi-check"></i></div> Progress saved successfully')
                    .fadeIn(500).delay(5000).fadeOut(100);
            } else {
                $('#warning-div')
                    .html('<div><i class="bi-exclamation-triangle"></i></div> ' + res.message)
                    .fadeIn(500).delay(5000).fadeOut(100);
            }
        },

        error: function(xhr, status, error) {
            $('#warning-div')
                .html('<div><i class="bi-exclamation-triangle"></i></div> Network error. Try again!')
                .fadeIn(500).delay(5000).fadeOut(100);

            console.log("AJAX Error:", error);
        }
    });
}





function open_chat(doctor_id) {
    var activities = document.querySelector('.activities-div');
    var chat_div = document.querySelector('.chat-div');
    activities.style.display = 'none';
    chat_div.style.display = 'flex';

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
















    