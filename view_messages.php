<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'A') redirect('login.php');
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Messages</title></head>
<body style="font-family:Segoe UI;background:linear-gradient(90deg,#74617c,#3498db);color:#fff;padding:20px">
<div style="max-width:1000px;margin:0 auto">
<h2>Contact Messages</h2>
<?php if (empty($messages)): ?><p>No messages found.</p><?php else: ?>
<table border="0" cellpadding="8" style="background:rgba(0,0,0,0.12);padding:8px;border-radius:8px;width:100%">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th></tr>
<?php foreach($messages as $m): ?>
<tr>
<td><?php echo $m['id']; ?></td>
<td><?php echo htmlspecialchars($m['name']); ?></td>
<td><?php echo htmlspecialchars($m['email']); ?></td>
<td><?php echo htmlspecialchars($m['subject']); ?></td>
<td><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
<td><?php echo $m['created_at']; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<p><a href="admin_dashboard.php" style="color:#fff">← Back</a></p>
</div></body></html>
