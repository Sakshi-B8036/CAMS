<?php
require_once 'config.php';

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

            if ($user['user_role'] === 'A') redirect('admin_dashboard.php');
            if ($user['user_role'] === 'T') redirect('teacher_dashboard.php');
            if ($user['user_role'] === 'S') redirect('student_dashboard.php');
        } else {
            $err = "Invalid roll number or password.";
        }
    }
}
?>
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
        </form>

        <a href="index.php" class="footer-link">← Back to Home</a>
    </div>

</body>
</html>