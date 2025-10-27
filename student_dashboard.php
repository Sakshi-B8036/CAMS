<?php
session_start();
require_once 'config.php';

// Ensure the user is logged in and is a Student ('S')
if (!isLoggedIn() || $_SESSION["user_role"] !== 'S') {
    redirect('login.php');
}

$student_roll = $_SESSION["roll_no"];
$student_name = $_SESSION["name"];
$error = "";

// Initialize data arrays to avoid 'Undefined variable' warnings
$total_sessions = 0;
$sessions_present = 0;
$attendance_percentage = "0.0";
$history_data = [];

try {
    // 1. CRITICAL: Get the student_id from the students table using the roll_no
    $sql_get_id = "SELECT student_id FROM students WHERE roll_no = :roll";
    $stmt_get_id = $pdo->prepare($sql_get_id);
    $stmt_get_id->bindParam(':roll', $student_roll);
    $stmt_get_id->execute();
    $student_id = $stmt_get_id->fetchColumn();

    if ($student_id) {
        // 2. QUERY FOR SUMMARY - Filter by the found student_id
        $sql_summary = "SELECT 
                            COUNT(status) AS total_sessions,
                            SUM(CASE WHEN status = 'P' THEN 1 ELSE 0 END) AS sessions_present
                        FROM attendance
                        WHERE student_id = :s_id"; 
        $stmt_summary = $pdo->prepare($sql_summary);
        $stmt_summary->bindParam(':s_id', $student_id);
        $stmt_summary->execute();
        $summary_data = $stmt_summary->fetch(PDO::FETCH_ASSOC);

        $total_sessions = $summary_data['total_sessions'] ?? 0;
        $sessions_present = $summary_data['sessions_present'] ?? 0;
        
        $attendance_percentage = ($total_sessions > 0) 
            ? number_format(($sessions_present / $total_sessions) * 100, 1) 
            : "0.0";
        
        // 3. QUERY FOR DETAILED HISTORY - Filter by the found student_id
        $sql_history = "SELECT a.session_date, a.status, s.subject_name, s.subject_code 
                        FROM attendance a
                        JOIN subjects s ON a.subject_code = s.subject_code
                        WHERE a.student_id = :s_id
                        ORDER BY a.session_date DESC";
        $stmt_history = $pdo->prepare($sql_history);
        $stmt_history->bindParam(':s_id', $student_id);
        $stmt_history->execute();
        $history_data = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

    } else {
        $error = "Error: Student roll number **{$student_roll}** is not linked in the `students` table. Contact admin.";
    }

} catch (PDOException $e) {
    // Log error, but provide a friendly message to the user
    $error = "Database Error: Could not load attendance data. SQLSTATE[{$e->getCode()}]: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-wrapper { max-width: 900px; margin: 50px auto; padding: 20px; text-align: center; }
        .summary-boxes { display: flex; justify-content: space-around; margin: 30px 0; }
        .box { padding: 20px; border: 1px solid #ccc; border-radius: 8px; width: 30%; }
        .box h4 { margin: 0 0 10px 0; color: #555; }
        .box .value { font-size: 2em; font-weight: bold; }
        .present { color: green; }
        .absent { color: red; }
        .alert-danger { color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="header-nav" style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Welcome, <?php echo htmlspecialchars($student_name); ?> (<?php echo htmlspecialchars($student_roll); ?>)</h1>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <h2>Attendance Summary</h2>

        <div class="summary-boxes">
            <div class="box">
                <h4>Total Sessions Marked</h4>
                <div class="value"><?php echo $total_sessions; ?></div>
            </div>
            <div class="box">
                <h4>Sessions Present</h4>
                <div class="value present"><?php echo $sessions_present; ?></div>
            </div>
            <div class="box">
                <h4>Attendance Percentage</h4>
                <div class="value <?php echo ($attendance_percentage < 75 && $total_sessions > 0) ? 'absent' : 'present'; ?>">
                    <?php echo $attendance_percentage; ?>%
                </div>
            </div>
        </div>

        <hr style="margin: 40px 0;">
        
        <h2>Detailed Attendance History</h2>

        <?php if (empty($history_data)): ?>
            <div class="alert-info" style="color: blue;">No attendance records found yet.</div>
        <?php else: ?>
            <table border="1" style="width:100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Subject Code</th>
                        <th style="padding: 10px;">Subject Name</th>
                        <th style="padding: 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history_data as $record): ?>
                    <tr>
                        <td style="padding: 10px;"><?php echo date('M j, Y', strtotime($record['session_date'])); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($record['subject_code']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($record['subject_name']); ?></td>
                        <td style="padding: 10px;" class="<?php echo ($record['status'] === 'P') ? 'present' : 'absent'; ?>">
                            <?php echo ($record['status'] === 'P') ? 'Present' : 'Absent'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>