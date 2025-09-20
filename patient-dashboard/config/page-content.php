
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
                            <li>
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

        </div>
    </section>




   
 

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







