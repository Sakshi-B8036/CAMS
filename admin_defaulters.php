<?php
require_once 'config.php';

// Ensure the user is logged in and is an Admin ('A')
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

// Ensure the constant is defined (it is defined as 75 in your config.php)
$min_percentage = defined('ATTENDANCE_MIN_PERCENTAGE') ? ATTENDANCE_MIN_PERCENTAGE : 75; 
$defaulters_data = [];
$non_defaulters_data = [];
$error = "";

try {
    // 1. Fetch ALL student attendance records by joining students, users, and attendance tables.
    $sql_all_attendance = "
        SELECT
            s.roll_no,
            u.name,
            s.class,
            s.stream,
            COUNT(a.student_id) AS total_sessions,
            SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) AS sessions_present,
            (SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.student_id)) AS attendance_percentage
        FROM 
            students s
        JOIN
            users u ON s.roll_no = u.roll_no
        JOIN 
            attendance a ON s.student_id = a.student_id
        GROUP BY 
            s.student_id, s.roll_no, u.name, s.class, s.stream
    ";
    
    $stmt = $pdo->query($sql_all_attendance);
    $all_attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Separate data into Defaulters and Non-Defaulters lists
    foreach ($all_attendance_data as $record) {
        // Skip students with 0 total sessions to avoid showing 0% for those who haven't started.
        if ($record['total_sessions'] == 0) continue; 

        if ($record['attendance_percentage'] < $min_percentage) {
            $defaulters_data[] = $record;
        } else {
            $non_defaulters_data[] = $record;
        }
    }

    // Sort defaulters by percentage (lowest first)
    usort($defaulters_data, function($a, $b) {
        return $a['attendance_percentage'] <=> $b['attendance_percentage'];
    });
    
    // Sort non-defaulters by percentage (highest first)
    usort($non_defaulters_data, function($a, $b) {
        return $b['attendance_percentage'] <=> $a['attendance_percentage'];
    });


} catch (PDOException $e) {
    $error = "Database Error: Could not fetch attendance data. Message: " . $e->getMessage();
}

$total_students_monitored = count($defaulters_data) + count($non_defaulters_data);
$defaulters_count = count($defaulters_data);
$non_defaulters_count = count($non_defaulters_data);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Attendance Compliance</title>
    <style>
        body { 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            margin: 0; 
            padding: 0; 
            color: #333;
        }
        .container { 
            max-width: 1300px; /* Wider container for more data */
            margin: 40px auto; 
            padding: 30px; 
            background: #ffffff; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        h1 { 
            font-size: 28px;
            color: #007bff;
            font-weight: 700;
            margin: 0;
        }
        .back-link { 
            text-decoration: none; 
            color: #007bff; 
            font-weight: 600; 
            padding: 8px 15px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        .back-link:hover {
            background-color: #007bff;
            color: white;
        }
        .alert-danger {
            color: #a94442; 
            background-color: #f2dede; 
            border: 1px solid #ebccd1; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            text-align: center;
        }
        
        /* Summary Cards */
        .summary-metrics {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .defaulters-metric { 
            background-color: #fcebeb; 
            border-left: 5px solid #d9534f; /* Red */ 
        }
        .non-defaulters-metric { 
            background-color: #ebfcf3; 
            border-left: 5px solid #28a745; /* Green */ 
        }
        .metric-card h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
            text-transform: uppercase;
        }
        .metric-card p {
            font-size: 2.2em;
            font-weight: 700;
            margin: 0;
        }
        .defaulters-metric p { color: #d9534f; }
        .non-defaulters-metric p { color: #28a745; }

        /* Table Styling */
        .data-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .data-section h2 {
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid;
        }
        .defaulter-list h2 { border-bottom-color: #d9534f; color: #d9534f; }
        .non-defaulter-list h2 { border-bottom-color: #28a745; color: #28a745; }

        .data-table {
            width: 100%; 
            border-collapse: collapse;
            font-size: 0.95em;
        }
        .data-table th, .data-table td { 
            padding: 12px 15px; 
            text-align: left; 
            border-bottom: 1px solid #e9ecef;
        }
        .data-table th { 
            background-color: #f7f9fb; 
            color: #495057; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }
        .data-table tbody tr:hover {
            background-color: #fafafa;
        }
        .percent-low {
            font-weight: 600;
            color: #d9534f; /* Red */
        }
        .percent-high {
            font-weight: 600;
            color: #28a745; /* Green */
        }
        .metric-cell {
             text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Attendance Compliance Monitoring (Min: <?php echo $min_percentage; ?>%)</h1>
            <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="summary-metrics">
            <div class="metric-card defaulters-metric">
                <h3>Total Defaulters</h3>
                <p><?php echo $defaulters_count; ?></p>
            </div>
            <div class="metric-card non-defaulters-metric">
                <h3>Total Non-Defaulters</h3>
                <p><?php echo $non_defaulters_count; ?></p>
            </div>
            <div class="metric-card" style="background-color: #f0f7ff; border-left: 5px solid #007bff;">
                <h3>Total Students Monitored</h3>
                <p style="color: #007bff;"><?php echo $total_students_monitored; ?></p>
            </div>
        </div>

        <div class="data-section defaulter-list">
            <h2>🚨 Defaulters List (Attendance below <?php echo $min_percentage; ?>%)</h2>
            <?php if (empty($defaulters_data)): ?>
                <div style="text-align: center; padding: 20px; background: #fff3f3; border: 1px solid #d9534f; border-radius: 5px; color: #d9534f; font-weight: 500;">
                    <p>No students found below the <?php echo $min_percentage; ?>% threshold.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Roll No</th>
                            <th style="width: 25%;">Student Name</th>
                            <th style="width: 15%;">Class/Stream</th>
                            <th style="width: 15%;" class="metric-cell">Total Sessions</th>
                            <th style="width: 15%;" class="metric-cell">Sessions Present</th>
                            <th style="width: 20%;" class="metric-cell">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($defaulters_data as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                            <td><?php echo htmlspecialchars($record['class'] . " (" . $record['stream'] . ")"); ?></td>
                            <td class="metric-cell"><?php echo $record['total_sessions']; ?></td>
                            <td class="metric-cell"><?php echo $record['sessions_present']; ?></td>
                            <td class="metric-cell percent-low">
                                **<?php echo number_format($record['attendance_percentage'], 1); ?>%**
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <hr style="margin: 50px 0; border-color: #eee;">

        <div class="data-section non-defaulter-list">
            <h2>✅ Non-Defaulters List (Attendance at or above <?php echo $min_percentage; ?>%)</h2>
            <?php if (empty($non_defaulters_data)): ?>
                <div style="text-align: center; padding: 20px; background: #f0fff3; border: 1px solid #28a745; border-radius: 5px; color: #28a745; font-weight: 500;">
                    <p>No students have met or exceeded the <?php echo $min_percentage; ?>% compliance threshold.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Roll No</th>
                            <th style="width: 25%;">Student Name</th>
                            <th style="width: 15%;">Class/Stream</th>
                            <th style="width: 15%;" class="metric-cell">Total Sessions</th>
                            <th style="width: 15%;" class="metric-cell">Sessions Present</th>
                            <th style="width: 20%;" class="metric-cell">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($non_defaulters_data as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                            <td><?php echo htmlspecialchars($record['class'] . " (" . $record['stream'] . ")"); ?></td>
                            <td class="metric-cell"><?php echo $record['total_sessions']; ?></td>
                            <td class="metric-cell"><?php echo $record['sessions_present']; ?></td>
                            <td class="metric-cell percent-high">
                                **<?php echo number_format($record['attendance_percentage'], 1); ?>%**
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>