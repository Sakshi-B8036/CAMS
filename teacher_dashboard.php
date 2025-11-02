<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'T') redirect('login.php');

$teacher_roll = $_SESSION['roll_no'];
$name = $_SESSION['name'] ?? '';

$subjects = $pdo->prepare("SELECT id,subject_code,subject_name,class,semester FROM subjects WHERE teacher_id = :t ORDER BY id ASC");
$subjects->execute([':t'=>$teacher_roll]);
$subjects = $subjects->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Teacher Dashboard</title>
<style>
body {
    font-family: Segoe UI, sans-serif;
    background: linear-gradient(90deg,#74617c,#3498db);
    color: #fff;
    padding: 20px;
}
.card {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    color: #333;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
}
.btn {
    display: inline-block;
    background: #1abc9c;
    padding: 10px 16px;
    color: #fff;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    margin: 8px 4px;
}
.btn:hover { background: #16a085; }
select, input[type=date] {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #aaa;
}
</style>
</head>
<body>

<div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>

    <h3>Mark Attendance</h3>
    
    <?php if (empty($subjects)) : ?>
        <div style="background:#c0392b;padding:12px;border-radius:8px;color:#fff">No subjects assigned.</div>
    <?php else: ?>
        <form method="get" action="mark_attendance.php">
            <select name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?php echo $s['id']; ?>">
                        <?php echo htmlspecialchars($s['subject_name'].' ('.$s['subject_code'].' - '.$s['class'].')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="session_date" value="<?php echo date('Y-m-d'); ?>" required>
            <button type="submit" class="btn">Load Class List</button>
        </form>
    <?php endif; ?>

    <br><br>
    <a href="view_attendance.php" class="btn">📊 View Attendance History</a>
    <a href="logout.php" class="btn" style="background:#e74c3c">Logout</a>
</div>

</body>
</html>
