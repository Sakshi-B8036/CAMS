<?php
require_once "config.php";

// Admin-only access
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

ini_set('display_errors', 0);
$error = "";
$success = "";

// --- fetch teachers (users with role 'T') ---
$teachers = [];
try {
    $stmt = $pdo->prepare("SELECT roll_no, name FROM users WHERE user_role = 'T' ORDER BY name ASC");
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading teachers: " . $e->getMessage();
}

// --- helper: sanitize POST values ---
function val($k) {
    return isset($_POST[$k]) ? trim($_POST[$k]) : '';
}

// --- Add or Update Subject ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['save_subject']))) {
    $id = isset($_POST['subject_id']) && $_POST['subject_id'] !== '' ? intval($_POST['subject_id']) : null;
    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_name = trim($_POST['subject_name'] ?? '');
    // class: use dropdown value or other_class text if selected
    $class_val = trim($_POST['class_select'] ?? '');
    if ($class_val === 'OTHER') {
        $class_val = trim($_POST['class_other'] ?? '');
    }
    $semester = trim($_POST['semester'] ?? '');
    $teacher_id = trim($_POST['teacher_id'] ?? '');

    // Basic validation
    if ($subject_code === '' || $subject_name === '' || $class_val === '' || $semester === '' || $teacher_id === '') {
        $error = "⚠️ All fields are required. Please fill subject code, name, class, semester and assign a teacher.";
    } else {
        try {
            if ($id) {
                // Update existing subject
                $sql = "UPDATE subjects
                        SET subject_code = :code, subject_name = :name, class = :class, semester = :sem, teacher_id = :teacher
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':code' => $subject_code,
                    ':name' => $subject_name,
                    ':class' => $class_val,
                    ':sem' => $semester,
                    ':teacher' => $teacher_id,
                    ':id' => $id
                ]);
                $success = "✅ Subject updated successfully.";
            } else {
                // Insert new subject
                $sql = "INSERT INTO subjects (subject_code, subject_name, class, semester, teacher_id)
                        VALUES (:code, :name, :class, :sem, :teacher)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':code' => $subject_code,
                    ':name' => $subject_name,
                    ':class' => $class_val,
                    ':sem' => $semester,
                    ':teacher' => $teacher_id
                ]);
                $success = "✅ Subject added successfully.";
            }
            // reload subjects at the end of script (fetch below)
        } catch (PDOException $e) {
            // Handle duplicate code unique error more friendly
            if ($e->getCode() === '23000') {
                $error = "⚠️ Subject code already exists. Use a unique subject code.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// --- Delete subject (uses ON DELETE CASCADE to remove related attendance) ---
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = :id");
        $stmt->execute([':id' => $deleteId]);
        $success = "🗑️ Subject deleted successfully (related attendance removed automatically).";
        // After delete we will reload list below
    } catch (PDOException $e) {
        $error = "Delete failed: " . $e->getMessage();
    }
}

// --- If editing, fetch subject data for prefill ---
$editing = false;
$edit_subject = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $editId]);
        $edit_subject = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($edit_subject) $editing = true;
    } catch (PDOException $e) {
        $error = "Error loading subject: " . $e->getMessage();
    }
}

