
<?php if ($page=='dashboard'){?>
<?php    
  
  $member_id=$_POST['member_id'];


   
?>
 
    <!-- ////index  -->
    <div class="fill-form-div login-div"  id="next_1"> 
        <?php include "index-phone-div.php"?>
        <header class="fadeInDown animated">
        </header>

        <section class="dashboard-session">
            <div class="inner-div">
                <div class="left-column">
                        <div class="profile-info-div">
                            <div class="heading-div">
                            <h3>Patient Info</h3>
                        </div>
                        <div class="inner-div">
                            <div class="profile-card">
                                <div class="img-div">
                                <?php if ($passport==''){?>
                                    <img src="<?php echo $website_url; ?>/uploaded_files/patient_profile_pix/11.png" 
                                        id="my_passport2" alt="profile picture"/>
                                <?php } else { ?>
                                    <img src="<?php echo $website_url; ?>/uploaded_files/profile_pix/<?php echo $passport; ?>" 
                                        id="my_passport" alt="profile picture"/>
                                <?php } ?>
                                </div>
                                <div class="text-div">
                                <h3><?php echo $member_fullname; ?></h3>
                                <p><b>Patient ID:</b> <?php echo $member_id; ?></p>
                                <p><b>Age:</b> <?php echo $member_age; ?> Years</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="recent-info-div">
                        <div class="inner-div">
                            <div class="heading-div">
                                <h3>Recently Contacted Doctors</h3>
                            </div>

                            <div class="recent-scroll">
                                <ul class="recent-list">
                                    <li onclick="open_chat();">
                                        <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                                        <div class="doctor-info">
                                            <p class="name">Dr. John Smith</p>
                                            <p class="specialty">Cardiologist</p>
                                            <p class="date">Last contacted: 12 Sep 2025</p>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                                        <div class="doctor-info">
                                            <p class="name">Dr. Sarah Lee</p>
                                            <p class="specialty">Neurologist</p>
                                            <p class="date">Last contacted: 10 Sep 2025</p>
                                        </div>
                                    </li>

                                    <li>
                                        <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                                        <div class="doctor-info">
                                            <p class="name">Dr. John Smith</p>
                                            <p class="specialty">Neurologist</p>
                                            <p class="date">Last contacted: 10 Sep 2025</p>
                                        </div>
                                    </li>
                                    <!-- Repeat dynamically with PHP loop -->
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
                                    <th>Doctor</th>
                                    <th>Prescription</th>
                                    </tr>
                                </thead>
                                <tbody id="all-entries-body">
                                    <tr>
                                        <td>2025-09-10</td>
                                        <td>Dr. Adebayo Johnson</td>
                                        <td>Paracetamol 500mg (2x daily)</td>
                                    </tr>
                                    <tr>
                                        <td>2025-09-05</td>
                                        <td>Dr. Chioma Okafor</td>
                                        <td>Amoxicillin 250mg (3x daily)</td>
                                    </tr>
                                    <tr>
                                        <td>2025-08-28</td>
                                        <td>Dr. Musa Ibrahim</td>
                                        <td>Cough Syrup (10ml, 2x daily)</td>
                                    </tr>
                                    <tr>
                                        <td>2025-08-15</td>
                                        <td>Dr. Grace Oladipo</td>
                                        <td>Vitamin D Supplements</td>
                                    </tr>
                                    <tr>
                                        <td>2025-07-30</td>
                                        <td>Dr. Kelvin Mensah</td>
                                        <td>Ibuprofen 400mg (as needed)</td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    
                    </div>
                </div>


                <div class="chat-div">
               <div class="chat-header">
                    <div class="chat-user">
                        <strong>Dr. John Doe</strong><br>
                        <span class="status">Online</span>
                    </div>
                    <div class="chat-actions">
                        <button class="icon-btn" title="Video Call">
                        <i class="bi bi-camera-video"></i>
                        </button>
                    </div>
                </div>


                <div class="chat-messages" id="chatMessages">
                    <div class="message received">Hey! How are you?</div>
                    <div class="message sent">
                        <span class="text">I'm good, thanks! How about you?</span>
                        <span class="ticks">✓✓</span>
                    </div>
                    <div class="message received">Doing well, just chilling.</div>
                </div>

                <div class="chat-input">
                    <textarea id="chatInput" placeholder="Type a message..."></textarea>
                    <button onclick="send_chat();" id="sendBtn">Send</button>
                </div>
                </div>



        </section>
    </div>  

    <div class="fill-form-div login-div"  id="next_2"> 
       <section class="doctor-list-section">
            <div class="inner-div">
                <div class="doctor-list-heading">
                    <div class="div-in">
                        <h3>Doctors List</h3>
                        <p>Browse and connect with our experienced doctors.</p>
                    </div>
                    
                </div>
               <div class="doctor-list-div">
                    <div class="doctor-card-container">
                       <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                               <h4>Dr. Jon Snow</h4>
                                <p class="role">Cardiologist</p>
                                <p class="desc">Specialist in heart and vascular health with 10+ years of experience.</p>
                                <div class="rating">★★★★★</div>
                                <div class="actions">
                                <button class="view-btn">View Profile</button>
                                <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Tyrion Lannister</h4>
                                <p class="role">Pediatrician</p>
                                <p class="desc">Expert in child healthcare and wellness programs.</p>
                                <div class="rating">★★★★☆</div>
                                <div class="actions">
                                <button class="view-btn">View Profile</button>
                                <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Daenerys Targaryen</h4>
                                <p class="role">Neurologist</p>
                                <p class="desc">Specialist in brain and nervous system disorders with global recognition.</p>
                                <div class="rating">★★★★★</div>
                                <div class="actions">
                                    <button class="view-btn">View Profile</button>
                                    <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Jon Snow</h4>
                                <p class="role">Cardiologist</p>
                                <p class="desc">Dedicated to heart and vascular health with years of life-saving expertise.</p>
                                <div class="rating">★★★★☆</div>
                                <div class="actions">
                                    <button class="view-btn">View Profile</button>
                                    <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Arya Stark</h4>
                                <p class="role">Surgeon</p>
                                <p class="desc">Highly skilled in precision surgeries and rapid recovery techniques.</p>
                                <div class="rating">★★★★★</div>
                                <div class="actions">
                                    <button class="view-btn">View Profile</button>
                                    <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Cersei Lannister</h4>
                                <p class="role">Oncologist</p>
                                <p class="desc">Expert in cancer treatment and patient-centered therapy programs.</p>
                                <div class="rating">★★★☆☆</div>
                                <div class="actions">
                                    <button class="view-btn">View Profile</button>
                                    <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Sansa Stark</h4>
                                <p class="role">Dermatologist</p>
                                <p class="desc">Focused on skincare, aesthetics, and long-term skin health solutions.</p>
                                <div class="rating">★★★★☆</div>
                                <div class="actions">
                                    <button class="view-btn">View Profile</button>
                                    <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                        <div class="doctor-card">
                            <img src="<?php echo $website_url; ?>/uploaded_files/doctor_profile_pix/doc_default.jpeg" alt="Doctor">
                            <div class="doctor-info">
                                <h4>Dr. Khal Drogo</h4>
                                <p class="role">Orthopedic Specialist</p>
                                <p class="desc">Master of bone, joint, and sports injury treatments with warrior spirit.</p>
                                <div class="rating">★★★★★</div>
                                <div class="actions">
                                    <button class="view-btn">View Profile</button>
                                    <button class="chat-btn">Chat Up</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            
        </section>
    </div>

    <div class="fill-form-div login-div"  id="next_3">
      <section class="setting-section">
        <div class="inner-div">

            <!-- Card: Account Settings -->
            <div class="card clickable" id="open-profile-modal">
            <h3 class="card-heading">Account Settings</h3>
            <p>Update your profile picture and full name.</p>
            </div>

            <!-- Card: Change Password -->
            <div class="card clickable" id="open-password-modal">
            <h3 class="card-heading">Change Password</h3>
            <p>Update your account password securely.</p>
            </div>
        </div>
        </section>

        <!-- Profile Modal -->
        <div class="modal" id="profile-modal">
            <div class="modal-content">
                <span class="close" data-close="profile-modal">&times;</span>
                <h3>Profile Settings</h3>
                <hr>
                <form id="profile-form" method="post" enctype="multipart/form-data" class="form-div">
                <div class="form-group">
                    <label for="my_passport">Profile Picture</label>
                    <div class="profile-pic-div">
                    <img src="<?php echo $website_url; ?>/uploaded_files/<?php echo $passport=='' ? 'patient_profile_pix/001.png' : 'profile_pix/'.$passport; ?>" 
                        id="change-btn" alt="Profile Picture"/>
                    <button class="upload-btn">Change Picture</button>
                    <div class="preview-div">
                        <img id="preview-img" src="#" alt="Image Preview" style="display: none;"/>
                    </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full_name" value="<?php echo $member_fullname; ?>" required>
                </div>
                <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Password Modal -->
        <div class="modal" id="password-modal">
        <div class="modal-content">
            <span class="close" data-close="password-modal">&times;</span>
            <h3>Change Password</h3>
            <form id="password-form" method="post" class="form-div">
            <div class="form-group">
                <label for="old-password">Old Password</label>
                <input type="password" id="old-password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
            </div>
            <button type="submit" class="save-btn">Update Password</button>
            </form>
        </div>
        </div>

    </div>

     <!-- <div class="fill-form-div login-div"  id="next_4">
        <section class="chat-section">

        </section>

     </div> -->

    
   
 
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


            const chatMessages = document.getElementById("chatMessages");
        const chatInput = document.getElementById("chatInput");

        function send_chat(event) {
            // Handle "Enter" vs "Shift+Enter"
            if (event && event.type === "keydown") {
                if (event.key === "Enter" && !event.shiftKey) {
                    event.preventDefault(); // stop newline
                } else {
                    return; // allow Shift+Enter to work
                }
            }

            const text = chatInput.value.trim();
            if (!text) return;

            const msg = document.createElement("div");
            msg.className = "message sent";
            msg.innerHTML = `<span class="text">${text}</span><span class="ticks">✓</span>`;
            chatMessages.appendChild(msg);

            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatInput.value = "";

            const ticks = msg.querySelector(".ticks");
            setTimeout(() => (ticks.textContent = "✓✓"), 1000);
            setTimeout(() => (ticks.style.color = "blue"), 2000);

            if (text.toLowerCase().includes("prescribe")) {
                setTimeout(() => {
                    const reply = document.createElement("div");
                    reply.className = "message received";
                    reply.textContent = "✅ Added to prescription list.";
                    chatMessages.appendChild(reply);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 1500);
            }
        }

        chatInput.addEventListener("keydown", send_chat);


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







