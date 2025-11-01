
<?php if ($page=='dashboard'){?>
<?php    
  
  $doctor_id=$_POST['doctor_id'];


   
?>

    <!-- ////index  -->
    <div class="fill-form-div login-div"  id="next_1"> 
        <?php include "index-phone-div.php"?>
        <header class="fadeInDown animated">
        </header>

        <section class="dashboard-session">
            <div class="inner-div">
                 <!-- 100% wide Top Row -->
                <div class="top-rows">
                    <div class="appointment-cards">
                        
                        <!-- Left (fixed) -->
                        <div class="appointment-card left-card">
                            <h3>Welcome back, <span id="doctorFirstName">xxxxxx</span></h3>
                            <p id="currentDateTime"></p>
                        </div>

                        <!-- Dynamic cards (JS appends here) -->
                        <div id="dynamicCards" class="dynamic-cards"></div>

                        <!-- Right (fixed illustration) -->
                        <div class="appointment-card right-card">
                        <img src="illustration.png" alt="End of appointments" class="end-image">
                        <p>All appointments completed!</p>
                        </div>

                    </div>
                </div>

                <div class="main-content">
                    <div class="left-column">
                            <div class="profile-info-div">
                                <div class="heading-div">
                                <h3>Doctor Info</h3>
                            </div>
                            <div class="inner-div-inner">
                                <div class="profile-card">
                                    <div class="img-div">
                                    <?php if ($passport==''){?>
                                        <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" 
                                            id="my_passport2" alt="profile picture"/>
                                    <?php } else { ?>
                                        <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/<?php echo $passport; ?>" 
                                            id="my_passport" alt="profile picture"/>
                                    <?php } ?>
                                    </div>
                                    <div class="text-div">
                                    <h3 id="doctor-name">xxxxxxx</h3>
                                 
                                       
                                    </div>
                                </div>
                                <div class="achievements-div">
                                    <p>Appointments: <span id="total_appointments">xxx</span></p>
                                    <p>Total Consualts:  <span id="total_patients"> xxx</span></p>
                                </div>
                            </div>
                        </div>


                        <div class="recent-info-div">
                            <div class="inner-div">
                                <div class="heading-div">
                                    <h3>Recently Contacted Patients</h3>
                                </div>

                            <div class="recent-scroll">
                                    <ul class="recent-list">
                                        <!-- Populated by JS -->
                                    </ul>
                                </div>

                                
                            </div>
                        
                        </div>
                    </div>

                    <!-- Big right column -->
                    <div class="activities-div">
                        <div class="right-inner-div">
                            <div class="heading-div">
                                <h3>Activities Log</h3>
                                <hr>
                            </div>
                            <div class="table-div">
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Prescription</th>
                                    </tr>
                                    </thead>
                                    <tbody id="all-entries-body">
                                    <!-- Rows will be inserted here dynamically -->
                                    </tbody>
                                </table>
                            </div>

                        
                        </div>
                    </div>


                    <div class="chat-div">
                        <div class="chat-header">
                            <div class="chat-user">
                                <strong id="chatDoctorName">Dr. John Doe</strong><br>
                                <span class="status" id="doctorStatus">Offline</span>
                            </div>
                            <div class="chat-actions">
                                <button class="icon-btn" title="Video Call">
                                    <i class="bi bi-camera-video"></i>
                                </button>
                            </div>
                        </div>

                        <div class="chat-messages" id="chatMessages">
                            <!-- Messages will load dynamically -->
                        </div>
                
                        <div class="chat-input">
                            <textarea id="chatInput" placeholder="Type a message..."></textarea>
                                <input class="input" type="hidden" value="" id="patient_id"  /><br><br>
                                <input class="input" type="hidden" value="" id="doctor_id"  />
                            <button onclick="send_chat();" id="sendBtn">Send</button>
                            
                        </div>
                    </div>
                </div>
                <div class="bottom-contents">
                    <div class="calender">
                        <div class="inner-div">
                            <div class="calender-heading">
                                <button id="prevMonth">&#10094;</button>
                                <h3 id="calendar-header">Nearest Treatment - Month Year</h3>
                                <button id="nextMonth">&#10095;</button>
                            </div>

                            <div class="calendar-days" id="calendar-days"></div>
                            <div class="calendar-dates" id="calendar-dates"></div>
                        </div>
                    </div>

                   <div class="last-appointments-list">
                    <div class="inner-div">
                        <h3 class="appointments-heading">New Appointments</h3>
                          <table class="appointments-table" id="appointments_table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Timing</th>
                                    </tr>
                                </thead>
                                <tbody id="appointments_body">
                                    <tr><td colspan="3">Loading...</td></tr>
                                </tbody>
                            </table>

                        </div>
                      

                    </div>

                </div>


               <!-- Appointment Modal -->
                <div id="appointmentModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h3 id="modal-date-title">Set Appointment for </h3>
                    <input id="appointment_date" type="hidden" value="" />

                    <form id="appointmentForm">
                    <div id="appointmentFields">
                        <div class="appointment-row">
                        <input type="text" id="appointment_patient_name" name="patient[]" placeholder="Patient Name" required>
                        <input type="time" id="appointment_patient_time" name="time[]" required>
                        <input type="text" id="appointment_reason" name="reason[]" placeholder="Reason for Visit" required>
                        <select id="appointment_status" name="status[]" required>
                            <option value="pending" selected>Pending</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        </div>
                    </div>

                    <button type="button" id="addMoreAppointment" class="btn">+ Add More</button>
                    <button type="button" onclick="save_appointment();" class="btn save-btn">Save Appointment</button>
                    </form>
                </div>
                </div>


                <!-- Single Appointment Modal -->
                <div id="viewAppointmentModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeAppointmentModal()">&times;</span>
                    <h3>Appointment Details</h3>

                    <div id="appointmentDetails">
                    <p><strong>Patient Name:</strong> <span id="view_patient_name"></span></p>
                    <p><strong>Appointment Date:</strong> <span id="view_appointment_date"></span></p>
                    <p><strong>Appointment Time:</strong> <span id="view_appointment_time"></span></p>
                    <p><strong>Reason for Visit:</strong> <span id="view_reason"></span></p>

                    <label for="view_status"><strong>Status:</strong></label>
                    <select id="view_status">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    </div>
                     <input type="hidden" id="view_appointment_id">

                    <div class="modal-actions">
                    <button type="button" class="btn save-btn" onclick="updateAppointmentStatus()">Update Status</button>
                    </div>
                </div>
                </div>













        </section>
    </div>  

    <div class="fill-form-div login-div"  id="next_2"> 
       <section class="doctor-profile-section">
            <div class="inner-div">

                <!-- Profile Overview -->
                <div class="profile-overview" id="profile_overview">

                    <div class="profile-card2" id="profile_card2">

                        <div class="profile-top" id="profile_top">
                            <div class="profile-image" id="profile_image_div">
                                <img src="../uploaded_files/doctor_profile_pix/doc_default.jpeg" 
                                    id="doctor_profile_passport" alt="Doctor Photo">
                            </div>

                            <div class="profile-info" id="profile_info">
                                <h3 class="doctor-name" id="doctor_name3">Dr. John Doe</h3>
                                <p class="doctor-title" id="doctor_title">Consultant Cardiologist</p>
                                <div class="badge" id="doctor_status">Active</div>
                            </div>
                        </div>

                        <div class="profile-stats" id="profile_stats">
                            <div class="stat-box" id="appointments_box">
                                <h4 id="appointments_count">120</h4>
                                <p>Appointments</p>
                            </div>
                            <div class="stat-box" id="patients_box">
                                <h4 id="patients_count">95</h4>
                                <p>Patients</p>
                            </div>
                            <div class="stat-box" id="experience_box">
                                <h4 id="experience_years">12</h4>
                                <p>Years Exp.</p>
                            </div>
                        </div>

                    </div>

                    <div class="info-card" id="info_card">
                        <div class="card-header" id="personal_info_header">
                            <h4><i class="fa-solid fa-user"></i> Personal Information</h4>
                        </div>

                        <div class="info-grid" id="personal_info_grid">
                            <div id="info_first_name"><strong>First Name:</strong> John</div>
                            <div id="info_last_name"><strong>Last Name:</strong> Doe</div>
                            <div id="info_age"><strong>Age:</strong> 38</div>
                            <div id="info_position"><strong>Position:</strong> Consultant Cardiologist</div>
                            <div id="info_email"><strong>Email:</strong> johndoe@gmail.com</div>
                            <div id="info_phone"><strong>Phone:</strong> +234 801 234 5678</div>
                            <div id="info_location"><strong>Location:</strong> Lagos, Nigeria</div>
                        </div>
                    </div>

                </div>

                <!-- Specialty | Notifications | Schedule -->
                <div class="triple-card-row" id="first_triple_row">

                    <!-- Specialty -->
                    <div class="card-box" id="specialty_card">
                        <div class="card-header" id="specialty_header">
                            <h4><i class="fa-solid fa-stethoscope"></i> Specialty</h4>
                        </div>

                        <ul class="list-dashed" id="specialty_list">
                            <li id="specialty_1">Heart Disease</li>
                            <li id="specialty_2">Hypertension</li>
                            <li id="specialty_3">Cardiac Surgery</li>
                            <li id="specialty_4">Preventive Cardiology</li>
                        </ul>
                    </div>

                    <!-- Notifications -->
                    <div class="card-box" id="notifications_card">
                        <div class="card-header" id="notifications_header">
                            <h4><i class="fa-solid fa-bell"></i> Notifications</h4>
                        </div>

                        <ul class="list-striped" id="notifications_list">
                            <li id="notif_1">New message from patient</li>
                            <li id="notif_2">Appointment reminder</li>
                            <li id="notif_3">Lab results ready for review</li>
                        </ul>
                    </div>

                    <!-- Schedule -->
                    <div class="card-box" id="schedule_card">
                        <div class="card-header" id="schedule_header">
                            <h4><i class="fa-solid fa-calendar-days"></i> Schedule</h4>
                        </div>

                        <table class="schedule-table" id="schedule_table">
                            <tr id="schedule_row1"><td>Mon - Fri</td><td>8am - 5pm</td></tr>
                            <tr id="schedule_row2"><td>Sat</td><td>9am - 2pm</td></tr>
                            <tr id="schedule_row3"><td>Sun</td><td>Off</td></tr>
                        </table>
                    </div>

                </div>

                <!-- Notes | Education | Experience -->
                <div class="triple-card-row" id="second_triple_row">

                    <!-- Notes -->
                    <div class="card-box" id="notes_card">
                        <div class="card-header" id="notes_header">
                            <h4><i class="fa-solid fa-notes-medical"></i> Patient Notes</h4>
                        </div>

                        <div class="notes" id="patient_notes">
                            <blockquote id="note_1">“Very kind and thorough.”</blockquote>
                            <blockquote id="note_2">“Explained diagnosis clearly.”</blockquote>
                        </div>
                    </div>

                    <!-- Education -->
                    <div class="card-box" id="education_card">
                        <div class="card-header" id="education_header">
                            <h4><i class="fa-solid fa-graduation-cap"></i> Education</h4>
                        </div>

                        <div class="edu-item" id="education_item">
                            <div id="edu_year"><strong>Year:</strong> 2010</div>
                            <div id="edu_degree"><strong>Degree:</strong> MBBS</div>
                            <div id="edu_institute"><strong>Institute:</strong> University of Lagos</div>
                            <div id="edu_result"><strong>Result:</strong> Distinction</div>
                        </div>
                    </div>

                    <!-- Experience -->
                    <div class="card-box" id="experience_card">
                        <div class="card-header" id="experience_header">
                            <h4><i class="fa-solid fa-briefcase-medical"></i> Experience</h4>
                        </div>

                        <div class="exp-item" id="experience_item">
                            <div id="exp_year"><strong>Year:</strong> 2015 - Present</div>
                            <div id="exp_department"><strong>Department:</strong> Cardiology</div>
                            <div id="exp_position"><strong>Position:</strong> Consultant</div>
                            <div id="exp_hospital"><strong>Hospital:</strong> Lagos General Hospital</div>
                            <div id="exp_feedback"><strong>Feedback:</strong> Excellent patient reviews.</div>
                        </div>
                    </div>

                </div>



            </div>
            </section>


       <!-- Doctor Profile Modal -->
        <div id="doctorProfileModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeDoctorModal()">&times;</span>

            <div class="profile-header">
            <img id="doctorImage" src="../uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
            <div class="profile-info">
                <h2 id="doctorName">Dr. John Doe</h2>
                <p id="doctorSpeciality">Cardiologist</p>
                <p><strong>Experience:</strong> <span id="doctorExperience">10 years</span></p>
                <p><strong>License:</strong> <span id="doctorLicense">ABC123</span></p>
            </div>
            </div>

            <div class="profile-details">
            <p><strong>Country:</strong> <span id="doctorCountry">Nigeria</span></p>
            </div>
        </div>
        </div>

    </div>


   





      <div class="fill-form-div login-div" id="next_3">
            <section class="setting-section">
                <div class="inner-div">

                <!-- Horizontal Tab Header -->
                <div class="settings-header">
                    <ul>
                    <li class="active" data-target="profile-tab">Edit Profile</li>
                    <li data-target="password-tab">Change Password</li>
                    <li data-target="email-tab">Email Notifications</li>
                    <li data-target="contact-tab">Manage Contact Info</li>
                    </ul>
                </div>

                <!-- Tab Contents -->
                <div class="settings-content">

                
                <!-- Edit Profile -->
                <div id="profile-tab" class="tab-pane active">
                <h3>Edit Profile</h3>
                <hr>

                <form id="profile-form" method="post" enctype="multipart/form-data" class="form-div">

                    <!-- ===== STEP 1: PERSONAL INFORMATION ===== -->
                    <div class="form-step active">
                        <h4>SECTION 1: PERSONAL INFORMATION</h4>

                        <div class="form-group">
                            <label>Profile Photo</label>
                            <div class="profile-pic-div">
                                <img src="<?php echo $website_url; ?>/uploaded_files/<?php echo $passport=='' ? 'doctor_profile_pix/001.png' : 'profile_pix/'.$passport; ?>" 
                                    id="profile_photo" alt="Profile Picture"/>
                                <button type="button" onclick="open_file()" class="upload-btn">Change Picture</button>
                            </div>
                        </div>

                        <div class="form-group"><label>Full Name *</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>

                        <div class="form-group"><label>Date of Birth *</label>
                            <input type="date" id="dob" name="dob" required>
                        </div>

                        <div class="form-group"><label>Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group"><label>Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>

                        <div class="form-group"><label>Residential Address *</label>
                            <input type="text" id="address" name="address" required>
                        </div>

                        <div class="form-row">
                            <input type="text" id="city" name="city" placeholder="City" required>
                            <input type="text" id="state" name="state" placeholder="State" required>
                            <input type="text" id="zip" name="zip" placeholder="ZIP" required>
                        </div>

                        <button type="button" class="save-btn" onclick="save_progress(1)">Save Progress</button>
                        <button type="button" class="next-btn">Next</button>
                    </div>

                    <!-- ===== STEP 2: PROFESSIONAL CREDENTIALS ===== -->
                    <div class="form-step">
                        <h4>SECTION 2: PROFESSIONAL CREDENTIALS</h4>

                        <div class="form-group"><label>Medical License Number *</label>
                            <input type="text" id="license_number" name="license_number" required>
                        </div>

                        <div class="form-group"><label>License Issuing State/Country *</label>
                            <input type="text" id="license_country" name="license_country" required>
                        </div>

                        <div class="form-group"><label>License Expiration Date *</label>
                            <input type="date" id="license_expiry" name="license_expiry" required>
                        </div>

                        <div class="form-group"><label>Board Certification *</label>
                            <input type="text" id="board_cert" name="board_cert" required>
                        </div>

                        <div class="form-group"><label>Primary Specialty *</label>
                            <input type="text" id="primary_specialty" name="primary_specialty" required>
                        </div>

                        <div class="form-group"><label>Sub-specialties</label>
                            <input type="text" id="sub_specialties" name="sub_specialties">
                        </div>

                        <div class="form-group"><label>Medical School *</label>
                            <input type="text" id="medical_school" name="medical_school" required>
                        </div>

                        <div class="form-row">
                            <input type="text" id="grad_year" name="grad_year" placeholder="Year of Graduation" required>
                            <input type="text" id="residency" name="residency" placeholder="Residency/Fellowship" required>
                            <input type="number" id="experience" name="experience" placeholder="Years of Experience" required>
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="save-btn" onclick="save_progress(2)">Save Progress</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- ===== STEP 3: PRACTICE INFO ===== -->
                    <div class="form-step">
                        <h4>SECTION 3: PROFESSIONAL PRACTICE INFORMATION</h4>

                        <div class="form-group"><label>Current Hospital Affiliations *</label>
                            <input type="text" id="hospital_affiliations" name="hospital_affiliations" required>
                        </div>

                        <div class="form-group"><label>Practice Name (if applicable)</label>
                            <input type="text" id="practice_name" name="practice_name">
                        </div>

                        <div class="form-group"><label>Practice Address *</label>
                            <input type="text" id="practice_address" name="practice_address" required>
                        </div>

                        <div class="form-group"><label>Languages Spoken *</label>
                            <input type="text" id="languages" name="languages" required>
                        </div>

                        <div class="form-group"><label>Areas of Expertise *</label>
                            <input type="text" id="expertise" name="expertise" required>
                        </div>

                        <div class="form-group"><label>Consultation Fee ($)</label>
                            <input type="number" id="consultation_fee" name="consultation_fee">
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="save-btn" onclick="save_progress(3)">Save Progress</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- ===== STEP 4: TECHNICAL + LEGAL ===== -->
                    <div class="form-step">
                        <h4>SECTION 4 & 5: TECHNICAL + LEGAL</h4>

                        <div class="form-row">
                            <label>Primary Device:</label>
                            <select id="device" name="device">
                                <option value="">Select Device</option>
                                <option>Desktop</option>
                                <option>Laptop</option>
                                <option>Tablet</option>
                                <option>Smartphone</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <label>Webcam Available:</label>
                            <select id="webcam" name="webcam">
                                <option>Yes</option><option>No</option>
                            </select>

                            <label>Microphone Available:</label>
                            <select id="microphone" name="microphone">
                                <option>Yes</option><option>No</option>
                            </select>
                        </div>

                        <h5>Legal & Compliance</h5>

                        <label><input type="checkbox" id="hipaa" name="hipaa" required> I agree to HIPAA compliance requirements</label><br>
                        <label><input type="checkbox" id="telemedicine_rules" name="telemedicine_rules" required> I acknowledge telemedicine regulations in my state</label><br>
                        <label><input type="checkbox" id="background_check" name="background_check" required> I consent to background verification</label>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="save-btn" onclick="save_progress(4)">Save Progress</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- ===== STEP 5: AVAILABILITY ===== -->
                    <div class="form-step">
                        <h4>SECTION 6: AVAILABILITY & PREFERENCES</h4>

                        <div class="form-group"><label>Time Zone *</label>
                            <input type="text" id="timezone" name="timezone" required>
                        </div>

                        <div class="form-group"><label>Preferred Consultation Hours *</label>
                            <input type="text" id="consultation_hours" name="consultation_hours" required>
                        </div>

                        <div class="checkbox-group">
                            <label><input type="checkbox" id="consult_video" name="consultation_types[]" value="Video"> Video</label>
                            <label><input type="checkbox" id="consult_phone" name="consultation_types[]" value="Phone"> Phone</label>
                            <label><input type="checkbox" id="consult_message" name="consultation_types[]" value="Messaging"> Messaging</label>
                        </div>

                        <div class="form-group"><label>Maximum Patients Per Day *</label>
                            <input type="number" id="max_patients" name="max_patients" required>
                        </div>

                        <div class="form-group"><label>Emergency Contact *</label>
                            <input type="text" id="emergency_contact" name="emergency_contact" required>
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="save-btn" onclick="save_progress(5)">Save Progress</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- ===== STEP 6: PAYMENT ===== -->
                    <div class="form-step">
                        <h4>SECTION 7: PAYMENT INFORMATION</h4>

                        <div class="form-group"><label>Bank Name *</label>
                            <input type="text" id="bank_name" name="bank_name" required>
                        </div>

                        <div class="form-group"><label>Account Number *</label>
                            <input type="text" id="account_number" name="account_number" required>
                        </div>

                        <div class="form-group"><label>Routing Number *</label>
                            <input type="text" id="routing_number" name="routing_number" required>
                        </div>

                        <div class="form-group"><label>Tax ID (SSN/EIN) *</label>
                            <input type="text" id="tax_id" name="tax_id" required>
                        </div>

                        <div class="form-group"><label>Insurance Plans Accepted</label>
                            <input type="text" id="insurance" name="insurance">
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="save-btn" onclick="save_progress(6)">Save Progress</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- ===== STEP 7: DOCUMENT UPLOAD ===== -->
                    <div class="form-step">
                        <h4>SECTION 8: DOCUMENT UPLOAD</h4>

                        <label>Government Issued ID</label>
                        <input type="file" id="id_upload" name="id_upload" accept=".pdf,.jpg,.png"><br>

                        <label>Medical License</label>
                        <input type="file" id="license_upload" name="license_upload" accept=".pdf,.jpg,.png"><br>

                        <label>Board Certification</label>
                        <input type="file" id="board_upload" name="board_upload" accept=".pdf,.jpg,.png"><br>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="save-btn" onclick="save_progress(7)">Save Progress</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- ===== STEP 8: AGREEMENT ===== -->
                    <div class="form-step">
                        <h4>SECTION 9: AGREEMENT</h4>

                        <label><input type="checkbox" id="terms" name="terms" required> I accept the Terms of Service</label><br>
                        <label><input type="checkbox" id="privacy" name="privacy" required> I acknowledge the Privacy Policy</label>

                        <div class="form-group"><label>Electronic Signature *</label>
                            <input type="text" id="signature" name="signature" required>
                        </div>

                        <div class="form-group"><label>Date *</label>
                            <input type="date" id="agreement_date" name="agreement_date" required>
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="prev-btn">Previous</button>
                            <!-- <button type="button" class="save-btn" onclick="save_progress(8)">Save Progress</button> -->
                            <button type="submit" onclick="update_profile();" class="save-btn">Submit</button>
                        </div>
                    </div>

                </form>


                </div>



                    <!-- Change Password -->
                    <div id="password-tab" class="tab-pane">
                    <h3>Change Password</h3>
                    <hr>
                    <form id="password-form" method="post" class="form-div">
                        <div class="form-group">
                        <label for="old-password">Old Password</label>
                        <input type="password" id="old_password" name="old_password" required>
                        </div>

                        <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new_password" onkeyup="checkPasswordStrength()" name="new_password" required>
                        </div>

                        <div class="form-group">
                        <label for="confirm-password">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" onkeyup="checkPasswordMatch()" name="confirm_new_password" required>
                        </div>

                        <div class="form-group" style="margin-top:5px;">
                        <i id="togglePassword" class="bi bi-eye" onclick="icon_toggle()" style="cursor:pointer; font-size:16px;"></i>
                        <i id="togglePassword2" class="bi bi-eye-slash" onclick="icon_toggle()" style="cursor:pointer; font-size:16px; display:none;"></i>
                        </div>

                        <div class="pswd_info3" style="display:none; margin-top:5px;">
                        <div class="strength-bar-container2">
                            <div class="strength-bar2"></div>
                        </div>
                        <p class="strength-text2">Password strength: Weak</p>
                        <small class="strength-requirements2">
                            At least 8 characters required including upper & lower cases, numbers, and special characters
                        </small>
                        </div>

                        <p id="matchMessage" style="font-size:12px; margin-top:5px; color:red; display:none;">Passwords do not match</p>
                        <button type="button" onclick="update_password();" class="save-btn" style="margin-top:10px;">Update Password</button>
                    </form>
                    </div>

                    <!-- Email Notifications -->
                    <div id="email-tab" class="tab-pane">
                    <h3>Email Notifications</h3>
                    <hr>
                    <div class="form-group">
                        <label>Receive Notification Emails</label>
                        <label class="switch">
                        <input type="checkbox" id="email_toggle" checked>
                        <span class="slider round"></span>
                        </label>
                    </div>
                    <button type="button" class="save-btn">Save Preference</button>
                    </div>

                    <!-- Manage Contact Info -->
                    <div id="contact-tab" class="tab-pane">
                    <h3>Manage Contact Info</h3>
                    <hr>
                    <form id="contact-form" class="form-div">
                        <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email" value="<?php echo $member_email; ?>" required>
                        </div>
                        <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="phone_number" value="<?php echo $member_phone; ?>" required>
                        </div>
                        <div class="form-group">
                        <label>Address</label>
                        <textarea id="address" rows="3"><?php echo $member_address; ?></textarea>
                        </div>
                        <button type="button" onclick="update_contact();" class="save-btn">Save Changes</button>
                    </form>
                    </div>

                </div>
                </div>
            </section>
        </div>





  

     <!-- <div class="fill-form-div login-div"  id="next_4">
        <section class="chat-section">

        </section>

     </div> -->

     <?php include "chat-script.php";?>
    
 
    <script>

           // --- Initialize WebSocket globally ---
        const ws = new WebSocket("ws://localhost:8080");
    </script>

    <script>
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-img').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
        }

        // Open modal
        document.getElementById('open-profile-modal').onclick = () => {
        document.getElementById('profile-modal').style.display = 'flex';
        }
        document.getElementById('open-password-modal').onclick = () => {
        document.getElementById('password-modal').style.display = 'flex';
        }

        // Close modal
        document.querySelectorAll('.close').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById(this.dataset.close).style.display = 'none';
        });
        });

        // Close if clicked outside modal content
        window.onclick = function(event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) {
            modal.style.display = "none";
            }
        });
        }

        //////////////for image upload and preview /////////////
        // Get the existing profile image
        var changeBtn = document.getElementById("change-btn");

        // Create a hidden file input
        var fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.accept = "image/*";

        // Named function to open file manager (called from button)
        function open_file() {
            fileInput.click(); // ✅ opens file manager
        }

        // When user selects a file, update the existing image
        fileInput.addEventListener("change", function showSelectedImage() {
            if (this.files && this.files[0]) {
                var selectedFile = this.files[0];

                var reader = new FileReader();
                reader.onload = function(e) {
                    changeBtn.src = e.target.result; // update existing image
                };
                reader.readAsDataURL(selectedFile);

                // Upload immediately
                uploadImageToServer(selectedFile);
            }
        });

    </script>


       


       <script>
