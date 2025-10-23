<?php
require_once "config.php";

// Ensure only teachers can access
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_no = trim($_POST["roll_no"]);
    $name = trim($_POST["name"]);
    $class = trim($_POST["class"]);
    $semester = trim($_POST["semester"]);
    $password = trim($_POST["password"]);

    if (empty($roll_no) || empty($name) || empty($class) || empty($semester) || empty($password)) {
        $message = "<div class='alert-danger'>⚠️ All fields are required!</div>";
    } else {
        try {
            // Check if roll_no already exists
            $check_sql = "SELECT roll_no FROM users WHERE roll_no = :roll_no";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->bindParam(":roll_no", $roll_no);
            $check_stmt->execute();

            if ($check_stmt->rowCount() > 0) {
                $message = "<div class='alert-danger'>⚠️ Student with this Roll Number already exists!</div>";
            } else {
                // Insert into users table
                $sql_user = "INSERT INTO users (roll_no, name, password, user_role)
                             VALUES (:roll_no, :name, :password, 'S')";
                $stmt_user = $pdo->prepare($sql_user);
                $stmt_user->bindParam(":roll_no", $roll_no);
                $stmt_user->bindParam(":name", $name);
                $stmt_user->bindParam(":password", $password);
                $stmt_user->execute();

                // Insert into students table
                $sql_student = "INSERT INTO students (roll_no, stream, semester)
                                VALUES (:roll_no, :stream, :semester)";
                $stmt_student = $pdo->prepare($sql_student);
                $stmt_student->bindParam(":roll_no", $roll_no);
                $stmt_student->bindParam(":stream", $class);
                $stmt_student->bindParam(":semester", $semester);
                $stmt_student->execute();

                $message = "<div class='alert-success'>✅ Student Added Successfully!</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert-danger'>Database Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Student</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        .form-container {
            width: 400px; margin: 60px auto; background: white;
            border-radius: 10px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-container h2 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="password"] {
            width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%; background-color: #007bff; color: white;
            padding: 10px; border: none; border-radius: 5px; margin-top: 15px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #0056b3; }
        .alert-danger {
            background-color: #f8d7da; color: #721c24;
            padding: 10px; border-radius: 5px; margin-bottom: 10px;
        }
        .alert-success {
            background-color: #d4edda; color: #155724;
            padding: 10px; border-radius: 5px; margin-bottom: 10px;
        }
        a.back-btn {
            display: inline-block; margin-top: 15px; text-decoration: none;
            color: #007bff; text-align: center; width: 100%;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Student</h2>

        <?php echo $message; ?>

        <form action="add_student.php" method="POST">
            <label>Roll Number:</label>
            <input type="text" name="roll_no" required>

            <label>Student Name:</label>
            <input type="text" name="name" required>

            <label>Class:</label>
            <input type="text" name="class" required>

            <label>Semester:</label>
            <input type="number" name="semester" min="1" max="8" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Add Student">
        </form>

        <a href="teacher_dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
</body>
</html>
