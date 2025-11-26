<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'A') redirect('login.php');

$error = ''; 
$success = '';

// Fetch teachers
$teachers = $pdo->query("SELECT roll_no,name FROM users WHERE user_role='T' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Insert / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_subject'])) {
    $id = !empty($_POST['subject_id']) ? intval($_POST['subject_id']) : null;
    $code = trim($_POST['subject_code']);
    $name = trim($_POST['subject_name']);
    $class = ($_POST['class_select'] === 'OTHER') ? trim($_POST['class_other']) : trim($_POST['class_select']);
    $semester = trim($_POST['semester']);
    $teacher = trim($_POST['teacher_id']);

    if ($code==='' || $name==='' || $class==='' || $semester==='' || $teacher==='') {
        $error = 'All fields required.';
    } else {
        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE subjects 
                    SET subject_code=:code, subject_name=:name, class=:class, semester=:sem, teacher_id=:t 
                    WHERE id=:id");
                $stmt->execute([':code'=>$code,':name'=>$name,':class'=>$class,':sem'=>$semester,':t'=>$teacher,':id'=>$id]);
                $success = "Subject updated successfully.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO subjects (subject_code,subject_name,class,semester,teacher_id) 
                    VALUES (:code,:name,:class,:sem,:t)");
                $stmt->execute([':code'=>$code,':name'=>$name,':class'=>$class,':sem'=>$semester,':t'=>$teacher]);
                $success = "Subject added successfully.";
            }
        } catch (PDOException $e) {
            $error = "DB error: ".$e->getMessage();
        }
    }
}

// Delete
if (isset($_GET['delete'])) {
    $did = intval($_GET['delete']);
    try {
        $pdo->prepare("DELETE FROM subjects WHERE id=:id")->execute([':id'=>$did]);
        $success = "Subject deleted.";
    } catch (PDOException $e) {
        $error = "Cannot delete subject (linked to attendance).";
    }
}

// Fetch subjects
$subjects = $pdo->query("
    SELECT s.*, u.name AS teacher_name 
    FROM subjects s 
    LEFT JOIN users u ON s.teacher_id = u.roll_no 
    ORDER BY s.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Manage Subjects</title>
<style>
input, select { padding:8px; width:100%; border-radius:6px; border:1px solid #ccc; margin-bottom:10px; }
button { padding:10px 15px; background:#1abc9c; border:none; color:#fff; border-radius:6px; cursor:pointer; }
button:hover { opacity:0.8; }
.table-box { margin-top:25px; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th,td { padding:10px; background:#fff; color:#000; border-bottom:1px solid #ccc; }
th { background:#3498db; color:#fff; }
a.delete-btn { color:red; font-weight:bold; text-decoration:none; }
</style>
</head>
<body style="font-family:Segoe UI;background:linear-gradient(90deg,#74617c,#3498db);color:#fff;padding:25px">

<div style="max-width:1100px;margin:auto;">
    <h2>Manage Subjects</h2>
    <a href="admin_dashboard.php" style="color:#fff">← Back</a>

    <?php if ($error) echo "<div style='background:#c0392b;padding:10px;border-radius:6px;margin-top:10px;'>".htmlspecialchars($error)."</div>"; ?>
    <?php if ($success) echo "<div style='background:#2ecc71;padding:10px;border-radius:6px;margin-top:10px;color:#000;'>".htmlspecialchars($success)."</div>"; ?>

    <h3 style="margin-top:20px;">Add / Edit Subject</h3>
    <form method="post">
        <input type="hidden" name="subject_id" id="subject_id">
        <input type="text" name="subject_code" placeholder="Subject Code" required>
        <input type="text" name="subject_name" placeholder="Subject Name" required>

        <select name="class_select" id="class_select" required onchange="toggleClassOther(this.value)">
            <option value="">-- Select Class --</option>
            <option value="FYBSC">FYBSC</option>
            <option value="SYBSC">SYBSC</option>
            <option value="TYBSC">TYBSC</option>
            <option value="OTHER">Other</option>
        </select>
        <input type="text" name="class_other" id="class_other" placeholder="Enter Class" style="display:none;">

        <select name="semester" required>
            <option value="">-- Select Semester --</option>
            <option value="SEM 1">SEM 1</option>
            <option value="SEM 2">SEM 2</option>
            <option value="SEM 3">SEM 3</option>
            <option value="SEM 4">SEM 4</option>
            <option value="SEM 5">SEM 5</option>
            <option value="SEM 6">SEM 6</option>
        </select>

        <select name="teacher_id" required>
            <option value="">-- Assign Teacher --</option>
            <?php foreach ($teachers as $t): ?>
                <option value="<?= $t['roll_no']; ?>"><?= $t['name']; ?> (<?= $t['roll_no']; ?>)</option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="save_subject">Save Subject</button>
    </form>

    <div class="table-box">
        <h3>All Subjects</h3>
        <?php if (empty($subjects)) echo "<p>No subjects found.</p>"; ?>
        <table>
            <tr>
                <th>ID</th><th>Code</th><th>Name</th><th>Class</th><th>Semester</th><th>Teacher</th><th>Action</th>
            </tr>
            <?php foreach ($subjects as $s): ?>
            <tr>
                <td><?= $s['id']; ?></td>
                <td><?= $s['subject_code']; ?></td>
                <td><?= $s['subject_name']; ?></td>
                <td><?= $s['class']; ?></td>
                <td><?= $s['semester']; ?></td>
                <td><?= $s['teacher_name'] ?? '—'; ?></td>
                <td><a class="delete-btn" href="?delete=<?= $s['id']; ?>" onclick="return confirm('Delete this subject?')">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php ?>
    </div>
</div>

<script>
function toggleClassOther(val){
    document.getElementById('class_other').style.display = (val === "OTHER") ? "block" : "none";
}
</script>

</body>
</html>
