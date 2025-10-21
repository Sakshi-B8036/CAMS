<?php
// Initialize the session
session_start();

// Check if the user is logged in and is a Student, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_role"] !== 'S'){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome, Student <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>
    <p>This is the placeholder Student Dashboard. Your roll number is: <b><?php echo htmlspecialchars($_SESSION["roll_no"]); ?></b></p>
    <p>Member 2 will build the Student features here.</p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>