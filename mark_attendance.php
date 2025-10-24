<?php
require_once 'config.php';

// Check if the user is logged in and is a Teacher
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

// Get and validate input
$subject_code = filter_input(INPUT_GET, 'subject_code', FILTER_SANITIZE_STRING);
$session_date = filter_input(INPUT_GET, 'session_date', FILTER_SANITIZE_STRING);

if (empty($subject_code) || empty($session_date)) {
    redirect('teacher_dashboard.php');
}

$students = [];
$subject_name = "";
$error = "";
$class_filter = "";
$stream_filter = "";

try {
    // 1. Get subject name AND class/stream for filtering
    $sql_subject_info = "SELECT subject_name, class, stream FROM subjects WHERE subject_code = :code";
    $stmt_subject_info = $pdo->prepare($sql_subject_info);
    $stmt_subject_info->bindParam(':code', $subject_code);
    $stmt_subject_info->execute();
    $subject_info = $stmt_subject_info->fetch(PDO::FETCH_ASSOC);

    if (!$subject_info) {
        throw new Exception("Subject not found or not fully configured.");
    }
    $subject_name = $subject_info['subject_name'];
    $class_filter = $subject_info['class'];
    $stream_filter = $subject_info['stream'];
    
    // 2. Fetch students list using the class/stream filters (FIXED: Added WHERE clause)
    $sql_students = "SELECT s.student_id, u.roll_no, u.name, s.class, s.stream
                     FROM students s
                     JOIN users u ON s.roll_no = u.roll_no
                     WHERE s.class = :class_filter AND s.stream = :stream_filter
                     ORDER BY u.roll_no";
    $stmt_students = $pdo->prepare($sql_students);
    $stmt_students->bindParam(':class_filter', $class_filter);
    $stmt_students->bindParam(':stream_filter', $stream_filter);
    $stmt_students->execute();
    $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database Error: Could not load class list. SQLSTATE[" . $e->getCode() . "]: " . $e->getMessage();
} catch (Exception $e) {
    $error = "Application Error: " . $e->getMessage();
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
            <h1>Mark Attendance: <?php echo htmlspecialchars($subject_name); ?> (<?php echo htmlspecialchars($subject_code); ?>)</h1>
            <a href="teacher_dashboard.php" class="logout-link">← Back to Dashboard</a>
        </div>
        
        <h3>Session Date: <?php echo date('F j, Y', strtotime($session_date)); ?></h3>

        <?php if (!empty($error)) : ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($students)) : ?>
            <div class="alert-danger">No students found for this class. (Filtering by Class: <?php echo htmlspecialchars($class_filter); ?> / Stream: <?php echo htmlspecialchars($stream_filter); ?>)</div>
        <?php else : ?>
            <form action="save_attendance.php" method="POST">
                <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
                <input type="hidden" name="session_date" value="<?php echo htmlspecialchars($session_date); ?>">

                <table border="1" style="width:100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="padding: 10px;">Roll No</th>
                            <th style="padding: 10px;">Student Name</th>
                            <th style="padding: 10px;">Class</th>
                            <th style="padding: 10px;">Stream</th> <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student) : ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['class']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['stream']); ?></td> <td style="padding: 10px; text-align: center;">
                                    <label>
                                        <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="P" required checked> Present
                                    </label>
                                    <label style="margin-left: 20px;">
                                        <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="A" required> Absent
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px;">
                    <input type="submit" value="Save Attendance" class="btn">
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>