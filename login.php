<?php
<<<<<<< HEAD
// Include the database connection file
require_once "config.php";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_GET['role'] ?? '';
$role_name = $role === 'A' ? 'Admin' : ($role === 'T' ? 'Teacher' : ($role === 'S' ? 'Student' : 'User'));
=======
require_once 'config.php';
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37

$role_param = $_GET['role'] ?? '';

$roll_no = $password = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll_no = trim($_POST['roll_no']);
    $password = trim($_POST['password']);

    if ($roll_no === '' || $password === '') {
        $err = "Please enter credentials.";
    } else {
        $sql = "SELECT id, roll_no, name, password, user_role FROM users WHERE roll_no = :roll LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':roll' => $roll_no]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $password === $user['password']) { // plain-text check
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['roll_no'] = $user['roll_no'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_role'] = $user['user_role'];

<<<<<<< HEAD
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($roll_no_err) && empty($password_err)) {
        $sql = "SELECT roll_no, password, user_role, name FROM users WHERE roll_no = :roll_no";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":roll_no", $roll_no, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Check if roll_no exists
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $db_password = $row["password"]; // Stored PLAIN TEXT password
                    $user_role = $row["user_role"];
                    $name = $row["name"];

                    // Comparison for PLAIN TEXT password
                    if ($password === $db_password) {
                        // Password is correct, start session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["roll_no"] = $roll_no;
                        $_SESSION["user_role"] = $user_role;
                        $_SESSION["name"] = $name;

                        // Redirect based on role
                        if ($user_role === 'T') {
                            header("location: teacher_dashboard.php");
                        } elseif ($user_role === 'S') {
                            header("location: student_dashboard.php");
                        } else {
                            header("location: admin_dashboard.php");
                        }
                        exit;
                    } else {
                        $login_err = "Invalid roll number or password.";
                    }
                } else {
                    $login_err = "Invalid roll number or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
=======
            if ($user['user_role'] === 'A') redirect('admin_dashboard.php');
            if ($user['user_role'] === 'T') redirect('teacher_dashboard.php');
            if ($user['user_role'] === 'S') redirect('student_dashboard.php');
        } else {
            $err = "Invalid roll number or password.";
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
        }
    }
}
?>
<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAMS Login</title>
    <style>
        /* Base Styles and Light Background Gradient */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Vibrant, light background */
            background: linear-gradient(to top right, #83c4f5ff 0%, #ffffffff 100%); 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
            /* CRITICAL: Allows absolute positioning of the back button relative to the viewport */
            position: relative; 
        }
        
        /* New Back Button Style */
        .back-button {
            /* Positioned at the top-left */
            position: absolute;
            top: 30px;
            right: 30px;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
            padding: 10px 15px;
            border: 1px solid #007bff;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .back-button:hover {
            background-color: #e9f5ff;
            box-shadow: 0 4px 8px rgba(0, 50, 100, 0.15);
        }

        /* Wrapper/Card Style */
        .wrapper {
            width: 100%;
            max-width: 400px; 
            padding: 40px; 
            border-radius: 16px; 
            background: #d0e0f8ff; 
            /* Subtle but noticeable shadow */
            box-shadow: 0 10px 30px rgba(0, 50, 100, 0.1); 
            text-align: center;
            /* Animated entry */
            transform: translateY(20px); 
            opacity: 0;
            animation: fadeInSlide 0.6s ease-out forwards;
        }

        /* Card Animation Keyframes */
        @keyframes fadeInSlide {
            to { transform: translateY(0); opacity: 1; }
        }

        /* Header and Title */
        .logo-header {
            font-size: 42px;
            font-weight: 800;
            color: #007bff; /* College Blue */
            margin-bottom: 0px;
            letter-spacing: -1px;
        }
        .role-subtitle {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
            font-weight: 400;
        }

        /* Input Group */
        .input-group {
            position: relative;
            margin-top: 20px;
        }
        .input-group label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 45px; /* Aligns with input padding */
            color: #007bff; /* Icon color matches brand color */
            font-style: normal; 
            font-size: 18px;
        }

        /* Input Fields */
        input[type="text"], input[type="password"] {
            width: 100%; 
            padding: 15px 15px 15px 50px; /* Space for the icon */
            border: 1px solid #ddd; 
            border-radius: 10px; 
            box-sizing: border-box;
            font-size: 16px;
            background: #fcfcfc;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        /* Error Spans */
        .error-span {
            display: block;
            color: #dc3545; 
            font-size: 12px;
            text-align: left;
            margin-top: 5px;
        }

        /* Submit Button */
        input[type="submit"] {
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 15px 15px;
            border-radius: 10px; 
            cursor: pointer; 
            width: 100%;
            font-size: 18px;
            font-weight: 700;
            margin-top: 35px;
            transition: background 0.3s ease, transform 0.1s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        input[type="submit"]:hover { 
            background: #0056b3; 
            transform: translateY(-2px); /* Lift button slightly on hover */
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        }

        /* Alert/Error Message */
        .alert-danger {
            color: #721c24; 
            background-color: #f8d7da;
            border: 1px solid #f5c6cb; 
            padding: 12px; 
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    
    <a href="index.php" class="back-button">
        <span>&#8592;</span> Back to Index
    </a>

    <div class="wrapper">
        <h1 class="logo-header">CAMS</h1>
        <p class="role-subtitle">Attendance Management System - <?php echo htmlspecialchars($role_name); ?> Login</p>
        
        <?php
        if (!empty($login_err)) {
            echo '<div class="alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div class="input-group">
                <label for="roll_no">Roll Number</label>
                <i>&#127380;</i> <input type="text" id="roll_no" name="roll_no" value="<?php echo htmlspecialchars($roll_no); ?>" placeholder="Enter your Roll No">
                <span class="error-span"><?php echo $roll_no_err; ?></span>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <i>&#128274;</i> <input type="password" id="password" name="password" placeholder="Enter your Password">
                <span class="error-span"><?php echo $password_err; ?></span>
            </div>
            
            <div>
                <input type="submit" value="LOG IN">
            </div>
=======
<!-- HTML form (same as earlier) -->
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title>
  <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #74617c, #3498db);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            width: 380px;
            padding: 25px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 15px;
            color: #fff;
            font-weight: 600;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            margin: 12px 0;
            border-radius: 5px;
            border: none;
            outline: none;
            font-size: 15px;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            border: none;
            background: #1abc9c;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        .btn-login:hover {
            background: #17a085;
            transform: translateY(-2px);
        }

        .alert {
            background: rgba(255, 0, 0, 0.15);
            color: #ffbaba;
            border: 1px solid rgba(255, 0, 0, 0.4);
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 6px;
            font-size: 14px;
        }

        .footer-link {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #e3e3e3;
            text-decoration: none;
        }

        .footer-link:hover {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
    <div class="login-box">
        <h2>Login to CAMS</h2>

        <?php if (!empty($login_err)) : ?>
            <div class="alert"><?php echo $login_err; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="roll_no" placeholder="Enter Roll Number" class="input-field" required>
            <input type="password" name="password" placeholder="Enter Password" class="input-field" required>
            <button type="submit" class="btn-login">Login</button>
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
        </form>

        <a href="index.php" class="footer-link">← Back to Home</a>
    </div>

</body>
</html>