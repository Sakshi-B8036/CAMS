<?php

require_once 'config.php';



// Check if the user is logged in and is a Teacher/Admin (assuming T can add students)

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

    // CRITICAL FIX: The form uses 'semester', but the DB schema uses 'stream'. 

    $stream = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_STRING); 

    $password = $_POST['password']; 



    if (empty($roll_no) || empty($name) || empty($class) || empty($stream) || empty($password)) {

        $error = "Error: All fields are required.";

    } else {

        // Use the plaintext password for consistency with the rest of your system

        $insert_password = $password; 



        try {

            $pdo->beginTransaction();



            // 2. Insert into the USERS table (for login)

            $sql_user = "INSERT INTO users (roll_no, name, password, user_role) 

                         VALUES (:roll, :name, :password, 'S')";

            $stmt_user = $pdo->prepare($sql_user);

            $stmt_user->bindParam(':roll', $roll_no);

            $stmt_user->bindParam(':name', $name);

            $stmt_user->bindParam(':password', $insert_password);

            $stmt_user->execute();



            // 3. Insert into the STUDENTS table (for class filtering)

            $sql_student = "INSERT INTO students (roll_no, class, stream) 

                            VALUES (:roll, :class, :stream_val)";

            $stmt_student = $pdo->prepare($sql_student);

            $stmt_student->bindParam(':roll', $roll_no);

            $stmt_student->bindParam(':class', $class);

            $stmt_student->bindParam(':stream_val', $stream); // Mapping 'semester' input to 'stream' column

            $stmt_student->execute();



            $pdo->commit();

            

            $success = "Student **$roll_no ($name)** added successfully.";

            // Optionally redirect after success: redirect('teacher_dashboard.php');



        } catch (PDOException $e) {

            $pdo->rollBack();

            

            // Handle duplicate roll number error (Error Code 23000 is common for duplicates)

            if ($e->getCode() === '23000') {

                 $error = "Error: Roll Number **$roll_no** already exists in the system.";

            } else {

                 $error = "Database error: Could not add student. " . $e->getMessage();

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

            

            <label for="semester">Stream/Semester:</label>

            <input type="text" id="semester" name="semester" required> 

            

            <label for="password">Password:</label>

            <input type="password" id="password" name="password" required>

            

            <button type="submit" class="btn">Add Student</button>

            

            <a href="teacher_dashboard.php" style="display: block; margin-top: 15px;">← Back to Dashboard</a>

        </form>

    </div>

</body>

</html>
