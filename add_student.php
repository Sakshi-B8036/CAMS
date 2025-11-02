<?php
session_start();
require_once 'config.php';

// Check if the user is logged in and is a Teacher
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

$error = "";
$success = "";

// ----------------------------------------
// Handle Form Submission
// ----------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $roll_no  = filter_input(INPUT_POST, 'roll_no', FILTER_SANITIZE_STRING);
    $name     = filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_STRING);
    $class    = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
    $stream   = filter_input(INPUT_POST, 'stream', FILTER_SANITIZE_STRING);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (empty($roll_no) || empty($name) || empty($class) || empty($stream) || empty($semester) || empty($password)) {
        $error = "⚠️ All fields are required.";
    } else {
        // NOTE: For production use password_hash()
        $insert_password = $password;

        try {
            // Start a transaction
            $pdo->beginTransaction();

            // 1️⃣ Insert into USERS table
            $sql_user = "INSERT INTO users (roll_no, name, password, user_role)
                         VALUES (:roll, :name, :password, 'S')";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([
                ':roll' => $roll_no,
                ':name' => $name,
                ':password' => $insert_password
            ]);

            // 2️⃣ Insert into STUDENTS table
            $sql_student = "INSERT INTO students (roll_no, class, stream, semester)
                            VALUES (:roll, :class, :stream, :semester)";
            $stmt_student = $pdo->prepare($sql_student);
            $stmt_student->execute([
                ':roll' => $roll_no,
                ':class' => $class,
                ':stream' => $stream,
                ':semester' => $semester
            ]);

            $pdo->commit();
            $success = "✅ Student <b>$roll_no ($name)</b> added successfully.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') {
                $error = "⚠️ Roll Number <b>$roll_no</b> already exists.";
            } else {
                $error = "❌ Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: #0056b3; }
        .alert-success { color: green; margin-bottom: 10px; }
        .alert-danger { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Student</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Roll Number</label>
            <input type="text" name="roll_no" required>

            <label>Student Name</label>
            <input type="text" name="student_name" required>

            <label>Class</label>
            <input type="text" name="class" required>

            <label>Stream</label>
            <input type="text" name="stream" required>

            <label>Semester</label>
            <input type="text" name="semester" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Add Student</button>
        </form>

        <p style="margin-top: 15px;">
            <a href="admin_dashboard.php">← Back to Dashboard</a>
        </p>
    </div>
</body>
</html>
