
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
    var action = 'get_member_details'; 
    var member_id = sessionId;
    var dataString = {
        action: action,
        member_id: member_id,
    };

    $.ajax({
        type: "POST",
        url: endPoint, // Ensure endPoint is correctly defined
        dataType: "json", // Expect JSON response
        data: dataString, // Data being sent
        cache: false, // Disable cache for security reasons
        success: function (response) {
            if (response.success) {
               
                // Extract member details from the response
                var memberDetails = response.data; // Now it's directly the object

                // Get the member details
                var memberName = memberDetails.member_name; 
                var member_id = memberDetails.member_id; 
                var member_passport = memberDetails.member_passport; // The file name of the passport
                var member_phone_number = memberDetails.member_phone_number;
                var member_bvn = memberDetails.member_bvn;
                var occupation = memberDetails.occupation;
                var date_of_birth = memberDetails.date_of_birth;
                var marital_status = memberDetails.marital_status;
                var member_nin = memberDetails.member_nin;
                var member_address = memberDetails.member_address;
                var member_gender = memberDetails.member_gender;
                var member_email = memberDetails.member_email;

                // Displaying the member's name in the HTML
                document.getElementById('name').textContent = memberName; // Update the span with the member name
                document.getElementById('member_id').value = member_id;

                // Set the passport image source instead of value
                var passportImage = document.getElementById('my_passport');
                var passportImage2 = document.getElementById('my_passport2');
                var passportImage3 = document.getElementById('my_passport3');
                if (member_passport) {
                    passportImage.src = '../uploaded_files/profile_pix/' + member_passport; // Set the actual passport image URL
                    passportImage2.src = '../uploaded_files/profile_pix/' + member_passport;
                    passportImage3.src = '../uploaded_files/profile_pix/' + member_passport;
                } else {
                    passportImage.src = '../uploaded_files/profile_pix/1.jpg'; // Set the default image if no passport is available
                    
                }

                // Populate other fields with member details
                // document.getElementById('phone_number').value = member_phone_number;
                // document.getElementById('bvn').value = member_bvn;
                // document.getElementById('occupation').value = occupation;
                // document.getElementById('member_email').value = member_email;
                // document.getElementById('date_of_birth').value = date_of_birth;
                // document.getElementById('marital_status').value = marital_status;
                // document.getElementById('address').value = member_address;
                // document.getElementById('gender').value = member_gender;
                // document.getElementById('file_name_display').value = member_nin;
                //document.getElementById('NIN').value = member_nin;
               
               
               

                // You can also log the member details if needed
                // console.log('Member Details:', memberDetails); // Log the full details for debugging
                pop_notification();
            } else {
                console.log('Failed to fetch member details:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching member details:', status, error);
        }
    });
}


function logout() {
    sessionStorage.clear();
    alert("Logging OUT...")
    window.location.href = "../login";
   

 
}




