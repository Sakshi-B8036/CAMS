<?php
require_once 'config.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'T') {
    redirect('login.php');
}

$teacher_roll = $_SESSION['roll_no'];
$name = $_SESSION['name'] ?? '';
$error = '';
$attendance_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'] ?? '';
    $session_date = $_POST['session_date'] ?? '';

    if (!empty($subject_id) && !empty($session_date)) {
        try {
            $sql = "SELECT s.student_name, s.roll_no, a.status, a.session_date, a.created_at
                    FROM attendance a
                    JOIN students s ON a.student_roll = s.roll_no
                    WHERE a.subject_id = :sid AND a.session_date = :sdate
                    ORDER BY s.roll_no ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':sid' => $subject_id, ':sdate' => $session_date]);
            $attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please select a subject and date.";
    }
}

// Fetch teacher subjects for dropdown
$stmt = $pdo->prepare("SELECT id, subject_name, subject_code, class FROM subjects WHERE teacher_id = :t");
$stmt->execute([':t' => $teacher_roll]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance History</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(90deg, #74617c, #3498db);
            margin: 0;
            padding: 30px;
            color: #333;
        }
        .container {
            background: #fff;
            border-radius: 12px;
            padding: 25px 30px;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            background-color: #3498db;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .form-inline {
            text-align: center;
            margin-bottom: 20px;
        }
        select, input[type="date"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin: 0 5px;
        }
        button {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover {
            background-color: #159a80;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .alert {
            text-align: center;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .alert-error {
            background-color: #e74c3c;
            color: white;
        }
        .alert-info {
            background-color: #3498db;
            color: white;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>View Attendance History</h2>

        <form method="POST" class="form-inline">
            <select name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?php echo $sub['id']; ?>">
                        <?php echo htmlspecialchars($sub['subject_name'].' ('.$sub['subject_code'].' - '.$sub['class'].')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="session_date" required value="<?php echo date('Y-m-d'); ?>">
            <button type="submit">View Attendance</button>
        </form>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($attendance_data)): ?>
            <div class="alert alert-info">No attendance records found for this date.</div>
        <?php elseif (!empty($attendance_data)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Session Date</th>
                        <th>Marked On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo $row['status'] === 'P' ? '✅ Present' : '❌ Absent'; ?></td>
                            <td><?php echo htmlspecialchars($row['session_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="teacher_dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
</body>
</html>
