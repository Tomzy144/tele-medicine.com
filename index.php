<?php include 'config/config.php'?>

<?php
if($s_staff_id!=''){
?>
    <script>
    window.parent(location="a/");
    </script>
<?php }?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http: //www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include 'meta.php'?>
<title><?php echo $thename?>  | Administrative Login</title>
<meta name="keywords" content="Admin - <?php echo $thename?>" />
<meta name="description" content="Administrative Login <?php echo $thename?>"/>
</head>

<body>
<?php include 'alert.php'?>
<?php include 'portal.php'?>
<!-- <i class="bi-arrow-left"  onclick="back();" style="color:blue;cursor:pointer; float:left;font-size:70px; margin:5%;"></i> -->
<script>
    function back(){
    window.parent(location="../");
    }
</script>

    <div class="advert-div fadeIn animated" >
       
       
         <div class="inner-div">
            <h2>TELE-MEDICINE</h2>
            <div class="text-div">
                <p>About </p>
                <p>Tele-Medicine is a system of healthcare delivery that utilizes telecommunications technology to provide medical services 
                    and information remotely. It enables patients to consult with healthcare professionals, receive diagnoses, and access treatment
                    options without the need for in-person visits. Tele-Medicine can include video consultations, remote monitoring of vital signs,
                    and the exchange of medical data and images. This approach enhances access to healthcare, particularly for individuals in
                    remote or underserved areas, and can improve the efficiency and convenience of medical care.
                </p>
                <div class="doctor-prompt fadeIn animated" id="doctor_prompt" >
                    <h3>Our Other Product:</h3>
                    <h2>Hospital Management System</h2>
                    <p>The Hospital Management System (HMS) is a comprehensive software solution designed to streamline and automate various
                        administrative and clinical processes within a hospital or healthcare facility. It encompasses a wide range of functionalities,
                        including patient registration, appointment scheduling, electronic medical records (EMR), billing and invoicing, inventory 
                        management, and reporting. By integrating these processes into a single platform, the HMS aims to improve operational 
                        efficiency, enhance patient care, and facilitate better communication among healthcare providers.
                    </p>
                    <p>Link👉🏽:     <a href="<?php echo $website_redirect_url?>/hospital-management-system" target="_blank">Hospital Management System</a></p>

                </div>
                

            </div>
          
         </div>
           
        
    </div>
    


    <div class="right-div " data-aos="fade-right" data-aos-duration="1000" >
        <div class="div-in">
            <div class="fill-form-div" id="more-info">
                <?php $page='login';?>
                <?php include 'config/page-content.php';?>
            </div>
        </div>
    </div>
 

<script>
        superplaceholder({el: reset_password_email,
            sentences: ['Enter Your Email', 'e.g telemedicine@gmail.com','e.g teleMedicineSupport@gmail.com'],
            options: {
            letterDelay: 80,
            loop: true,
            startOnFocus: false
        }
    });
    </script>
<?php include 'bottom-scripts.php'?>
</body>
</html>