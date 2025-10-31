<?php
require_once 'config.php';

// Ensure only admin can access
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

$error = $success = "";

// --------------------
// 1️⃣ Handle Add Teacher/Student
// --------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $role = $_POST["role"];
    $roll_no = trim($_POST["roll_no"]);
    $name = trim($_POST["name"]);
    $password = trim($_POST["password"]);

    if (empty($role) || empty($roll_no) || empty($name) || empty($password)) {
        $error = "All fields are required!";
    } else {
        try {
            $sql = "INSERT INTO users (roll_no, name, password, user_role) VALUES (:roll_no, :name, :password, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':roll_no' => $roll_no,
                ':name' => $name,
                ':password' => $password,
                ':role' => strtoupper(substr($role, 0, 1))
            ]);
            $success = "$role added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding user: " . $e->getMessage();
        }
    }
}

// --------------------
// 2️⃣ Handle Add Subject
// --------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_subject"])) {
    $subject_code = trim($_POST["subject_code"]);
    $subject_name = trim($_POST["subject_name"]);
    $teacher_id = trim($_POST["teacher_id"]);

    if (empty($subject_code) || empty($subject_name) || empty($teacher_id)) {
        $error = "All subject fields are required!";
    } else {
        try {
            $sql = "INSERT INTO subjects (subject_code, subject_name, teacher_id) VALUES (:subject_code, :subject_name, :teacher_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':subject_code' => $subject_code,
                ':subject_name' => $subject_name,
                ':teacher_id' => $teacher_id
            ]);
            $success = "Subject added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding subject: " . $e->getMessage();
        }
    }
}

// --------------------
// 3️⃣ Fetch Attendance Summary
// --------------------
try {
    $sql_summary = "
        SELECT s.subject_code, s.subject_name,
               COUNT(a.attendance_id) AS total_records,
               SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) AS total_present,
               ROUND(SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) / COUNT(a.attendance_id) * 100, 1) AS percentage
        FROM attendance a
        JOIN subjects s ON a.subject_code = s.subject_code
        GROUP BY s.subject_code, s.subject_name
        ORDER BY s.subject_code ASC";
    $summary_stmt = $pdo->prepare($sql_summary);
    $summary_stmt->execute();
    $attendance_summary = $summary_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $attendance_summary = [];
    $error = "Error loading summary: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; }
        .container { width: 90%; margin: 30px auto; }
        h1 { text-align: center; color: #007bff; margin-bottom: 30px; }
        .card {
            background: #fff; padding: 20px; border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px;
        }
        .card h2 { margin-bottom: 15px; color: #333; }
        input, select { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn { background-color: #007bff; color: white; padding: 10px; border: none; border-radius: 5px; width: 100%; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: center; border: 1px solid #ccc; }
        th { background-color: #007bff; color: white; }
        .alert { padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .logout-link { text-decoration: none; color: red; float: right; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👑 Admin Dashboard</h1>
        <a href="logout.php" class="logout-link">Logout</a>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- 1️⃣ Add Teacher / Student -->
        <div class="card">
            <h2>Add Teacher / Student</h2>
            <form method="POST">
                <label>Role:</label>
                <select name="role">
                    <option value="Teacher">Teacher</option>
                    <option value="Student">Student</option>
                </select>
                <label>Roll No:</label>
                <input type="text" name="roll_no" required>
                <label>Name:</label>
                <input type="text" name="name" required>
                <label>Password:</label>
                <input type="text" name="password" value="12345" required>
                <button type="submit" name="add_user" class="btn">Add User</button>
            </form>
        </div>

        <!-- 2️⃣ Manage Subjects -->
        <div class="card">
            <h2>Manage Subjects</h2>
            <form method="POST">
                <label>Subject Code:</label>
                <input type="text" name="subject_code" required>
                <label>Subject Name:</label>
                <input type="text" name="subject_name" required>
                <label>Assigned Teacher Roll No:</label>
                <input type="text" name="teacher_id" required>
                <button type="submit" name="add_subject" class="btn">Add Subject</button>
            </form>
        </div>

        <!-- 3️⃣ View Attendance Summary -->
        <div class="card">
            <h2>Overall Attendance Summary</h2>
            <?php if (!empty($attendance_summary)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Total Records</th>
                            <th>Total Present</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_summary as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_records']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_present']); ?></td>
                                <td><?php echo htmlspecialchars($row['percentage']); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No attendance records found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
