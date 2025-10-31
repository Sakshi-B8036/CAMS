<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') redirect('login.php');

$summary = $pdo->query("
    SELECT s.subject_code, s.subject_name, COUNT(a.attendance_id) AS total_records,
           SUM(a.status='P') AS total_present, SUM(a.status='A') AS total_absent
    FROM subjects s
    LEFT JOIN attendance a ON s.subject_code = a.subject_code
    GROUP BY s.subject_code, s.subject_name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overall Attendance Summary</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .container { width: 700px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2>📊 Overall Attendance Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Total Records</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Attendance %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary as $row): 
                $percentage = ($row['total_records'] > 0)
                    ? round(($row['total_present'] / $row['total_records']) * 100, 2)
                    : 0;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td><?php echo $row['total_records']; ?></td>
                    <td style="color:green;"><?php echo $row['total_present']; ?></td>
                    <td style="color:red;"><?php echo $row['total_absent']; ?></td>
                    <td><b><?php echo $percentage; ?>%</b></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
