<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}
$admin_name = $_SESSION["name"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | CAMS</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: linear-gradient(to right, #74617c, #3498db);
    color: white;
}
.container {
    max-width: 900px;
    margin: 40px auto;
    text-align: center;
}
h1 { color: #455462ff; }
.dashboard-btn {
    display: block;
    background: rgba(0,0,0,0.3);
    padding: 15px;
    margin: 15px auto;
    width: 60%;
    border-radius: 8px;
    font-size: 18px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}
.dashboard-btn:hover {
    background: #1abc9c;
    transform: translateY(-3px);
}
.logout {
    margin-top: 30px;
    display: inline-block;
    padding: 10px 20px;
    background: #e74c3c;
    border-radius: 6px;
    color: white;
    text-decoration: none;
}
.logout:hover { background: #c0392b; }
</style>
</head>
<body>

<div class="container">
    <h1>Welcome Admin, <?php echo $admin_name; ?></h1>
    <p>Select an action below:</p>

    <a class="dashboard-btn" href="add_student.php">➕ Add Student</a>
    <a class="dashboard-btn" href="add_teacher.php">👨‍🏫 Add Teacher</a>
    <a class="dashboard-btn" href="manage_subjects.php">📘 Manage Subjects</a>
    <a class="dashboard-btn" href="view_summary.php">📊 View Attendance Summary</a>
    <a class="dashboard-btn" href="view_messages.php">📩 View Contact Messages</a>

    <a class="logout" href="logout.php">Logout</a>
</div>

</body>
</html>
