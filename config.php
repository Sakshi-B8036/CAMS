<?php
// ----------------------------------------
// Database Configuration
// !! CHANGE these values to match your local XAMPP/WAMP settings !!
// ----------------------------------------
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default for XAMPP/WAMP
define('DB_PASSWORD', '');     // Default for XAMPP/WAMP (often blank)
define('DB_NAME', 'cams_project');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect to database. " . $conn->connect_error);
}

// ----------------------------------------
// Global System Constants
// ----------------------------------------
// Define the 75% attendance threshold as a global constant
define('ATTENDANCE_MIN_PERCENTAGE', 75);

// Start the session (required for storing login data)
session_start();
?>