<?php
session_start();
require_once 'config.php';

// Check if the user is a teacher and submitted data
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect('login.php');
}

// 1. Get required data
$subject_code = filter_input(INPUT_POST, 'subject_code', FILTER_SANITIZE_STRING);
$session_date = filter_input(INPUT_POST, 'session_date', FILTER_SANITIZE_STRING);
$attendance_data = $_POST['status'] ?? []; // Array of [roll_no => status (P/A)]

// Check for missing data 
if (empty($subject_code) || empty($session_date) || empty($attendance_data)) {
    $_SESSION['error'] = "Error: Missing required attendance data.";
    redirect('mark_attendance.php?subject_code=' . $subject_code . '&session_date=' . $session_date);
}

$success_count = 0;
$error_count = 0;

try {
    // Start a transaction for safe multi-record insertion
    $pdo->beginTransaction();

    // Prepare statement to fetch student_id (needed for attendance table)
    $sql_get_id = "SELECT student_id FROM students WHERE roll_no = :roll";
    $stmt_get_id = $pdo->prepare($sql_get_id);

    // Prepare the insertion statement using student_id
    $sql_insert = "INSERT INTO attendance (student_id, subject_code, session_date, status) 
                   VALUES (:s_id, :subject, :session_date, :status)";
    $stmt_insert = $pdo->prepare($sql_insert);

    foreach ($attendance_data as $roll_no => $status) {
        $roll_no = filter_var($roll_no, FILTER_SANITIZE_STRING);
        $status = filter_var($status, FILTER_SANITIZE_STRING);

        // 2. Fetch the student_id for the current roll_no
        $stmt_get_id->bindParam(':roll', $roll_no);
        $stmt_get_id->execute();
        $student_id = $stmt_get_id->fetchColumn();

        if ($student_id) {
            // 3. Insert the record using the fetched student_id
            $stmt_insert->bindParam(':s_id', $student_id);
            $stmt_insert->bindParam(':subject', $subject_code);
            $stmt_insert->bindParam(':session_date', $session_date);
            $stmt_insert->bindParam(':status', $status);
            
            $stmt_insert->execute();
            $success_count++;
        } else {
            // Log if a roll number could not be mapped to a student_id
            $error_count++;
            // Note: In a real system, you'd log the $roll_no that failed.
        }
    }

    $pdo->commit();
    $_SESSION['success'] = "Attendance for **$subject_code** on $session_date saved successfully for $success_count students. ($error_count failed to map)";

} catch (PDOException $e) {
    $pdo->rollBack();
    // Temporarily uncomment the die() line if saving fails to see the exact error code
     die("ATTENDANCE SAVE FAILED. ERROR: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
    
    $_SESSION['error'] = "Database Error: Could not save attendance. Please check subject_code integrity. " . $e->getMessage();
}

redirect('teacher_dashboard.php');
?>