document.querySelectorAll('.settings-header li').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.settings-header li').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(content => content.classList.remove('active'));
    this.classList.add('active');
    document.getElementById(this.dataset.target).classList.add('active');
  });
});





</script>

        <script>
        const steps = document.querySelectorAll('.form-step');
        let currentStep = 0;

        function showStep(index) {
        steps.forEach((step, i) => step.classList.toggle('active', i === index));
        }

        document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
            }
        });
        });

        document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
            }
        });
        });

        showStep(currentStep);
        </script>


<!-- <script>
    $(function() {
        var Test = {
            UpdatePreview: function(obj) {
                if (!window.FileReader) {
                    // Browser doesn't support FileReader
                    return;
                }

                var reader = new FileReader();
                
                reader.onload = function(e) {
                    // Update the src of the image to show the preview
                    $('#change-btn').prop("src", e.target.result);
                };

                reader.readAsDataURL(obj.files[0]); // Read the selected file
            }
        };

        // Example usage: Assuming there's a file input with id="file-input"
        $('#my_passport').on('change', function() {
            Test.UpdatePreview(this);
        });
    });
</script> -->


<script>
           document.addEventListener("DOMContentLoaded", function () {
  const calendarHeader = document.getElementById("calendar-header");
  const calendarDays = document.getElementById("calendar-days");
  const calendarDates = document.getElementById("calendar-dates");
  const prevMonthBtn = document.getElementById("prevMonth");
  const nextMonthBtn = document.getElementById("nextMonth");
  const appointmentsBody = document.getElementById("appointments_body");

  const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
  calendarDays.innerHTML = daysOfWeek.map(day => `<div>${day}</div>`).join("");

  let currentDate = new Date();

  function renderCalendar() {
    calendarDates.innerHTML = "";

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const prevLastDay = new Date(year, month, 0);

    const prevDays = prevLastDay.getDate();
    const lastDate = lastDay.getDate();
    const firstDayIndex = firstDay.getDay();
    const lastDayIndex = lastDay.getDay();
    const nextDays = 6 - lastDayIndex;

    calendarHeader.textContent = `Nearest Treatment - ${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;

    for (let x = firstDayIndex; x > 0; x--) {
      const div = document.createElement("div");
      div.textContent = prevDays - x + 1;
      div.classList.add("inactive");
      calendarDates.appendChild(div);
    }

    for (let i = 1; i <= lastDate; i++) {
      const div = document.createElement("div");
      div.textContent = i;
      if (
        i === new Date().getDate() &&
        month === new Date().getMonth() &&
        year === new Date().getFullYear()
      ) {
        div.classList.add("current-day");
      }
      calendarDates.appendChild(div);
    }

    for (let j = 1; j <= nextDays; j++) {
      const div = document.createElement("div");
      div.textContent = j;
      div.classList.add("inactive");
      calendarDates.appendChild(div);
    }
  }

  prevMonthBtn.onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
  };

  nextMonthBtn.onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
  };

  renderCalendar();

  // ==== Appointment Modal Logic ====
  const modal = document.getElementById("appointmentModal");
  const closeModal = document.querySelector(".close-modal");
  const appointmentForm = document.getElementById("appointmentForm");
  const appointmentFields = document.getElementById("appointmentFields");
  const addMoreAppointment = document.getElementById("addMoreAppointment");
  const modalTitle = document.getElementById("modal-date-title");

  let selectedDate = null;

  calendarDates.addEventListener("click", function (e) {
    if (e.target.tagName === "DIV" && !e.target.classList.contains("inactive")) {
      selectedDate = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        e.target.textContent
      );
      const formattedDate = selectedDate.toDateString();
      modalTitle.textContent = "Set Appointment for " + formattedDate;
      document.getElementById("appointment_date").value = formattedDate;
      modal.style.display = "flex";
    }
  });

  closeModal.onclick = () => (modal.style.display = "none");
  window.onclick = e => { if (e.target === modal) modal.style.display = "none"; };

    document.getElementById("addMoreAppointment").addEventListener("click", function () {
    const container = document.getElementById("appointmentFields");
    const newRow = document.createElement("div");
    newRow.classList.add("appointment-row");
    newRow.innerHTML = `
        <input type="text" name="patient[]" placeholder="Patient Name" required>
        <input type="time" name="time[]" required>
        <input type="text" name="reason[]" placeholder="Reason for Visit" required>
        <select name="status[]" required>
        <option value="pending" selected>Pending</option>
        <option value="approved">Approved</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
        </select>
    `;
    container.appendChild(newRow);
    });


  appointmentForm.onsubmit = (e) => {
    e.preventDefault();
    const patientInputs = appointmentForm.querySelectorAll("input[name='patient[]']");
    const timeInputs = appointmentForm.querySelectorAll("input[name='time[]']");
    let html = "";

    for (let i = 0; i < patientInputs.length; i++) {
      const patient = patientInputs[i].value.trim();
      const time = timeInputs[i].value;
      if (patient && time) {
        html += `
          <tr>
            <td>${patient}</td>
            <td>${selectedDate.toDateString()}</td>
            <td>${time}</td>
          </tr>
        `;
      }
    }

    if (html) {
      if (appointmentsBody.querySelector("td[colspan='3']")) {
        appointmentsBody.innerHTML = "";
      }
      appointmentsBody.insertAdjacentHTML("beforeend", html);
    }

    appointmentForm.reset();
    appointmentFields.innerHTML = `
      <div class="appointment-row">
        <input type="text" name="patient[]" placeholder="Patient Name" required>
        <input type="time" name="time[]" required>
      </div>`;
    modal.style.display = "none";
  };
});





            





    



        </script>


<?php }?>
<script>
    AOS.init({
    easing: 'ease-in-out-sine'
    });
</script>







