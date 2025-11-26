<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'A') redirect('login.php');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll = trim($_POST['roll_no']);
    $name = trim($_POST['student_name']);
    $class = trim($_POST['class']);
    $semester = trim($_POST['semester']);
    $password = trim($_POST['password']);

    if ($roll===''||$name===''||$class===''||$semester===''||$password==='') {
        $error = 'All fields required.';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (roll_no,name,password,user_role) VALUES (:roll,:name,:pwd,'S')");
            $stmt->execute([':roll'=>$roll, ':name'=>$name, ':pwd'=>$password]);

            $stmt2 = $pdo->prepare("INSERT INTO students (roll_no, class, semester) VALUES (:roll,:class,:sem)");
            $stmt2->execute([':roll'=>$roll, ':class'=>$class, ':sem'=>$semester]);

            $pdo->commit();
            $success = "Student added.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "DB error: " . $e->getMessage();
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
