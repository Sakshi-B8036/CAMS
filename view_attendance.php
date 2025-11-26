<?php
require_once 'config.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'T') {
    redirect('login.php');
}

<<<<<<< HEAD
$teacher_roll = $_SESSION["roll_no"];
$teacher_name = $_SESSION["name"];
$error = "";
$success_message = ""; // ⬅️ NEW: Variable for success messages
$subjects = [];
$attendance_records = [];
$defaulters_list = [];
$view_type = 'session'; // Default view type

// -------------------------------------------------------------
// 2️⃣ Fetch ALL subjects
// -------------------------------------------------------------
try {
    $sql_subjects = "SELECT subject_code, subject_name FROM subjects ORDER BY subject_name ASC";
    $stmt_subjects = $pdo->prepare($sql_subjects);
    $stmt_subjects->execute();
    $subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

// -------------------------------------------------------------
// 3️⃣ Handle Form Submission & Warning Action
// -------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_code = $_POST['subject_code'] ?? '';
    $view_type = $_POST['view_type'] ?? 'session';

    // 🅰️ Handle Defaulter Warning Submission (NEW LOGIC)
    if (isset($_POST['warn_student'])) {
        $warn_roll_no = $_POST['warn_roll_no'] ?? '';
        $warn_subject_code = $_POST['warn_subject_code'] ?? '';

        if (!empty($warn_roll_no) && !empty($warn_subject_code)) {
            try {
                // ASSUMPTION: 'defaulter_warnings' table exists and has columns: roll_no, subject_code, warning_date, issued_by
                $sql_insert_warning = "INSERT INTO defaulter_warnings (roll_no, subject_code, warning_date, issued_by) 
                                       VALUES (:roll_no, :subject_code, CURDATE(), :teacher_roll)";
                
                $stmt_warning = $pdo->prepare($sql_insert_warning);
                $stmt_warning->execute([
                    ':roll_no' => $warn_roll_no,
                    ':subject_code' => $warn_subject_code,
                    ':teacher_roll' => $teacher_roll
                ]);
                
                $success_message = "Alert successfully issued to student **" . htmlspecialchars($warn_roll_no) . "** for " . htmlspecialchars($warn_subject_code) . ".";
            } catch (PDOException $e) {
                // Handle cases like duplicate entry if an index exists
                $error = "Error issuing warning. (Note: The student may have already been warned for this subject.)";
            }
        } else {
            $error = "Missing student or subject information for warning.";
        }
        
        // Ensure we fetch the defaulters list again after submission
        $view_type = 'defaulters';
        $subject_code = $warn_subject_code;
    }


    // 🅱️ Fetch Data (Session or Defaulters)
    if (empty($subject_code)) {
        $error = "Please select a subject.";
    } elseif ($view_type === 'session') {
        // --- View Specific Session Attendance ---
        $session_date = $_POST['session_date'] ?? '';

        if (!empty($session_date)) {
            try {
                // Fetch attendance records for a specific date and subject
                $sql = "SELECT u.roll_no, u.name, a.status, s.class, s.stream
                        FROM attendance a
                        JOIN students s ON a.student_id = s.student_id
                        JOIN users u ON s.roll_no = u.roll_no
                        WHERE a.subject_code = :subject_code
                        AND a.session_date = :session_date
                        ORDER BY u.roll_no";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':subject_code' => $subject_code,
                    ':session_date' => $session_date
                ]);
                $attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $error = "Error fetching attendance: " . $e->getMessage();
            }
        } else {
            // Set error if warn_student was not set but form submitted without date
            if (!isset($_POST['warn_student'])) {
                 $error = "Please select a subject and date.";
            }
        }
    } elseif ($view_type === 'defaulters') {
        // --- View Defaulters List ---
        $threshold = 75; // Set the default defaulter threshold to 75%
        
        try {
            // 1. Fetch existing warnings for the current subject
            $sql_warnings_check = "SELECT roll_no FROM defaulter_warnings WHERE subject_code = :subject_code";
            $stmt_warnings = $pdo->prepare($sql_warnings_check);
            $stmt_warnings->execute([':subject_code' => $subject_code]);
            $existing_warnings = $stmt_warnings->fetchAll(PDO::FETCH_COLUMN);

            // 2. Fetch defaulters
            $sql_defaulters = "
                SELECT 
                    u.roll_no, 
                    u.name, 
                    s.class, 
                    s.stream,
                    (SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) * 100.0 / COUNT(DISTINCT a.session_date)) AS attendance_percentage,
                    COUNT(DISTINCT a.session_date) AS total_sessions
                FROM attendance a
                JOIN students s ON a.student_id = s.student_id
                JOIN users u ON s.roll_no = u.roll_no
                WHERE a.subject_code = :subject_code
                GROUP BY u.roll_no, u.name, s.class, s.stream
                HAVING attendance_percentage < :threshold
                ORDER BY attendance_percentage ASC";

            $stmt_defaulters = $pdo->prepare($sql_defaulters);
            $stmt_defaulters->execute([
                ':subject_code' => $subject_code,
                ':threshold' => $threshold
            ]);
            $defaulters_list = $stmt_defaulters->fetchAll(PDO::FETCH_ASSOC);

            // 3. Attach warning status
            foreach ($defaulters_list as $key => $row) {
                $defaulters_list[$key]['warning_issued'] = in_array($row['roll_no'], $existing_warnings);
            }
        } catch (PDOException $e) {
            $error = "Error fetching defaulters list: " . $e->getMessage();
=======
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
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
        }
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
<<<<<<< HEAD
        body { font-family: Arial; background-color: #f9f9f9; }
        .container { width: 900px; margin: 40px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; }
        hr { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #007bff; color: white; }
        .btn { background-color: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; margin: 0 5px; }
        .btn:hover { background-color: #0056b3; }
        .alert-danger { color: red; margin-top: 10px; text-align: center; }
        .alert-info { color: #007bff; margin-top: 10px; text-align: center; }
        .alert-success { color: green; margin-top: 10px; text-align: center; font-weight: bold; } /* ⬅️ NEW: Success style */
        .status-present { color: green; font-weight: bold; }
        .status-absent { color: red; font-weight: bold; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .form-group { margin: 15px 0; display: flex; align-items: center; justify-content: center; gap: 10px;}
        .defaulter { background-color: #ffe0e0; }
        .warn-btn { background-color: #ffc107; color: #343a40; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; } /* ⬅️ NEW: Warn button style */
        .warn-btn:hover { background-color: #e0a800; }
        .warn-btn:disabled { background-color: #6c757d; cursor: not-allowed; color: #fff; } /* ⬅️ NEW: Disabled button style */
=======
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
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
    </style>
</head>
<body>
    <div class="container">
        <h2>View Attendance History</h2>

<<<<<<< HEAD
    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?> 
        <div class="alert-success"><?php echo $success_message; ?></div> 
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="subject_code"><b>Select Subject:</b></label>
            <select name="subject_code" required>
                <option value="">-- Choose Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo htmlspecialchars($subject['subject_code']); ?>"
                        <?php if (!empty($subject_code) && $subject_code === $subject['subject_code']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo htmlspecialchars($subject['subject_code']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="text-align:center;">
            <input type="hidden" name="view_type" id="view_type" value="<?php echo htmlspecialchars($view_type); ?>">
            
            <label for="session_date"><b>Select Date:</b></label>
            <input type="date" name="session_date" value="<?php echo htmlspecialchars($_POST['session_date'] ?? ''); ?>">
            <button type="submit" class="btn" onclick="document.getElementById('view_type').value='session';">View Session</button>

            <button type="submit" class="btn" style="background-color: #dc3545;" onclick="document.getElementById('view_type').value='defaulters';">View Defaulters (Below 75%)</button>
        </div>
    </form>
    
    <hr>

    <?php 
    // Get the selected subject name for the heading
    $selected_subject_name = '';
    foreach ($subjects as $subject) {
        if (!empty($subject_code) && $subject['subject_code'] === $subject_code) {
            $selected_subject_name = $subject['subject_name'];
            break;
        }
    }
    ?>

    <?php if ($view_type === 'session' && !empty($attendance_records)): ?>
        <h3 style="margin-top: 30px; text-align:center;">Attendance for 
            <?php echo htmlspecialchars($selected_subject_name); ?> on <?php echo htmlspecialchars($_POST['session_date']); ?>
        </h3>
        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Stream</th>
                    <th>Status</th>
                    <th>Session Date</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['roll_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['class']); ?></td>
                        <td><?php echo htmlspecialchars($row['stream']); ?></td>
                        <td class="<?php echo $row['status'] === 'P' ? 'status-present' : 'status-absent'; ?>">
                            <?php echo $row['status'] === 'P' ? 'Present ✅' : 'Absent ❌'; ?>
                        </td>
                        <td><?php echo htmlspecialchars($_POST['session_date']); ?></td> 
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($view_type === 'defaulters' && !empty($defaulters_list)): ?>
        <h3 style="margin-top: 30px; text-align:center; color: #dc3545;">
            Defaulters List (Below 75%) for <?php echo htmlspecialchars($selected_subject_name); ?>
        </h3>
        <table>
            <thead>
                <tr style="background-color: #dc3545;">
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Stream</th>
                    <th>Total Sessions</th>
                    <th>Attendance %</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($defaulters_list as $row): ?>
                    <tr class="defaulter">
                        <td><?php echo htmlspecialchars($row['roll_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['class']); ?></td>
                        <td><?php echo htmlspecialchars($row['stream']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_sessions']); ?></td>
                        <td style="font-weight: bold;"><?php echo number_format($row['attendance_percentage'], 2) . '%'; ?></td>
                        <td>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="warn_student" value="1">
                                <input type="hidden" name="warn_roll_no" value="<?php echo htmlspecialchars($row['roll_no']); ?>">
                                <input type="hidden" name="warn_subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
                                
                                <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
                                <input type="hidden" name="view_type" value="defaulters">

                                <button type="submit" class="warn-btn"
                                    <?php echo $row['warning_issued'] ? 'disabled' : ''; ?>
                                    title="<?php echo $row['warning_issued'] ? 'Warning already sent' : 'Send alert to student dashboard'; ?>">
                                    <?php echo $row['warning_issued'] ? 'Alert Sent' : 'Warn'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST" && $view_type === 'defaulters' && empty($defaulters_list)): ?>
        <div class="alert-info">🎉 Great! No defaulters found for this subject (all students are above the 75% threshold).</div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST" && $view_type === 'session' && empty($attendance_records)): ?>
        <div class="alert-info">No attendance records found for this subject and date.</div>
    <?php endif; ?>
</div>
=======
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
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
</body>
</html>