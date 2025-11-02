<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'A') redirect('login.php');
$error=''; $success='';
// fetch teachers
$teachers = $pdo->query("SELECT roll_no,name FROM users WHERE user_role='T' ORDER BY name")->fetchAll();
// add/update/delete logic (same as earlier final full file)
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_subject'])) {
    $id = !empty($_POST['subject_id']) ? intval($_POST['subject_id']) : null;
    $code = trim($_POST['subject_code'] ?? '');
    $name = trim($_POST['subject_name'] ?? '');
    $class = (trim($_POST['class_select'] ?? '')==='OTHER') ? trim($_POST['class_other'] ?? '') : trim($_POST['class_select'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    $teacher = trim($_POST['teacher_id'] ?? '');
    if ($code===''||$name===''||$class===''||$semester===''||$teacher==='') $error='All fields required.';
    else {
        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE subjects SET subject_code=:code,subject_name=:name,class=:class,semester=:sem,teacher_id=:t WHERE id=:id");
                $stmt->execute([':code'=>$code,':name'=>$name,':class'=>$class,':sem'=>$semester,':t'=>$teacher,':id'=>$id]);
                $success="Subject updated.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO subjects (subject_code,subject_name,class,semester,teacher_id) VALUES (:code,:name,:class,:sem,:t)");
                $stmt->execute([':code'=>$code,':name'=>$name,':class'=>$class,':sem'=>$semester,':t'=>$teacher]);
                $success="Subject added.";
            }
        } catch (PDOException $e) { $error="DB error: ".$e->getMessage(); }
    }
}
if (isset($_GET['delete'])) {
    $did = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM subjects WHERE id=:id")->execute([':id'=>$did]);
    $success = "Subject deleted.";
}
$subjects = $pdo->query("SELECT s.*, u.name AS teacher_name FROM subjects s LEFT JOIN users u ON s.teacher_id = u.roll_no ORDER BY s.id ASC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Subjects</title></head><body style="font-family:Segoe UI;background:linear-gradient(90deg,#74617c,#3498db);color:#fff;padding:20px">
<div style="max-width:1100px;margin:0 auto">
  <h2>Manage Subjects</h2>
  <?php if ($error) echo "<div style='background:#c0392b;padding:8px;border-radius:6px'>".htmlspecialchars($error)."</div>"; ?>
  <?php if ($success) echo "<div style='background:#2ecc71;padding:8px;border-radius:6px;color:#052d12'>".htmlspecialchars($success)."</div>"; ?>
  <!-- form & list (use the full UI file you accepted earlier if you prefer) -->
  <a href="admin_dashboard.php" style="color:#fff">← Back</a>
</div>
</body></html>
