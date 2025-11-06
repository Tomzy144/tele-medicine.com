var rootUrl = "";
var endPoint = "";


// Auto-detect environment
if (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1") {
    // Local development
    endPoint = "http://localhost/tele-medicine-base-api/";
   
    rootUrl = "http://localhost/tele-medicine.com/";
} else {
    // Production
    endPoint = "https://tele-medicine-base-api.onrender.com/";
    //endPoint = "https://tele-medicine-base-api.onrender.com/";
    
    rootUrl = "https://tele-medicine.onrender.com/"; 
}
