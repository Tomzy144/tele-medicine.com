
<?php if ($page=='dashboard'){?>
<?php    
  
  $member_id=$_POST['member_id'];


   
?>
 
    <!-- ////index  -->
    <div class="fill-form-div login-div"  id="next_1"> 
    <?php include "index-phone-div.php"?>
    <header class="fadeInDown animated">
    </header>



   
    <section class="input-section fadeIn animated">
        <p>Hi <span id="name">XXXXXXXX</span> Welcome to Dashboard</p>
        
        <div class="inner-div">
            <hr>
            
            <table border="1" id="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th rowspan="2">DATE</th>
            <th rowspan="2">PARTICULARS</th>
            <th colspan="3">SHARES (₦)</th>
            <th colspan="3">SAVINGS (₦)</th>
            <th colspan="3">LOANS (₦)</th>
            <th colspan="3">DEPOSITS (₦)</th>
            <th rowspan="2"></th> <!-- Single column for Interest Remaining -->
            <th rowspan="2">RECEIPTS REMARK</th>
        </tr>
        <tr>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <!-- Add data rows here -->
    </tbody>
            </table>

        </div>
    </section>





<!-- script for picture selection  -->

    </div> 
      
<!-- record -->
<?php $page =="record"?>

    <div class="fill-form-div login-div"  id="next_2"> 
    <?php include "record-phone-div.php"?>
       
        <!-- <header class="fadeInDown animated">
            ?php include "recordheaderinner.php"?>
        </header> -->
      
        <script>
            function toggleMenu() {
            var x = document.getElementById("myLinks");
            if (x.style.display === "block") {
            x.style.display = "none";
            // y.style.paddingTop="50%";
            } 
            else {
            x.style.display = "block";
            // y.style.paddingTop="0%";
            }
            }
                </script>
            
    <section class="input-section">
    <h3 id="add-on">System Setting</h3>
        <div class="Rinner-div">
          <hr>
            <div class="option-div" id="option-div" onclick="show_account_setting()">
                <h4>Account Setting</h4>
                <div class="text-div">
                    Click to change your cooperative account Bio Data
                </div>
                
            </div>

            <div class="option-div" id="option-div" onclick="show_system_setting()">
                <h4>System Setting</h4>
                <div class="text-div">
                    Click to change your password
                </div>
                
            </div>

            <div class="account-setting-div" id="account-setting-div">
            <h4>Update your Bio-data</h4>
            <hr>
                <div class="setting-inner-div">
                    <h4>Update your passport</h4>
                    <form method="post" enctype="multipart/form-data">
                        <div class="image-div">
                            <div class="left-div">
                                <img src="<?php echo $website_url ?>/uploaded_files/profile_pix/1.jpg" id="my_passport" alt="profile picture"/>
                              
                            </div>
                            <div class="left-div right-div">
                                <button class="remove-btn" id="remove_btn" type="button" onclick="removeImage()" disabled><i class="bi-trash"></i> Remove</button>
                                <button class="remove-btn change-btn" type="button" id="change-btn" onclick="changeImage()"><i class="bi-upload"></i> Change Image</button>
                                <input type="file" id="file-input" style="display:none;" onchange="previewImage(event)" />
                            </div>
                        </div>
                        <script>
                                    var remove_btn = document.getElementById('remove_btn');
                                    remove_btn.style.cursor = "not-allowed";
                                    remove_btn.disabled = true; 
                                </script>

                    
                        <input class="input" type="hidden" value="" id="member_id"  />
                    </form>

                </div>
            </div>

            <div class="system-setting-div" id="system-setting-div">
            <h4>Change your password</h4>
            <hr>
                <div class="setting-inner-div">
                <form method="post" enctype="multipart/form-data">
                <form method="post" enctype="multipart/form-data">
                    <label class="input-label">Enter your old password</label><br>
                    <input class="input" type="password" id="old_password" placeholder="Kindly Enter your Old password" autocomplete="off"/><br><br>

                    <label class="input-label">Create New password</label><br>
                    <input class="input" type="password" id="new_password" placeholder="Kindly Create a new password" autocomplete="off" onkeyup="checkPasswordStrength();" /><br>
                    <small id="password-strength-info" style="color: red;"></small><br><br>

                    <label class="input-label">Confirm New password</label><br>
                    <input class="input" type="password" id="confirm_new_password" placeholder="Re-enter new password" autocomplete="off" onkeyup="checkPasswordMatch();" /><br>
                    <small id="password-match-info" style="color: red;"></small><br><br>

                    <div style="cursor: pointer;" class="eye" onclick="icon_toggle();"> 
                        <i class="bi-eye" id="togglePassword"></i> 
                        <i class="bi-eye-slash" id="togglePassword2" style="display:none;"></i>
                    </div>
                    <br><br>

                    <button class="bio-data-btn" type="button" onclick="update_password();">Update Password</button><br><br>
                </form>

                </div>
            </div>

       
           
        </div>
       
       
   
    </section>
     
    
</div>

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







