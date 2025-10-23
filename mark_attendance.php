<?php 
// Include the configuration file 
require_once 'config.php'; 

// Check if the user is logged in and is a Teacher ('T') 
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') { 
    redirect('login.php'); 
} 
// ---------------------------------------- 
// 1. Get and Validate Input 
// ---------------------------------------- 
$subject_code = filter_input(INPUT_GET, 'subject_code', FILTER_SANITIZE_STRING); 
$session_date = filter_input(INPUT_GET, 'session_date', FILTER_SANITIZE_STRING); 
if (empty($subject_code) || empty($session_date)) { 
    // If input is missing, redirect back to the dashboard 
    redirect('teacher_dashboard.php'); 
} 
// ---------------------------------------- 
// 2. Fetch Subject Class/Stream and Filter Students 
// ---------------------------------------- 
$students = []; 
$subject_name = ""; 
$error = ""; 

try { 
    // Step 1: Get subject name, class, AND stream 
    $sql_subject_info = "SELECT subject_name, class, stream FROM subjects WHERE subject_code = :code"; 
    $stmt_subject_info = $pdo->prepare($sql_subject_info); 
    $stmt_subject_info->bindParam(':code', $subject_code); 
    $stmt_subject_info->execute(); 
    $subject_info = $stmt_subject_info->fetch(PDO::FETCH_ASSOC);

    if ($subject_info) {
        $subject_name = $subject_info['subject_name'];
        $class_to_filter = $subject_info['class'];
        $stream_to_filter = $subject_info['stream'];
    } else {
        $error = "Subject not found or class/stream not assigned.";
        throw new Exception("Subject data error."); 
    }

    // Step 2: Fetch list of students filtered by BOTH class and stream
// ... around line 43
    $sql_students = "SELECT s.student_id, u.roll_no, u.name 
                     FROM students s 
                     JOIN users u ON s.roll_no = u.roll_no 
                     WHERE s.class = :class_to_filter AND s.stream = :stream_to_filter 
                     ORDER BY u.roll_no"; 
    $stmt_students = $pdo->prepare($sql_students); 
    $stmt_students->bindParam(':class_to_filter', $class_to_filter); 
    // Line 47: CHECK FOR MISSING SEMICOLON HERE
    $stmt_students->bindParam(':stream_to_filter', $stream_to_filter); // <--- MUST end with a semicolon
    
    $stmt_students->execute(); // Line 48: The reported error line
    $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC); 
// ...

} catch (PDOException $e) { 
    $error = "Database Error: Could not load class list. " . $e->getMessage(); 
} catch (Exception $e) {
    if (empty($error)) $error = $e->getMessage();
}
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Mark Attendance</title> 
    <link rel="stylesheet" href="style.css"> 
</head> 
<body> 
    <div class="dashboard-wrapper"> 
        <div class="header-nav"> 
            <h1>Mark Attendance: <?php echo htmlspecialchars($subject_name); ?> (<?php echo 
htmlspecialchars($subject_code); ?>)</h1> 
            <a href="teacher_dashboard.php" class="logout-link">← Back to Dashboard</a> 
        </div> 
        
        <h3>Session Date: <?php echo date('F j, Y', strtotime($session_date)); ?></h3> 

        <?php if (!empty($error)) : ?> 
            <div class="alert-danger"><?php echo $error; ?></div> 
        <?php endif; ?> 

        <?php if (empty($students)) : ?> 
            <div class="alert-danger">No students found for this class.</div> 
        <?php else : ?> 

            <form action="save_attendance.php" method="POST"> 
                <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>"> 
                <input type="hidden" name="session_date" value="<?php echo htmlspecialchars($session_date); ?>"> 

                <table class="attendance-table"> 
                    <thead> 
                        <tr> 
                            <th>Roll No</th> 
                            <th>Student Name</th> 
                            <th>Status</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php foreach ($students as $student) : ?> 
                            <tr> 
                                <td><?php echo htmlspecialchars($student['roll_no']); ?></td> 
                                <td><?php echo htmlspecialchars($student['name']); ?></td> 
                                <td class="attendance-status-cell"> 
                                    <label> 
                                        <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="P" 
required checked> Present 
                                    </label> 
                                    <label style="margin-left: 20px;"> 
                                        <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="A" 
required> Absent 
                                    </label> 
                                </td> 
                            </tr> 
                        <?php endforeach; ?> 
                    </tbody> 
                </table> 
                <div class="attendance-submit-container"> 
                    <input type="submit" value="Save Attendance" class="btn"> 
                </div> 
            </form> 
        <?php endif; ?> 
    </div> 
</body> 
</html>