function fetch_all_entries(session_id) {
    var action = 'fetch_all_entries'; 
    var member_id = session_id;
    var dataString = { action: action, member_id: member_id };

    $.ajax({
        type: "POST",
        url: endPoint, // Ensure endPoint is correctly defined
        dataType: "json", // Expect JSON response
        data: dataString, // Data being sent
        cache: false, // Disable cache for security reasons
        success: function (response) {
            if (response.success) {
                var entries = response.data;
                var table = document.getElementById('table');

                // Clear initial content of the table except the header rows
                $("#table tr:gt(1)").remove();

                var runningBalance = {
                    shares: 0,
                    savings: 0,
                    loans: 0,
                    deposits: 0
                };

                // Loop through the entries to populate the table
                entries.forEach(function (entry) {
                    var row = table.insertRow();

                    // Insert data into cells
                    row.insertCell(0).textContent = new Date(entry.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
                    row.insertCell(1).textContent = entry.payment_type || 'xxxxxxx';

                    // Shares
                    var sharesDebit = parseFloat(entry.shares_debit) || 0;
                    var sharesCredit = parseFloat(entry.shares_credit) || 0;
                    runningBalance.shares += sharesCredit - sharesDebit;
                    row.insertCell(2).textContent = sharesDebit || 'xxxxxxx';
                    row.insertCell(3).textContent = sharesCredit || 'xxxxxxx';
                    row.insertCell(4).textContent = runningBalance.shares.toFixed(2);

                    // Savings
                    var savingsDebit = parseFloat(entry.savings_debit) || 0;
                    var savingsCredit = parseFloat(entry.savings_credit) || 0;
                    runningBalance.savings += savingsCredit - savingsDebit;
                    row.insertCell(5).textContent = savingsDebit || 'xxxxxxx';
                    row.insertCell(6).textContent = savingsCredit || 'xxxxxxx';
                    row.insertCell(7).textContent = runningBalance.savings.toFixed(2);



                     // Loans data
                    const loansDebit = parseFloat(entry.loans_debit) || 0;
                    const loansCredit = parseFloat(entry.loans_credit) || 0;

                    // Calculate running balance and ensure it is positive
                    runningBalance.loans += loansCredit - loansDebit;
                    var runningBalance1 = Math.abs(runningBalance.loans);
                    // Insert values into table cells
                    row.insertCell(8).textContent = loansDebit.toFixed(2) || 'xxxxxxx';
                    row.insertCell(9).textContent = loansCredit.toFixed(2) || 'xxxxxxx';
                    row.insertCell(10).textContent = runningBalance1.toFixed(2);




                    // Loans
                    // var loansDebit = parseFloat(entry.loans_debit) || 0;
                    // var loansCredit = parseFloat(entry.loans_credit) || 0;
                    // runningBalance.loans += loansCredit - loansDebit;
                    // row.insertCell(8).textContent = loansDebit || 'xxxxxxx';
                    // row.insertCell(9).textContent = loansCredit || 'xxxxxxx';
                    // row.insertCell(10).textContent = runningBalance.loans.toFixed(2);

                    // Deposits
                    var depositsDebit = parseFloat(entry.deposits_debit) || 0;
                    var depositsCredit = parseFloat(entry.deposits_credit) || 0;
                    runningBalance.deposits += depositsCredit - depositsDebit;
                    row.insertCell(11).textContent = depositsDebit || 'xxxxxxx';
                    row.insertCell(12).textContent = depositsCredit || 'xxxxxxx';
                    row.insertCell(13).textContent = runningBalance.deposits.toFixed(2);

                    // Interest Remaining
                    row.insertCell(14).textContent = entry.interest_remaining || 'N/A';

                    // Receipts Remark
                    row.insertCell(15).textContent = entry.remark || 'N/A';
                });

                // Display search box after loading entries
                document.getElementById('search_box').style.display = "block";
                // Display a notification once after entries load
            } else {
                console.log('Failed to fetch entries:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching entries:', status, error);
        }
    });
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
    var option_divs = document.querySelectorAll('.option-div'); // Get all option-div elements
    var account_setting_div = document.getElementById('account-setting-div'); // Get the account-setting-div element
    var add_on = document.getElementById('add-on'); // Get the add-on element (heading)
    var system_setting_div = document.getElementById('system-setting-div'); // Get the account-setting-div element
    var search_div = document.getElementById('search_box');

    // Loop through option divs and restore visibility
    option_divs.forEach(function(option_div) {
        if (option_div.style.display === "none") {
            option_div.style.display = "block";  // Restore option divs
            account_setting_div.style.display = "none"; // Hide account-setting div
            system_setting_div.style.display = "none"; // Hide account-setting div
           
        }
    });
        search_div.style.display="none";
   

    // Reset the heading text
    add_on.innerHTML = "Setting";  // Reset text to original
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
    }
    else{
        // toggle.add='none';
        password.type = 'password';
       cpassword.type = 'password';
       toggle.style.display="block";
        toggle2.style.display="none";
    }
    
  }
  

  // Function triggered when "Change Image" button is clicked
function changeImage() {
    // Trigger the hidden file input when the change button is clicked
    document.getElementById('file-input').click();
}

// Function triggered when the file input changes (i.e., when a file is selected)
function previewImage(event) {
    var file = event.target.files[0];

    if (file) {
        var reader = new FileReader();

        // When the file is loaded, update the image preview
        reader.onload = function(e) {
            document.getElementById('my_passport').src = e.target.result;
            document.getElementById('my_passport2').src = e.target.result;

            // Enable the remove button after an image is selected
            var remove_btn = document.getElementById('remove_btn');
            remove_btn.disabled = false;
            remove_btn.style.cursor = "pointer";
        };

        // Read the file as a data URL (base64 encoded)
        reader.readAsDataURL(file);

        // Send the image to the server
        uploadImageToServer(file);
    }
}
function uploadImageToServer(file) {
    var member_id = $('#member_id').val();
    var formData = new FormData();
    // var action = "move_passport";
    formData.append('member_id', member_id);
    // formData.append('action', action);

    formData.append('passport', file);
    

    $.ajax({
        url:  '../config/upload_passport.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(success) {
            // Ensure response is parsed as JSON
            if (success) {
                $('#success-div').html('<div><i class="bi-check"></i></div> '+ success.message)
                    .fadeIn(500).delay(5000).fadeOut(100);
            } else {
                // Handle failure
                $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + success.message);
                $('#warning-div').fadeIn(500).delay(5000).fadeOut(100); // Optional: fade out warning after some time
            }
        },
    });
}


