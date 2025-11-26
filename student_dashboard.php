<?php
require_once 'config.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'S') {
    redirect('login.php');
}

<<<<<<< HEAD
$student_roll = $_SESSION["roll_no"];
$student_name = $_SESSION["name"];
$error = "";

// Initialize data arrays to avoid 'Undefined variable' warnings
$total_sessions = 0;
$sessions_present = 0;
$attendance_percentage = "0.0";
$history_data = [];
$warning_data = []; // ⬅️ NEW: Initialize warning data array
=======
$student_roll = $_SESSION['roll_no'];
$student_name = $_SESSION['name'] ?? '';
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37

/* -------- FETCH ATTENDANCE SUMMARY -------- */
try {
    // Total sessions student has records for
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE student_roll = :sr");
    $stmt->execute([':sr' => $student_roll]);
    $total_sessions = (int)$stmt->fetchColumn();

<<<<<<< HEAD
    if ($student_id) {
        // 2. QUERY FOR SUMMARY
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
        
        // 3. QUERY FOR DETAILED HISTORY
        $sql_history = "SELECT a.session_date, a.status, s.subject_name, s.subject_code 
                        FROM attendance a
                        JOIN subjects s ON a.subject_code = s.subject_code
                        WHERE a.student_id = :s_id
                        ORDER BY a.session_date DESC";
        $stmt_history = $pdo->prepare($sql_history);
        $stmt_history->bindParam(':s_id', $student_id);
        $stmt_history->execute();
        $history_data = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

        // 4. ⬅️ NEW: QUERY FOR DEFAULTER WARNINGS
        $sql_warnings = "SELECT dw.subject_code, s.subject_name, dw.warning_date 
                         FROM defaulter_warnings dw
                         JOIN subjects s ON dw.subject_code = s.subject_code
                         WHERE dw.roll_no = :roll_no
                         ORDER BY dw.warning_date DESC, dw.subject_code ASC";
        $stmt_warnings = $pdo->prepare($sql_warnings);
        $stmt_warnings->bindParam(':roll_no', $student_roll);
        $stmt_warnings->execute();
        $warning_data = $stmt_warnings->fetchAll(PDO::FETCH_ASSOC);

    } else {
        $error = "Error: Student roll number **{$student_roll}** is not linked in the `students` table. Contact admin.";
    }
=======
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
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37

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
<<<<<<< HEAD
        /* Base Styles: Professional and clean */
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc; /* Soft, light background */
            color: #333;
        }
        
        /* Dashboard Wrapper (Container) */
        .dashboard-wrapper { 
            max-width: 1000px; 
            margin: 50px auto; 
            padding: 30px; 
            text-align: center; 
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Clean, visible shadow */
        }
        
        /* Header and Titles */
        .header-nav {
            border-bottom: 2px solid #947d7dff; /* Subtle divider */
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        h1 { 
            font-size: 26px;
            color: #e90202ff; /* Primary institutional blue */
            font-weight: 700;
            margin: 0;
            text-align: left;
        }
        h2 {
            font-size: 20px;
            color: #2c2929ff;
            margin: 20px 0;
            font-weight: 600;
            text-align: left;
        }
        .logout-link {
            text-decoration: none;
            color: #df7b7bff;
            font-weight: 600;
            padding: 8px 15px;
            border: 1px solid #f54b03ff;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        .logout-link:hover {
            background-color: #c00f0fff;
            color: white;
        }

        /* ⬅️ NEW: Warning Alert Styling */
        .warning-alert {
            background-color: #fff3cd; /* Light yellow background */
            color: #856404; /* Dark yellow text */
            border: 1px solid #ffeeba;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: left;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
        }
        .warning-alert h3 {
            margin-top: 0;
            color: #dc3545; /* Red color for urgency */
            font-size: 1.3em;
            margin-bottom: 10px;
        }
        .warning-list {
            list-style-type: '🚨 '; /* Custom list marker */
            padding-left: 20px;
        }
        .warning-list li {
            margin-bottom: 5px;
            padding-left: 5px;
        }
        .warning-list strong {
            color: #dc3545;
        }


        /* Summary Boxes (Card Styling) */
        .summary-boxes { 
            display: flex; 
            justify-content: space-around; 
            gap: 20px;
            margin: 30px 0; 
        }
        .box { 
            flex: 1;
            padding: 25px 15px; 
            border: 1px solid #e0e0e0;
            border-radius: 8px; 
            background-color: #f1f1f1ff; /* Light grey background */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s;
        }
        .box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.06);
        }
        .box h4 { 
            margin: 0 0 10px 0; 
            color: #f68e8cff; 
            font-size: 14px;
            text-transform: uppercase;
        }
        .box .value { 
            font-size: 2.2em; 
            font-weight: 800;
        }

        /* Status Colors - Applied to .value and table cells */
        .present { 
            color: #27ae60; /* Professional Green */
            font-weight: 800;
        } 
        .absent { 
            color: #df0d17ff; /* Professional Red */
            font-weight: 800;
        }
        
        /* Error Alert Styling */
        .alert-danger { 
            color: #c0392b; 
            background-color: #f8e8e8; 
            border: 1px solid #e74c3c; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 25px; 
            font-weight: 500;
            text-align: center;
        }

        /* Divider */
        hr {
            border: 0;
            border-top: 1px solid #e0e0e0;
            margin: 40px 0;
        }

        /* Detailed History Table */
        table {
            border: none !important; /* Override original border="1" */
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        table thead tr {
            background-color: #eef2f7 !important; /* Light blue/grey header background */
            border-bottom: 2px solid #d0d0d0;
        }
        table th {
            padding: 12px 10px;
            color: #ed7d32ff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            text-align: left;
        }
        table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        table tbody tr:hover {
            background-color: #fafafa;
        }
        /* Aligning the Status column content to the center */
        table th:last-child, table td:last-child {
             text-align: center;
        }
        .alert-info { 
            padding: 15px;
            border: 1px solid #a0a0ff;
            background-color: #eef1ff;
            border-radius: 5px;
        }

=======
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
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
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
<<<<<<< HEAD
        
        <?php if (!empty($warning_data)): ?>
            <div class="warning-alert">
                <h3>⚠️ Urgent Warning: Defaulter List Imminent!</h3>
                <p>You have received **official warnings** for the subjects listed below due to **attendance being below the required 75% threshold**. You are advised to immediately meet with the concerned faculty.</p>
                <ul class="warning-list">
                    <?php foreach ($warning_data as $warning): ?>
                        <li>
                            **<?php echo htmlspecialchars($warning['subject_name']); ?> (<?php echo htmlspecialchars($warning['subject_code']); ?>)** — Warning Issued on: <strong><?php echo date('M j, Y', strtotime($warning['warning_date'])); ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

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
                    <tr style="background-color: #ccc8c8ff;">
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
=======
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
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
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
