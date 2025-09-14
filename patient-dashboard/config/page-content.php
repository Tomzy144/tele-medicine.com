
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
        <div class="profile-info-div">

        </div>
        <div class="activities-div">
            
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







