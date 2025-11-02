<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'A') redirect('login.php');

// Student summary
$student_summary = $pdo->query("
    SELECT st.roll_no, u.name,
      SUM(a.status = 'P') AS present,
      COUNT(a.id) AS total,
      ROUND(100 * SUM(a.status = 'P') / NULLIF(COUNT(a.id),0),2) AS percent
    FROM students st
    JOIN users u ON st.roll_no = u.roll_no
    LEFT JOIN attendance a ON a.student_roll = st.roll_no
    GROUP BY st.roll_no, u.name
    ORDER BY percent DESC
")->fetchAll();

// Subject summary
$subject_summary = $pdo->query("
    SELECT sub.id, sub.subject_code, sub.subject_name,
      SUM(a.status = 'P') AS present,
      COUNT(a.id) AS total,
      ROUND(100 * SUM(a.status = 'P') / NULLIF(COUNT(a.id),0),2) AS percent
    FROM subjects sub
    LEFT JOIN attendance a ON a.subject_id = sub.id
    GROUP BY sub.id, sub.subject_code, sub.subject_name
    ORDER BY sub.id
")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Attendance Summary</title></head>
<body style="font-family:Segoe UI;background:linear-gradient(90deg,#74617c,#3498db);color:#fff;padding:20px">
<div style="max-width:1100px;margin:0 auto">
<h2>Attendance Summary</h2>
<h3>Per Student</h3>
<table border="0" cellpadding="8" style="width:100%;background:rgba(0,0,0,0.12);padding:8px;border-radius:8px">
<tr><th>Roll</th><th>Name</th><th>Present</th><th>Total</th><th>%</th></tr>
<?php foreach($student_summary as $r): ?>
<tr>
<td><?php echo htmlspecialchars($r['roll_no']); ?></td>
<td><?php echo htmlspecialchars($r['name']); ?></td>
<td><?php echo $r['present'] ?? 0; ?></td>
<td><?php echo $r['total'] ?? 0; ?></td>
<td><?php echo $r['percent'] ?? 0; ?>%</td>
</tr>
<?php endforeach; ?>
</table>

<h3>Per Subject</h3>
<table border="0" cellpadding="8" style="width:100%;background:rgba(0,0,0,0.12);padding:8px;border-radius:8px;margin-top:12px">
<tr><th>ID</th><th>Code</th><th>Name</th><th>Present</th><th>Total</th><th>%</th></tr>
<?php foreach($subject_summary as $s): ?>
<tr>
<td><?php echo intval($s['id']); ?></td>
<td><?php echo htmlspecialchars($s['subject_code']); ?></td>
<td><?php echo htmlspecialchars($s['subject_name']); ?></td>
<td><?php echo $s['present'] ?? 0; ?></td>
<td><?php echo $s['total'] ?? 0; ?></td>
<td><?php echo $s['percent'] ?? 0; ?>%</td>
</tr>
<?php endforeach; ?>
</table>

<p><a href="admin_dashboard.php" style="color:#fff">← Back</a></p>
</div></body></html>
