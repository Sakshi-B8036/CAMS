<?php
session_start();
require_once 'config.php'; 

// Ensure the user is a logged-in teacher
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

$teacher_roll = $_SESSION["roll_no"]; 
$teacher_name = $_SESSION["name"]; 
$subjects = []; 
$error = "";

try {
    // 1️⃣ Get teacher_id using the teacher's roll number
    $sql_teacher = "SELECT teacher_id FROM teachers WHERE roll_no = :roll_no";
    $stmt_teacher = $pdo->prepare($sql_teacher);
    $stmt_teacher->bindParam(':roll_no', $teacher_roll, PDO::PARAM_STR);
    $stmt_teacher->execute();
    $teacher_id = $stmt_teacher->fetchColumn();

    if ($teacher_id) {
        // 2️⃣ Fetch subjects assigned to this teacher
        $sql_subjects = "SELECT subject_code, subject_name FROM subjects WHERE teacher_id = :teacher_id";
        $stmt_subjects = $pdo->prepare($sql_subjects);
        $stmt_subjects->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
        $stmt_subjects->execute();
        $subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = "Teacher not found in the system. Please contact admin.";
    }

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style> /* Basic styling for demonstration/readability */
        .dashboard-wrapper { max-width: 800px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; text-align: center; }
        .alert-danger { color: red; margin-bottom: 15px; }
        .btn { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="header-nav">
            <h1>Welcome, <?php echo htmlspecialchars($teacher_name); ?> (<?php echo htmlspecialchars($teacher_roll); ?>)</h1>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
        
        <h2>Mark Attendance</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($subjects)) : ?>
            <div class="alert-danger">No subjects are available in the system.</div>
        <?php else : ?>
            <form action="mark_attendance.php" method="GET" class="subject-select-form">
                <label for="subject_code">Select Subject:</label>
                <select name="subject_code" id="subject_code" required>
                    <option value="">-- Choose a Subject --</option>
                    <?php foreach ($subjects as $subject) : ?>
                        <option value="<?php echo htmlspecialchars($subject['subject_code']); ?>">
                            <?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo htmlspecialchars($subject['subject_code']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="session_date" value="<?php echo date('Y-m-d'); ?>" required>
                <input type="submit" value="Load Class List" class="btn">
            </form>
        <?php endif; ?>

        <hr>
        <div style="margin-top: 30px; text-align: center; display: flex; justify-content: center; gap: 20px;">
            <a href="add_student.php" class="btn" style="text-decoration:none; padding:10px 20px; background-color:#007bff; color:white; border-radius:5px;">
                ➕ Add New Student
            </a>
            
            <a href="add_subject.php" class="btn" style="text-decoration:none; padding:10px 20px; background-color:#28a745; color:white; border-radius:5px;">
                📚 Add New Subject
            </a>
            </div>
    </div>
</body>
</html>