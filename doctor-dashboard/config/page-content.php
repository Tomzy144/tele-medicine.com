
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
                            <p id="currentDate"></p>
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
                                    <p>Appointments: <span> 500</span> </p>
                                    <p>Total Consualts:  <span> 100</span></p>
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
                            <table class="appointments-table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Timing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Petey Cruiser</td>
                                        <td>20/02/2020</td>
                                        <td>8:00 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Anna Sthesia</td>
                                        <td>25/02/2020</td>
                                        <td>8:30 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Paul Molive</td>
                                        <td>25/02/2020</td>
                                        <td>9:45 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Anna Mull</td>
                                        <td>27/02/2020</td>
                                        <td>11:30 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Paige Turner</td>
                                        <td>28/02/2020</td>
                                        <td>3:30 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Don Stairs</td>
                                        <td>28/02/2020</td>
                                        <td>4:30 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Pat Agonia</td>
                                        <td>29/02/2020</td>
                                        <td>5:00 PM</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                      

                    </div>

                </div>








        </section>
    </div>  

    <div class="fill-form-div login-div"  id="next_2"> 
       <section class="doctor-profile-section">
            <div class="inner-div">

                <!-- Profile Overview -->
                <div class="profile-overview">
                    <div class="profile-card">
                        <div class="profile-top">
                            <div class="profile-image">
                                <img src="../uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor" alt="Doctor Photo">
                            </div>
                            <div class="profile-info">
                                <h3 class="doctor-name">Dr. John Doe</h3>
                                <p class="doctor-title">Consultant Cardiologist</p>
                                <div class="badge">Active</div>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-box">
                                <h4>120</h4>
                                <p>Appointments</p>
                            </div>
                            <div class="stat-box">
                                <h4>95</h4>
                                <p>Patients</p>
                            </div>
                            <div class="stat-box">
                                <h4>12</h4>
                                <p>Years Exp.</p>
                            </div>
                        </div>
                    </div>

                <div class="info-card">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-user"></i> Personal Information</h4>
                    </div>
                    <div class="info-grid">
                    <div><strong>First Name:</strong> John</div>
                    <div><strong>Last Name:</strong> Doe</div>
                    <div><strong>Age:</strong> 38</div>
                    <div><strong>Position:</strong> Consultant Cardiologist</div>
                    <div><strong>Email:</strong> johndoe@gmail.com</div>
                    <div><strong>Phone:</strong> +234 801 234 5678</div>
                    <div><strong>Location:</strong> Lagos, Nigeria</div>
                    </div>
                </div>
                </div>

                <!-- Specialty | Notifications | Schedule -->
                <div class="triple-card-row">
                <div class="card-box">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-stethoscope"></i> Specialty</h4>
                    </div>
                    <ul class="list-dashed">
                    <li>Heart Disease</li>
                    <li>Hypertension</li>
                    <li>Cardiac Surgery</li>
                    <li>Preventive Cardiology</li>
                    </ul>
                </div>

                <div class="card-box">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-bell"></i> Notifications</h4>
                    </div>
                    <ul class="list-striped">
                    <li>New message from patient</li>
                    <li>Appointment reminder</li>
                    <li>Lab results ready for review</li>
                    </ul>
                </div>

                <div class="card-box">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-calendar-days"></i> Schedule</h4>
                    </div>
                    <table class="schedule-table">
                    <tr><td>Mon - Fri</td><td>8am - 5pm</td></tr>
                    <tr><td>Sat</td><td>9am - 2pm</td></tr>
                    <tr><td>Sun</td><td>Off</td></tr>
                    </table>
                </div>
                </div>

                <!-- Notes | Education | Experience -->
                <div class="triple-card-row">
                <div class="card-box">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-notes-medical"></i> Patient Notes</h4>
                    </div>
                    <div class="notes">
                    <blockquote>“Very kind and thorough.”</blockquote>
                    <blockquote>“Explained diagnosis clearly.”</blockquote>
                    </div>
                </div>

                <div class="card-box">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-graduation-cap"></i> Education</h4>
                    </div>
                    <div class="edu-item">
                    <div><strong>Year:</strong> 2010</div>
                    <div><strong>Degree:</strong> MBBS</div>
                    <div><strong>Institute:</strong> University of Lagos</div>
                    <div><strong>Result:</strong> Distinction</div>
                    </div>
                </div>

                <div class="card-box">
                    <div class="card-header">
                    <h4><i class="fa-solid fa-briefcase-medical"></i> Experience</h4>
                    </div>
                    <div class="exp-item">
                    <div><strong>Year:</strong> 2015 - Present</div>
                    <div><strong>Department:</strong> Cardiology</div>
                    <div><strong>Position:</strong> Consultant</div>
                    <div><strong>Hospital:</strong> Lagos General Hospital</div>
                    <div><strong>Feedback:</strong> Excellent patient reviews.</div>
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
                        <div class="form-group">
                        <label for="my_passport">Profile Picture</label>
                        <div class="profile-pic-div">
                            <img src="<?php echo $website_url; ?>/uploaded_files/<?php echo $passport=='' ? 'doctor_profile_pix/001.png' : 'profile_pix/'.$passport; ?>" 
                                id="change-btn" alt="Profile Picture"/>
                            <button type="button" onclick="open_file()" class="upload-btn">Change Picture</button>
                        </div>
                        </div>
                        <div class="form-group">
                        <label for="full-name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo $member_fullname; ?>" required>
                        </div>
                        <button type="button" onclick="update_profile();" class="save-btn">Save Changes</button>
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

        // Handle profile form submit
        document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("Profile form submitted with picture and name");
        // AJAX request here
        });

        // Handle password form submit
        document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("Password form submitted with old/new/confirm");
        // AJAX request here
        });
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
           document.addEventListener("DOMContentLoaded", function () {
            const calendarHeader = document.getElementById("calendar-header");
            const calendarDays = document.getElementById("calendar-days");
            const calendarDates = document.getElementById("calendar-dates");
            const prevMonthBtn = document.getElementById("prevMonth");
            const nextMonthBtn = document.getElementById("nextMonth");

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


<?php }?>
<script>
    AOS.init({
    easing: 'ease-in-out-sine'
    });
</script>







