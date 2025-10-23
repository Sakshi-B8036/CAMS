<?php
// Initialize the session
session_start();
require_once 'config.php';

// Check if the user is logged in and is a Student ('S')
if (!isLoggedIn() || $_SESSION["user_role"] !== 'S') {
    redirect('login.php');
}

$student_roll = $_SESSION["roll_no"];
$student_name = $_SESSION["name"];
$attendance_records = [];
$total_sessions = 0;
$present_count = 0;
$error = "";

try {
    // 1. Get the student's internal ID (student_id)
    $sql_student_id = "SELECT student_id FROM students WHERE roll_no = :roll";
    $stmt_student_id = $pdo->prepare($sql_student_id);
    $stmt_student_id->bindParam(':roll', $student_roll);
    $stmt_student_id->execute();
    $student_id = $stmt_student_id->fetchColumn();

    if ($student_id) {
        // 2. Get all attendance records for this student, joining with subjects for subject name
        $sql_attendance = "
            SELECT 
                a.session_date, 
                a.subject_code, 
                a.status,
                s.subject_name
            FROM attendance a
            JOIN subjects s ON a.subject_code = s.subject_code
            WHERE a.student_id = :student_id
            ORDER BY a.session_date DESC, a.subject_code ASC";

        $stmt_attendance = $pdo->prepare($sql_attendance);
        $stmt_attendance->bindParam(':student_id', $student_id);
        $stmt_attendance->execute();
        $attendance_records = $stmt_attendance->fetchAll(PDO::FETCH_ASSOC);

        // 3. Calculate Attendance Summary
        $total_sessions = count($attendance_records);
        $present_count = array_reduce($attendance_records, function($carry, $item) {
            return $carry + ($item['status'] === 'P' ? 1 : 0);
        }, 0);

    } else {
        $error = "Student ID not found in the database.";
    }

} catch (PDOException $e) {
    $error = "Database Error: Could not load attendance history. " . $e->getMessage();
}

// Calculate percentage for display
$attendance_percentage = ($total_sessions > 0) ? round(($present_count / $total_sessions) * 100, 2) : 0;
$percentage_class = $attendance_percentage >= 75 ? 'text-success' : 'text-danger';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles for the dashboard summary (Can be moved to style.css) */
        .summary-box {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .summary-item {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 30%;
        }
        .summary-item h3 {
            margin-top: 0;
            color: #555;
            font-size: 1.1em;
        }
        .summary-item .value {
            font-size: 2.5em;
            font-weight: bold;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="header-nav">
            <h1>Welcome, <?php echo htmlspecialchars($student_name); ?> (<?php echo htmlspecialchars($student_roll); ?>)</h1>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>

        <h2>Attendance Summary</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="summary-box">
            <div class="summary-item">
                <h3>Total Sessions Marked</h3>
                <div class="value"><?php echo $total_sessions; ?></div>
            </div>
            <div class="summary-item">
                <h3>Sessions Present</h3>
                <div class="value text-success"><?php echo $present_count; ?></div>
            </div>
            <div class="summary-item">
                <h3>Attendance Percentage</h3>
                <div class="value <?php echo $percentage_class; ?>"><?php echo $attendance_percentage; ?>%</div>
            </div>
        </div>

        <hr>

        <h2>Detailed Attendance History</h2>

        <?php if (empty($attendance_records)) : ?>
            <div class="alert-danger">No attendance records found yet.</div>
        <?php else : ?>
            <table class="attendance-table"> <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record) : ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($record['session_date'])); ?></td>
                            <td><?php echo htmlspecialchars($record['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($record['subject_name']); ?></td>
                            <td class="attendance-status-cell">
                                <?php 
                                    $status = htmlspecialchars($record['status']);
                                    $status_display = ($status === 'P') ? 'Present' : 'Absent';
                                    $status_class = ($status === 'P') ? 'text-success' : 'text-danger';
                                ?>
                                <strong class="<?php echo $status_class; ?>"><?php echo $status_display; ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>