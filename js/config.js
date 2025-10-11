var rootUrl = "";
var endPoint = "";
var activity_endPoint = "";

// Auto-detect environment (localhost vs Render)
if (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1") {
    // Local development setup
    endPoint = "http://localhost/tele-medicine-base-api/";
    activity_endPoint = "http://localhost/tele-medicine-base-api/config/activity.php";
    rootUrl = "http://localhost/tele-medicine.com/";
} else {
    // Render or production setup
    endPoint = "https://tele-medicine-base-api.onrender.com/";
    activity_endPoint = "https://tele-medicine-base-api.onrender.com/config/activity.php";
    rootUrl = "https://tele-medicine-base-api.onrender.com/";
}
