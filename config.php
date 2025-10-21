<?php
// --- CAMS: Database Connection and Configuration (config.php) ---

// Database Credentials (Member 1: CHECK THESE AGAINST YOUR LOCAL SETUP)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); // Leave blank if using default XAMPP/WAMP settings
define('DB_NAME', 'cams_db'); 

// Application Constants
define('ATTENDANCE_MIN_PERCENTAGE', 75); // The college's compliance threshold

// Attempt to connect to MySQL database
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start session management for login/logout functionality
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (PDOException $e) {
    die("ERROR: Could not connect to the database. Check config.php settings. " . $e->getMessage());
}

// Security and Utility Functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}
?>