// Function triggered when "Remove" button is clicked
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

  
  

/////// accept alphabeth ///////
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


  function isPhoneNumberValid() {
    const phoneNumber = document.getElementById('phone_number').value.trim();
    
    // If the input is empty, hide the error message and reset the border
    if (phoneNumber === "") {
        document.getElementById('duration_info').style.display = 'none';
        document.getElementById('phone_number').style.border = "rgba(0, 0, 0, .1) 1px solid";
        return; // Exit the function since no further validation is needed for an empty input
    }
    
    // Check if the input contains only digits
    const isNumeric = /^\d+$/.test(phoneNumber);
    
    // Check if the phone number starts with one of the allowed prefixes
    const startsWithValidPrefix = phoneNumber.startsWith('090') || 
                                  phoneNumber.startsWith('081') || 
                                  phoneNumber.startsWith('080') || 
                                  phoneNumber.startsWith('091') || 
                                  phoneNumber.startsWith('070');
    
    // Check if the phone number is 11 digits long, numeric, and has a valid prefix
    if (phoneNumber.length === 11 && isNumeric && startsWithValidPrefix) {
        // If valid, hide the error message and reset the border
        document.getElementById('duration_info').style.display = 'none';
        document.getElementById('phone_number').style.border = "rgba(0, 0, 0, .1) 1px solid";
    } else {
        // If invalid, show the error message and highlight the input field
        document.getElementById('duration_info').style.display = 'block';
        document.getElementById('phone_number').style.border = "rgb(245, 142, 58) 1px solid";
    }
}


function isBVNValid(){
    const bvn = document.getElementById('bvn').value.trim();
    
    // If the input is empty, hide the error message and reset the border
    if (bvn === "") {
        document.getElementById('BVN_info').style.display = 'none';
        document.getElementById('bvn').style.border = "rgba(0, 0, 0, .1) 1px solid";
        return; // Exit the function since no further validation is needed for an empty input
    }
    
    // Check if the input contains only digits
    const isNumeric = /^\d+$/.test(bvn);
    
    // Check if the phone number is 11 digits long, numeric, and has a valid prefix
    if (bvn.length === 11 && isNumeric) {
        // If valid, hide the error message and reset the border
        document.getElementById('BVN_info').style.display = 'none';
        document.getElementById('bvn').style.border = "rgba(0, 0, 0, .1) 1px solid";
    } else {
        // If invalid, show the error message and highlight the input field
        document.getElementById('BVN_info').style.display = 'block';
        document.getElementById('bvn').style.border = "rgb(245, 142, 58) 1px solid";
    }

}
function showSelectedFileName() {
    const ninInput = document.getElementById('NIN');
    const fileNameDisplay = document.getElementById('file_name_display');

    // Check if a file has been selected
    if (ninInput.files && ninInput.files.length > 0) {
        const fileName = ninInput.files[0].name; // Get the name of the selected file
        fileNameDisplay.value = fileName; // Display the file name in the text input
        uploadNinToServer(ninInput.files[0]); // Pass the actual file
    }
}

