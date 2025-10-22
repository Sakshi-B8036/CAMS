<?php
// Include the database connection file
require_once "config.php";

// Initialize variables
$roll_no = $password = "";
$roll_no_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if roll_no is empty
    if (empty(trim($_POST["roll_no"]))) {
        $roll_no_err = "Please enter roll number.";
    } else {
        $roll_no = trim($_POST["roll_no"]);
    }

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
                    $db_password = $row["password"];
                    $user_role = $row["user_role"];
                    $name = $row["name"];

                    // NOTE: Currently plain-text comparison (for testing only)
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
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CAMS Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font: 14px sans-serif; background-color: #f9f9f9; }
        .wrapper {
            width: 350px; padding: 20px; margin: 80px auto;
            border: 1px solid #ccc; border-radius: 10px; background: #fff;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
        }
        .alert-danger {
            color: #721c24; background-color: #f8d7da;
            border-color: #f5c6cb; padding: 10px; border-radius: 5px;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;
        }
        input[type="submit"] {
            background: #007bff; color: white; border: none; padding: 8px 15px;
            border-radius: 5px; cursor: pointer;
        }
        input[type="submit"]:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>CAMS Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Roll Number</label>
                <input type="text" name="roll_no" value="<?php echo htmlspecialchars($roll_no); ?>">
                <span><?php echo $roll_no_err; ?></span>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password">
                <span><?php echo $password_err; ?></span>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
