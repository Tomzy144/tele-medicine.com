<?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
    
     $thename='Tele-Medicine'; 
    $page = basename($_SERVER['SCRIPT_NAME']);
    $website_auto_url =(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   

    // Detect environment
    $environment = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) ? 'local' : 'production';

    // Handle CORS properly
    if ($environment === 'local') {
        header("Access-Control-Allow-Origin: http://localhost/tele-medicine.com");
    } else {
        header("Access-Control-Allow-Origin: https://tele-medicine-base-api.onrender.com");
    }

    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

   
    /////////////////////////////////////////////////////////////////


    // Database Configuration
    $environment = ($_SERVER['HTTP_HOST'] === 'localhost') ? 'local' : 'production';

    if ($environment === 'local') {
        // $HOST_NAME = 'localhost';
        // $SERVER_USERNAME = 'root';
        // $SERVER_PASSWORD = '';
        // $DATABASE_NAME = 'hms_db';
        $website_url = 'http://localhost/tele-medicine.com/';
        $website_redirect_url = 'http://localhost/';
    } else {
        $HOST_NAME = 'mysql-hospital-management-system.alwaysdata.net';
        // $SERVER_USERNAME = '410215';
        // $SERVER_PASSWORD = 'Tomzzzyy';
        // $DATABASE_NAME = 'hospital-management-system_telemedicine';
        $website_url = 'https://tele-medicine.onrender.com/';
        $website_redirect_url = 'https://tele-medicine.onrender.com/';
    }

    // Create Connection To Database
    // $conn = mysqli_connect($HOST_NAME, $SERVER_USERNAME, $SERVER_PASSWORD) or die("connection error");
    // mysqli_select_db($conn, $DATABASE_NAME);
?>
