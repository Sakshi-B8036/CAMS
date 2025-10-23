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

try {
    // Get subject name for display
    $sql_subject_name = "SELECT subject_name FROM subjects WHERE subject_code = :code";
    $stmt_subject_name = $pdo->prepare($sql_subject_name);
    $stmt_subject_name->bindParam(':code', $subject_code);
    $stmt_subject_name->execute();
    $subject_name = $stmt_subject_name->fetchColumn();

    // ✅ Fetch students list correctly
    // Our students table: students (student_id, roll_no, class, semester)
    // Join with users to get name
    $sql_students = "SELECT s.student_id, u.roll_no, u.name, s.class, s.semester
                     FROM students s
                     JOIN users u ON s.roll_no = u.roll_no
                     ORDER BY u.roll_no";
    $stmt_students = $pdo->prepare($sql_students);
    $stmt_students->execute();
    $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database Error: Could not load class list. " . $e->getMessage();
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
            <div class="alert-danger">No students found for this class.</div>
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
                            <th style="padding: 10px;">Semester</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student) : ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['class']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($student['semester']); ?></td>
                                <td style="padding: 10px; text-align: center;">
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
