<?php

// ----------------------------------------
// Database Configuration and Constants
// ----------------------------------------

// Database Credentials 
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); // Leave blank if using default XAMPP/WAMP settings
define('DB_NAME', 'cams'); 

// Application Constants
define('ATTENDANCE_MIN_PERCENTAGE', 75); // The college's compliance threshold

// ----------------------------------------
// Database Connection (PDO)
// ----------------------------------------

// Attempt to connect to MySQL database
try {
    // Create a PDO connection object
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";port=3306;dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    
    // Set the PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start session management for login/logout functionality
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (PDOException $e) {
    // Kill the application if connection fails
    die("ERROR: Could not connect to the database. Check config.php settings. " . $e->getMessage());
}

// ----------------------------------------
// Global Security and Utility Functions
// ----------------------------------------

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

?>