// --- Fetch all subjects (fresh) ---
$subjects = [];
try {
    $stmt = $pdo->prepare("
        SELECT s.*, u.name AS teacher_name
        FROM subjects s
        LEFT JOIN users u ON s.teacher_id = u.roll_no
        ORDER BY s.id ASC
    ");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading subjects: " . $e->getMessage();
}

// --- Predefined classes for dropdown (you can edit/add) ---
$predefined_classes = [
    "FYBCS",
    "SYBCS",
    "TYBCS(A)",
    "TYBCS(B)"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Manage Subjects | CAMS Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<style>
/* Styling consistent with your project */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: linear-gradient(to right, #74617c, #3498db);
    color: #fff;
}
.container {
    max-width: 1100px;
    margin: 28px auto;
    background: rgba(0,0,0,0.35);
    padding: 22px;
    border-radius: 10px;
}
h1 { color: #fff; margin: 0 0 12px 0; text-align: center; }
.actions { display:flex; gap:12px; justify-content:center; margin-bottom: 18px; flex-wrap:wrap; }
.btn { background:#1abc9c; color:#fff; padding:10px 14px; border-radius:6px; text-decoration:none; display:inline-block; }
.btn:hover { background:#148f77; transform:translateY(-2px); }
.btn-del { background:#e74c3c; color:#fff; padding:8px 10px; border-radius:6px; text-decoration:none; }
.btn-del:hover { background:#c0392b; transform:translateY(-2px); }
.alert { padding:10px; border-radius:6px; margin-bottom:12px; text-align:center; }
.alert-success { background:#2ecc71; color:#052d12; }
.alert-error { background:#f8d7da; color:#721c24; background:#e74c3c; color:white; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:10px; }
@media (max-width:720px) { .form-row { grid-template-columns:1fr; } }
input, select { padding:8px 10px; border-radius:6px; border:none; width:100%; box-sizing:border-box; }
.card { background: rgba(255,255,255,0.03); padding:14px; border-radius:8px; margin-bottom:16px; }
.table { width:100%; border-collapse: collapse; margin-top:8px; }
.table th, .table td { padding:10px; border-bottom:1px solid rgba(255,255,255,0.06); text-align:left; vertical-align:middle; }
.table th { background: rgba(0,0,0,0.3); font-weight:600; }
.small { font-size:0.9rem; color: #e8f6f0; }
.top-row { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px; }
.link-back { text-decoration:none; color:#fff; }
.edit-link { color:#fff; text-decoration: none; background:#007bff; padding:6px 8px; border-radius:6px; }
.edit-link:hover { background:#0056b3; }
</style>
</head>
<body>
<div class="container">
    <h1>📘 Manage Subjects</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Top actions -->
    <div class="top-row">
        <div class="small">Admin: <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></strong></div>
        <div class="actions">
            <a class="btn" href="admin_dashboard.php">← Dashboard</a>
            <a class="btn" href="manage_subjects.php">⟳ Refresh</a>
        </div>
    </div>

    <!-- Add / Edit form -->
    <div class="card">
        <form method="POST" id="subjectForm">
            <input type="hidden" name="subject_id" value="<?php echo $editing ? intval($edit_subject['id']) : ''; ?>">

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <div style="flex:1; min-width:220px;">
                    <label class="small">Subject Code</label>
                    <input type="text" name="subject_code" required value="<?php echo $editing ? htmlspecialchars($edit_subject['subject_code']) : ''; ?>" placeholder="Ex: CS101">
                </div>

                <div style="flex:2; min-width:220px;">
                    <label class="small">Subject Name</label>
                    <input type="text" name="subject_name" required value="<?php echo $editing ? htmlspecialchars($edit_subject['subject_name']) : ''; ?>" placeholder="Ex: Database Management">
                </div>

                <div style="flex:1; min-width:220px;">
                    <label class="small">Semester</label>
                    <input type="text" name="semester" required value="<?php echo $editing ? htmlspecialchars($edit_subject['semester']) : ''; ?>" placeholder="Ex: TYBCS">
                </div>
            </div>

            <div style="margin-top:12px; display:flex; gap:12px; flex-wrap:wrap;">
                <div style="flex:1; min-width:220px;">
                    <label class="small">Class</label>
                    <select name="class_select" id="class_select">
                        <?php foreach ($predefined_classes as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>"
                                <?php
                                    $sel = $editing ? $edit_subject['class'] : '';
                                    if ($sel === $c) echo 'selected';
                                ?>>
                                <?php echo htmlspecialchars($c); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="OTHER" <?php if ($editing && !in_array($edit_subject['class'], $predefined_classes)) echo 'selected'; ?>>Other (type below)</option>
                    </select>
                </div>

                <div style="flex:1; min-width:220px;">
                    <label class="small">If Other — specify class</label>
                    <input type="text" name="class_other" id="class_other" placeholder="Ex: TYBCS(C)" value="<?php
                        if ($editing) {
                            $cl = $edit_subject['class'];
                            if (!in_array($cl, $predefined_classes)) echo htmlspecialchars($cl);
                        }
                    ?>">
                </div>

                <div style="flex:1; min-width:220px;">
                    <label class="small">Assign Teacher</label>
                    <select name="teacher_id" required>
                        <option value="">-- Assign Teacher --</option>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo htmlspecialchars($t['roll_no']); ?>"
                                <?php if ($editing && $edit_subject['teacher_id'] === $t['roll_no']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($t['name'] . ' (' . $t['roll_no'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" name="save_subject" class="btn"><?php echo $editing ? '💾 Update Subject' : '➕ Add Subject'; ?></button>
                <?php if ($editing): ?>
                    <a href="manage_subjects.php" class="btn btn-del" style="background:#6c757d;">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
        <p class="small" style="margin-top:10px;">Tip: Use predefined classes for consistency. Choose <strong>Other</strong> to type a custom class.</p>
    </div>

    <!-- Subject list -->
    <div class="card">
        <h3 style="margin-top:0;">📄 Subjects List</h3>

        <?php if (empty($subjects)): ?>
            <p class="small">No subjects added yet.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Semester</th>
                        <th>Teacher</th>
                        <th style="width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td><?php echo intval($s['id']); ?></td>
                        <td><?php echo htmlspecialchars($s['subject_code']); ?></td>
                        <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['class']); ?></td>
                        <td><?php echo htmlspecialchars($s['semester']); ?></td>
                        <td><?php echo htmlspecialchars($s['teacher_name'] ?: $s['teacher_id']); ?></td>
                        <td>
                            <a class="edit-link" href="manage_subjects.php?edit=<?php echo intval($s['id']); ?>">Edit</a>
                            &nbsp;
                            <a class="btn-del" href="manage_subjects.php?delete=<?php echo intval($s['id']); ?>" onclick="return confirm('Delete this subject? Related attendance will also be deleted.');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<script>
// Toggle display of "other class" field when Other selected
(function(){
    const classSelect = document.getElementById('class_select');
    const classOther = document.getElementById('class_other');
    function updateOther() {
        if (!classSelect) return;
        if (classSelect.value === 'OTHER') {
            classOther.style.background = '#fff';
            classOther.style.color = '#000';
            classOther.required = true;
        } else {
            classOther.required = false;
            // clear only if the selected is not OTHER and the other value equals selected
            // but keep any custom typed value (so editing isn't destructive)
        }
    }
    if (classSelect) {
        classSelect.addEventListener('change', updateOther);
        // initial run
        updateOther();
    }
})();
</script>
</body>
</html>
