<?php
// Initialize the session
session_start();

// Check if the user is logged in and is a Teacher, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_role"] !== 'T'){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
</head>
<body>
    <h1>Welcome, Teacher <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>
    <p>This is the placeholder Teacher Dashboard. Your roll number is: <b><?php echo htmlspecialchars($_SESSION["roll_no"]); ?></b></p>
    <p>Member 3 will build the Teacher features here.</p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>