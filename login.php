<?php
// Include the database connection file
require_once "config.php";

// Initialize variables
$roll_no = $password = "";
$roll_no_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if roll_no is empty
    if(empty(trim($_POST["roll_no"]))){
        $roll_no_err = "Please enter roll number.";
    } else{
        $roll_no = trim($_POST["roll_no"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($roll_no_err) && empty($password_err)){
        $sql = "SELECT roll_no, password, user_role, name FROM users WHERE roll_no = ?";

        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_roll_no);
            $param_roll_no = $roll_no;

            if($stmt->execute()){
                $stmt->store_result();

                // Check if roll_no exists, if yes then verify password
                if($stmt->num_rows == 1){
                    $stmt->bind_result($roll_no, $hashed_password, $user_role, $name);
                    if($stmt->fetch()){
                        // NOTE: For now, we are using plain text passwords for simplicity (bad practice!)
                        if($password === $hashed_password){
                            // Password is correct, start a new session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["roll_no"] = $roll_no;
                            $_SESSION["user_role"] = $user_role;
                            $_SESSION["name"] = $name;

                            // Redirect user based on role
                            if($user_role === 'T') {
                                header("location: teacher_dashboard.php");
                            } else {
                                header("location: student_dashboard.php");
                            }
                            exit;
                        } else{
                            // Password is not valid
                            $login_err = "Invalid roll number or password.";
                        }
                    }
                } else{
                    // roll_no doesn't exist
                    $login_err = "Invalid roll number or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CAMS Login</title>
    <link rel="stylesheet" href="style.css"> <style>
        /* Basic inline styling to make the form visible before Member 2 starts */
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; margin: 0 auto; border: 1px solid #ccc; margin-top: 50px;}
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>CAMS Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if(!empty($login_err)){
            echo '<div class="alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Roll Number</label>
                <input type="text" name="roll_no" value="<?php echo $roll_no; ?>">
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