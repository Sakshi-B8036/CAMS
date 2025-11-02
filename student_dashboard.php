<?php
require_once 'config.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'S') {
    redirect('login.php');
}

$student_roll = $_SESSION['roll_no'];
$student_name = $_SESSION['name'] ?? '';

/* -------- FETCH ATTENDANCE SUMMARY -------- */
try {
    // Total sessions student has records for
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE student_roll = :sr");
    $stmt->execute([':sr' => $student_roll]);
    $total_sessions = (int)$stmt->fetchColumn();

    // Total present sessions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE student_roll = :sr AND status = 'P'");
    $stmt->execute([':sr' => $student_roll]);
    $present_sessions = (int)$stmt->fetchColumn();

    $attendance_percentage = $total_sessions > 0 ? round(($present_sessions / $total_sessions) * 100, 2) : 0;

    // Fetch detailed attendance
    $stmt = $pdo->prepare("
        SELECT a.session_date, a.status, s.subject_code, s.subject_name
        FROM attendance a
        JOIN subjects s ON a.subject_id = s.id
        WHERE a.student_roll = :sr
        ORDER BY a.session_date DESC
    ");
    $stmt->execute([':sr' => $student_roll]);
    $attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f4f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            background: #3498db;
            color: white;
            padding: 12px;
            border-radius: 8px;
        }
        .summary-boxes {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
        }
        .box {
            flex: 1;
            margin: 0 10px;
            background: #fff;
            border: 1px solid #ddd;
            padding: 18px;
            text-align: center;
            border-radius: 8px;
        }
        .box h3 {
            color: #444;
        }
        .box p {
            font-size: 28px;
            margin: 8px 0 0 0;
            font-weight: bold;
        }
        .red { color: #e74c3c; }
        .green { color: #2ecc71; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background: #3498db;
            color: white;
        }
        tr:nth-child(even) { background: #f9f9f9; }
        .logout-btn {
            float: right;
            padding: 8px 14px;
            border: 1px solid #c0392b;
            color: #c0392b;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #c0392b;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="logout.php" class="logout-btn">Logout</a>
    <h2>Welcome, <?php echo htmlspecialchars($student_name . " (" . $student_roll . ")"); ?></h2>

    <h3 style="text-align:center; margin-top:20px;">Attendance Summary</h3>

    <div class="summary-boxes">
        <div class="box">
            <h3>Total Sessions Marked</h3>
            <p><?php echo $total_sessions; ?></p>
        </div>
        <div class="box">
            <h3>Sessions Present</h3>
            <p><?php echo $present_sessions; ?></p>
        </div>
        <div class="box">
            <h3>Attendance Percentage</h3>
            <p class="<?php echo ($attendance_percentage < 75) ? 'red' : 'green'; ?>">
                <?php echo $attendance_percentage; ?>%
            </p>
        </div>
    </div>

    <h3 style="text-align:center;">Detailed Attendance History</h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($attendance_records)) : ?>
            <tr><td colspan="4">No attendance records found.</td></tr>
        <?php else: ?>
            <?php foreach ($attendance_records as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['session_date']); ?></td>
                <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                <td><?php echo $row['status'] === 'P' ? '✅ Present' : '❌ Absent'; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