function uploadNinToServer(file) {
    var formData = new FormData();
    formData.append('NIN', file); // Append the file to the FormData

    $.ajax({
        url: '../config/upload_nin.php', // Change to your server-side script
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log('Image uploaded successfully:', response);
        },
        error: function(xhr, status, error) {
            console.error('Image upload failed:', status, error);
        }
    });
}


 

function validateDOB() {
    const dobInput = document.getElementById('date_of_birth').value;
    
    // Check if a date has been selected
    if (!dobInput) {
        return; // If no date is selected, skip the validation
    }

    // Convert the input date from yyyy-mm-dd to a Date object
    const dob = new Date(dobInput);

    // Get today's date and format it
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Set today's date to midnight

    // Format the input date to dd/mm/yyyy
    const formattedDob = dob.getDate().toString().padStart(2, '0') + '/' + 
                         (dob.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                         dob.getFullYear();

    // Format today's date to dd/mm/yyyy
    const formattedToday = today.getDate().toString().padStart(2, '0') + '/' + 
                           (today.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                           today.getFullYear();
    
    // Check if the selected date is today or a future date
    if (dob >= today) {
        // If invalid, show the error message and highlight the input field
        document.getElementById('dob_info').innerHTML = `Date of birth cannot be today (${formattedToday}) or a future date.`;
        document.getElementById('dob_info').style.display = 'block';
        document.getElementById('date_of_birth').style.border = "rgb(245, 142, 58) 1px solid";
    } else {
        // If valid, hide the error message and reset the border
        document.getElementById('dob_info').style.display = 'none';
        document.getElementById('date_of_birth').style.border = "rgba(0, 0, 0, .1) 1px solid";
    }
}

function validateEmail() {
    var emailInput = document.getElementById('member_email');
    var emailWarning = document.getElementById('email-warning');
    var email = emailInput.value;
    
    // Regular expression for basic email validation
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email && !emailPattern.test(email)) {
        emailWarning.textContent = 'Invalid email format.';
    } else {
        emailWarning.textContent = ''; // Clear warning if valid
    }
}

// function submit_biodata() {
//     // var phone_number = $('#phone_number').val();
//     // var bvn = $('#bvn').val();
//     // var address = $('#address').val();
//     // var gender = $('#gender').val();
//     // var occupation = $('#occupation').val();
//     // var date_of_birth = $('#date_of_birth').val();
//     // var marital_status = $('#marital_status').val();
//     var member_id = $('#member_id').val();
//     // var member_email = $('#member_email').val();
//     // var nin = $('#NIN').prop('files')[0]; // File input for NIN
//     var passport = $('#file-input').prop('files')[0]; // File input for passport
    
//     var action = 'update_biodata';

//     // // Validate phone number is not empty
//     // if (phone_number === "") {
//     //     $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + "Phone Number can't be empty")
//     //         .fadeIn(500).delay(5000).fadeOut(100);
//     //     return; // Exit the function if validation fails
//     // }

//     // Create a FormData object to hold the data
//     var formData = new FormData();
//     formData.append('action', action);
//     // formData.append('phone_number', phone_number);
//     // formData.append('bvn', bvn);
//     // formData.append('address', address);
//     // formData.append('gender', gender);
//     // formData.append('occupation', occupation);
//     // formData.append('date_of_birth', date_of_birth);
//     // formData.append('marital_status', marital_status);
//     formData.append('member_id', member_id);
//     // formData.append('member_email', member_email);

//     // // Append files only if new files are selected
//     // if (nin) {
//     //     formData.append('nin', nin);
//     // } else {
//     //     formData.append('nin', "");  // Send an empty string if no new NIN file is selected
//     // }

//     if (passport) {
//         formData.append('passport', passport);
//     } else {
//         formData.append('passport', "");  // Send an empty string if no new passport file is selected
//     }

//     // Store the original button text
//     var btn_text = $('#bio_data_btn').html();

//     // Update button text and disable the button to prevent multiple requests
//     $('#bio_data_btn').html('Authenticating...');
//     document.getElementById('bio_data_btn').disabled = true;

//     $.ajax({
//         type: "POST",
//         url: endPoint, // Ensure endPoint is correctly defined
//         dataType: "json", // Expect JSON response
//         data: formData, // Use the FormData object
//         processData: false, // Prevent jQuery from processing the data
//         contentType: false, // Prevent jQuery from setting content type
//         cache: false,
//         success: function (response) {
//             // Check for response success
//             if (response.success === true) {
//                 $('#success-div').html('<div><i class="bi-check"></i></div> PASSPORT UPDATED SUCCESSFULLY!')
//                     .fadeIn(500).delay(5000).fadeOut(100);
//                 setTimeout(function() {
//                     window.location.reload();
//                 }, 3000);
//             } else {
//                 // Handle failure
//                 $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> ' + response.message1 + " " + response.message2)
//                     .fadeIn(500).delay(5000).fadeOut(100);
//             }

//             // Re-enable the submit button and restore its text
//             $('#bio_data_btn').html(btn_text);
//             document.getElementById('bio_data_btn').disabled = false;
//         },
//         error: function (xhr, status, error) {
//             // Handle any AJAX errors
//             console.error('AJAX Error:', status, error);
//             $('#warning-div').html('<div><i class="bi-exclamation-triangle"></i></div> An error occurred. Please try again.')
//                 .fadeIn(500).delay(5000).fadeOut(100);

//             // Re-enable the submit button and restore its text
//             $('#bio_data_btn').html(btn_text);
//             document.getElementById('bio_data_btn').disabled = false;
//         }
//     });
// }






function checkPasswordStrength() {
    const password = document.getElementById('new_password').value;
    const strengthInfo = document.getElementById('password-strength-info');
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%\^&\*])[A-Za-z\d!@#\$%\^&\*]{8,}$/;

    if (password === '') {
        strengthInfo.textContent = '';
    } else if (strongPasswordRegex.test(password)) {
        strengthInfo.textContent = 'Strong password';
        strengthInfo.style.color = 'white';
    } else {
        strengthInfo.textContent = 'Password must be at least 8 characters long, include an uppercase letter, a number, and a symbol.';
        strengthInfo.style.color = 'orange';
    }
}

function checkPasswordMatch() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_new_password').value;
    const matchInfo = document.getElementById('password-match-info');

    if (confirmPassword === '') {
        matchInfo.textContent = '';
    } else if (password === confirmPassword) {
        matchInfo.textContent = 'Passwords match';
        matchInfo.style.color = 'white';
    } else {
        matchInfo.textContent = 'Passwords do not match';
        matchInfo.style.color = 'orange';
    }
}

function icon_toggle() {
    const passwordField = document.getElementById('new_password');
    const iconShow = document.getElementById('togglePassword');
    const iconHide = document.getElementById('togglePassword2');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        iconShow.style.display = 'none';
        iconHide.style.display = 'inline';
    } else {
        passwordField.type = 'password';
        iconShow.style.display = 'inline';
        iconHide.style.display = 'none';
    }
}

  
function update_password() {
    var old_password = $('#old_password').val();
    var new_password = $('#new_password').val();
    var confirm_new_password = $('#confirm_new_password').val();
    var action = "update_password";
    var member_id = $('#member_id').val();

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
        member_id: member_id,
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
















    