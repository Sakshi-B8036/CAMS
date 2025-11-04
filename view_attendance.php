<?php
require_once 'config.php';

// -------------------------------------------------------------
// 1️⃣ Check login and role
// -------------------------------------------------------------
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

$teacher_roll = $_SESSION["roll_no"];
$teacher_name = $_SESSION["name"];
$error = "";
$subjects = [];
$attendance_records = [];

// -------------------------------------------------------------
// 2️⃣ Fetch ALL subjects (teacher can view any subject)
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
// 3️⃣ If teacher selects subject & date, fetch attendance records
// -------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_code = $_POST['subject_code'] ?? '';
    $session_date = $_POST['session_date'] ?? '';

    if (!empty($subject_code) && !empty($session_date)) {
        try {
            // 🛑 FIX: The column 'a.marked_at' is REMOVED/CORRECTED.
            // Since the date is provided in the WHERE clause, fetching 'session_date' is redundant,
            // but we'll fetch the date/time the record was CREATED if such a column exists
            // (assuming 'marked_at' was intended to be a TIMESTAMP).
            // However, based on your structure, 'session_date' is the closest date field.
            
            // To be safe, let's remove a.marked_at entirely, as 'session_date' is already selected via the form.
            // If you have a separate TIMESTAMP column (e.g., 'created_at'), you should use that instead.
            
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
        $error = "Please select a subject and date.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance Records</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; }
        .container { width: 800px; margin: 40px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; }
        hr { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #007bff; color: white; }
        .btn { background-color: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .alert-danger { color: red; margin-top: 10px; text-align: center; }
        .alert-info { color: #007bff; margin-top: 10px; text-align: center; }
        .status-present { color: green; font-weight: bold; }
        .status-absent { color: red; font-weight: bold; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-nav">
        <h2>📅 View Attendance Records</h2>
        <a href="teacher_dashboard.php" class="btn">← Back to Dashboard</a>
    </div>
    <p style="text-align:center;">Welcome, <b><?php echo htmlspecialchars($teacher_name); ?></b> (<?php echo htmlspecialchars($teacher_roll); ?>)</p>
    <hr>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" style="text-align:center;">
        <label for="subject_code"><b>Select Subject:</b></label>
        <select name="subject_code" required>
            <option value="">-- Choose Subject --</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?php echo htmlspecialchars($subject['subject_code']); ?>"
                    <?php if (!empty($_POST['subject_code']) && $_POST['subject_code'] === $subject['subject_code']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo htmlspecialchars($subject['subject_code']); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="session_date"><b>Select Date:</b></label>
        <input type="date" name="session_date" value="<?php echo htmlspecialchars($_POST['session_date'] ?? ''); ?>" required>

        <button type="submit" class="btn">View Attendance</button>
    </form>

    <?php if (!empty($attendance_records)): ?>
        <h3 style="margin-top: 30px; text-align:center;">Attendance for <?php echo htmlspecialchars($_POST['session_date']); ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Stream</th>
                    <th>Status</th>
                    <th>Session Date</th> </tr>
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
                        <td><?php echo htmlspecialchars($_POST['session_date']); ?></td> </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST" && empty($attendance_records)): ?>
        <div class="alert-info">No attendance records found for this subject and date.</div>
    <?php endif; ?>
</div>
</body>
</html>