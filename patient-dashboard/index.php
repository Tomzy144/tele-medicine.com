<?php include '../config/config.php' ?>
<!-- ?php include '../config/member-session-validation.php';?> -->
<!-- ?php include '../../temidire-base-api/connection/function.php'; ?> -->



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php include 'meta.php' ?>
    <title><?php echo $thename ?> | Patient Dashboard</title>
    <meta name="keywords" content="Patient - <?php echo $thename ?>" />
    <meta name="description" content="Patient Dashboard <?php echo $thename ?>" />

    <script>

       
        var sessionId = sessionStorage.getItem('session_id');
      
        if (!sessionId) {
            sessionStorage.clear();
            window.location.href = "../";
        }

        var sessionTimeout = 25 * 60 * 1000;
        var sessionTimeoutHandler;

        function resetSessionTimer() {
            clearTimeout(sessionTimeoutHandler);
            sessionTimeoutHandler = setTimeout(function() {
                sessionStorage.clear();
                alert("Session expired. Redirecting to login page...");
                window.location.href = "../";
            }, sessionTimeout);
        }

        

        window.onload = function() {
            get_patient_details(sessionId);
            // fetch_all_entries(sessionId);
            resetSessionTimer();

          
           
       
           
          document.getElementById('patient_id').textContent = sessionId;
            document.addEventListener('mousemove', resetSessionTimer);
            document.addEventListener('keypress', resetSessionTimer);
        };

      
    </script>



</head>
<body>
    <?php include 'alert.php'; ?>
    <?php include 'config/page-content.php'; ?>

    <header>
       
        <!-- Include header -->
        <?php include "headerinner.php" ?>
        <div class="logo-div">
            <div class="text-div"><h2>TELE-MEDICINE</h2></div>
            <div class="list-div">
                <ul>
                    <li>
                        <p  id="first" class="first active" onclick="_next_page('next_1'),highlite2('first'), remove_heading(), fetch_user() ;">
                            <i class="bi-speedometer"></i> Dashboard
                        </p>
                    </li>
                     <li>
                        <p id="second" class="second" onclick="_next_page('next_2'), highlite2('second'), remove_heading(), fetch_all_doctors();">
                            <i class="bi-people"></i> Doctors List
                        </p>
                    </li>
                    <li>
                        <p id="third" class="third" onclick="_next_page('next_3'), restore_div(), highlite2('third');">
                            <i class="bi-person-gear"></i> Setting
                        </p>
                    </li>

                    <li id="fourth" onclick="logout(),highlite2('fourth');"><p><i class="bi-power"></i> Log-Out</p></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="fill-form-div" id="more-info">
        <?php $page = 'dashboard'; ?>
        <?php include 'config/page-content.php'; ?>
    </div>

    <script>
        // Initialize AOS (if using)
        AOS.init({
            easing: 'ease-in-out-sine'
        });

        // Push state to the history stack and reload page on back navigation
        if (window.history && window.history.pushState) {
            window.history.pushState('forward', null);
            window.onpopstate = function () {
                location.reload();
            };
        }
    </script>



    <!-- Include bottom scripts -->
    <?php include 'bottom-scripts.php' ?>
</body>
</html>
