<?php
require_once "config.php";

// Allow only Admin to access this page
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

$messages = [];
$error = "";

try {
    $sql = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Messages | Admin</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #74617c, #3498db);
    color: white;
    margin: 0;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    background: rgba(0,0,0,0.3);
    padding: 20px;
    border-radius: 10px;
}
h2 {
    text-align: center;
    color: #fff;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table th, table td {
    padding: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.3);
}
table th {
    background: rgba(0,0,0,0.4);
    font-weight: bold;
}
tr:hover {
    background: rgba(0,0,0,0.2);
}
.btn-back {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 18px;
    background: #1abc9c;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
.btn-back:hover {
    background: #148f77;
}
.no-data {
    text-align: center;
    padding: 20px;
    font-size: 18px;
}
</style>
</head>
<body>

<div class="container">
    <h2>📩 Contact Form Messages</h2>

    <?php if (!empty($error)): ?>
        <p style="color: #ff4d4d; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (empty($messages)): ?>
        <p class="no-data">No messages received yet.</p>
    <?php else: ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Date</th>
        </tr>

        <?php foreach ($messages as $msg): ?>
            <tr>
                <td><?php echo htmlspecialchars($msg['id']); ?></td>
                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                <td><?php echo htmlspecialchars($msg['submitted_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
</div>

</body>
</html>
