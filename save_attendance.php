<?php
require_once 'config.php';

// Check if the user is logged in and is a Teacher
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

// Validate POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $subject_code = $_POST['subject_code'] ?? '';
    $session_date = $_POST['session_date'] ?? '';
    $attendance_data = $_POST['attendance'] ?? [];

    if (empty($subject_code) || empty($session_date) || empty($attendance_data)) {
        die("Error: Missing required attendance data.");
    }

    try {
        $pdo->beginTransaction();

        // Prepare SQL insert
        $sql = "INSERT INTO attendance (student_id, subject_code, session_date, status)
                VALUES (:student_id, :subject_code, :session_date, :status)
                ON DUPLICATE KEY UPDATE status = VALUES(status)";

        $stmt = $pdo->prepare($sql);

        foreach ($attendance_data as $student_id => $status) {
            $stmt->execute([
                ':student_id' => $student_id,
                ':subject_code' => $subject_code,
                ':session_date' => $session_date,
                ':status' => $status
            ]);
        }

        $pdo->commit();

        echo "<div style='text-align:center; padding:20px;'>
                <h2>✅ Attendance Saved Successfully!</h2>
                <a href='teacher_dashboard.php' style='color:#007bff; text-decoration:none;'>← Back to Dashboard</a>
              </div>";

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Database Error: " . $e->getMessage());
    }
} else {
    redirect('teacher_dashboard.php');
}
?>
