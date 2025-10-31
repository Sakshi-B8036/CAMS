<?php
// If already logged in, redirect based on role
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    switch ($_SESSION["user_role"]) {
        case 'A':
            header("location: admin_dashboard.php");
            break;
        case 'T':
            header("location: teacher_dashboard.php");
            break;
        case 'S':
            header("location: student_dashboard.php");
            break;
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CAMS</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            width: 400px;
            backdrop-filter: blur(6px);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        p {
            font-size: 1em;
            margin-bottom: 25px;
            opacity: 0.9;
        }
        .btn {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 12px;
            background: white;
            color: #007bff;
            font-size: 1em;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
            color: white;
            transform: scale(1.05);
        }
        .footer {
            margin-top: 20px;
            font-size: 0.85em;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CAMS Portal</h1>
        <p>Welcome to College Attendance Management System</p>

        <!-- Role-based login buttons -->
        <a href="login.php?role=A" class="btn">👑 Admin Login</a>
        <a href="login.php?role=T" class="btn">👨‍🏫 Teacher Login</a>
        <a href="login.php?role=S" class="btn">🎓 Student Login</a>

        <div class="footer">
            <p>© 2025 CAMS | All Rights Reserved</p>
        </div>
    </div>
</body>
</html>
