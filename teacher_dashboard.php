<?php
// Initialize the session
session_start();

// Check if the user is logged in and is a Teacher, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_role"] !== 'T'){
    header("location: login.php");
    exit;
}
?>
<?php 
// Include the configuration file (which handles the PDO connection and session start) 
require_once 'config.php'; 
// Check if the user is logged in and is a Teacher ('T') 
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') { 
redirect('login.php'); 
} 
$teacher_roll = $_SESSION["roll_no"]; 
$teacher_name = $_SESSION["name"]; 
$subjects = []; 
try { 
// 1. Get the teacher's internal ID 
$sql_teacher_id = "SELECT teacher_id FROM teachers WHERE roll_no = :roll"; 
$stmt_teacher_id = $pdo->prepare($sql_teacher_id); 
$stmt_teacher_id->bindParam(':roll', $teacher_roll); 
$stmt_teacher_id->execute(); 
$teacher_id = $stmt_teacher_id->fetchColumn(); 
if ($teacher_id) { 
// 2. Get all subjects assigned to this teacher 
$sql_subjects = "SELECT subject_code, subject_name FROM subjects WHERE teacher_id = :teacher_id"; 
$stmt_subjects = $pdo->prepare($sql_subjects); 
        $stmt_subjects->bindParam(':teacher_id', $teacher_id); 
        $stmt_subjects->execute(); 
        $subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC); 
    } 
} catch (PDOException $e) { 
    // Basic error display for development 
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
</head> 
<body> 
    <div class="dashboard-wrapper"> 
        <div class="header-nav"> 
            <h1>Welcome, <?php echo htmlspecialchars($teacher_name); ?> (<?php echo 
htmlspecialchars($teacher_roll); ?>)</h1> 
            <a href="logout.php" class="logout-link">Logout</a> 
        </div> 
         
        <h2>Mark Attendance</h2> 
 
        <?php if (!empty($error)) : ?> 
            <div class="alert-danger"><?php echo $error; ?></div> 
        <?php endif; ?> 
 
        <?php if (empty($subjects)) : ?> 
            <div class="alert-danger">You are not currently assigned any subjects.</div> 
        <?php else : ?> 
             
            <form action="mark_attendance.php" method="GET" class="subject-select-form"> 
                <label for="subject_code">Select Subject:</label> 
                <select name="subject_code" id="subject_code" required> 
                    <option value="">-- Choose a Subject --</option> 
                    <?php foreach ($subjects as $subject) : ?> 
                        <option value="<?php echo htmlspecialchars($subject['subject_code']); ?>"> 
                            <?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo 
htmlspecialchars($subject['subject_code']); ?>) 
                        </option> 
                    <?php endforeach; ?> 
                </select> 
                <input type="date" name="session_date" value="<?php echo date('Y-m-d'); ?>" required> 
                <input type="submit" value="Load Class List" class="btn"> 
            </form> 
 
        <?php endif; ?> 
 
        </div> 
        <hr>
<!-- Add New Student Button -->
<div style="margin-top: 30px; text-align: center;">
    <a href="add_student.php" class="btn" style="text-decoration:none; padding:10px 20px; background-color:#007bff; color:white; border-radius:5px;">
        ➕ Add New Student
    </a>
</div>

</body> 
</html>