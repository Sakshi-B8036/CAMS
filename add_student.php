<?php

session_start();
require_once 'config.php';

// Check if the user is logged in and is a Teacher (or Admin, depending on your system)
=======
require_once 'config.php';

// Check if user is logged in and is a Teacher
>>>>>>> 768bfefd44e34f28e4e60eeccdefe07eb0d671f6
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

$error = "";
$success = "";

// ----------------------------------------
// 1. Process Form Submission
// ----------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate input
    $roll_no = filter_input(INPUT_POST, 'roll_no', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_STRING);
    $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
    // CRITICAL FIX: Mapping 'semester' input (form field) to 'stream' (DB column)
    $stream = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_STRING); 
    $password = $_POST['password']; 

    if (empty($roll_no) || empty($name) || empty($class) || empty($stream) || empty($password)) {
        $error = "Error: All fields are required.";

    // Sanitize input
    $roll_no = filter_input(INPUT_POST, 'roll_no', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_STRING);
    $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
    $stream = filter_input(INPUT_POST, 'stream', FILTER_SANITIZE_STRING);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (empty($roll_no) || empty($name) || empty($class) || empty($stream) || empty($semester) || empty($password)) {
        $error = "⚠️ All fields are required.";

    } else {
        // NOTE: For a production application, always use password_hash() here!
        $insert_password = $password; 

        try {

            // Start a database transaction to ensure both inserts succeed or fail together
            $pdo->beginTransaction();

            // 2. Insert into the USERS table (for student login)
            $sql_user = "INSERT INTO users (roll_no, name, password, user_role) 
                         VALUES (:roll, :name, :password, 'S')";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->bindParam(':roll', $roll_no);
            $stmt_user->bindParam(':name', $name);
            $stmt_user->bindParam(':password', $insert_password);
            $stmt_user->execute();

            // 3. Insert into the STUDENTS table (for class filtering by teachers)
            $sql_student = "INSERT INTO students (roll_no, class, stream) 
                            VALUES (:roll, :class, :stream_val)";
            $stmt_student = $pdo->prepare($sql_student);
            $stmt_student->bindParam(':roll', $roll_no);
            $stmt_student->bindParam(':class', $class);
            $stmt_student->bindParam(':stream_val', $stream); 
            $stmt_student->execute();

            $pdo->commit();
            
            $success = "Student **$roll_no ($name)** added successfully.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            
            // Handle duplicate roll number error (Error Code 23000)
            if ($e->getCode() === '23000') {
                 $error = "Error: Roll Number **$roll_no** already exists in the system.";
            } else {
                 $error = "Database error: Could not add student. " . $e->getMessage();

            $pdo->beginTransaction();

            // 1️⃣ Add to users table
            $sql_user = "INSERT INTO users (roll_no, name, password, user_role)
                         VALUES (:roll, :name, :password, 'S')";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([
                ':roll' => $roll_no,
                ':name' => $name,
                ':password' => $password   
            ]);

            // 2️⃣ Add to students table
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

    <link rel="stylesheet" href="style.css"> 
    <style> /* Basic styling for demonstration/readability */
        .container { max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; text-align: center; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; }
        .alert-success { color: green; margin-bottom: 15px; }
        .alert-danger { color: red; margin-bottom: 15px; }
        .btn { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: #0056b3; }
        .alert-success { color: green; margin-bottom: 10px; }
        .alert-danger { color: red; margin-bottom: 10px; }

    </style>
</head>
<body>
    <div class="container">

        <div class="header-nav">
            <h2>Add New Student</h2>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="add_student.php" method="POST"> 
            
            <label for="roll_no">Roll Number:</label>
            <input type="text" id="roll_no" name="roll_no" required>
            
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name" required>
            
            <label for="class">Class:</label>
            <input type="text" id="class" name="class" required>
            
            <label for="semester">Stream/Semester (Maps to DB 'stream'):</label>
            <input type="text" id="semester" name="semester" required> 
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="btn">Add Student</button>
            
            <a href="teacher_dashboard.php" style="display: block; margin-top: 15px;">← Back to Dashboard</a>
        </form>

        <h2>Add New Student</h2>

        <?php if (!empty($error)) echo "<div class='alert-danger'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert-success'>$success</div>"; ?>

        <form action="" method="POST">
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
            <input type="password" name="password" value="12345" required>

            <button type="submit">Add Student</button>
        </form>

        <p style="margin-top: 15px;"><a href="teacher_dashboard.php">← Back to Dashboard</a></p>

    </div>
</body>
</html>