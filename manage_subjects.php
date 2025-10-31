<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') redirect('login.php');

$message = "";

// Add subject
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];
    $teacher_id = $_POST['teacher_id'];

    try {
        $sql = "INSERT INTO subjects (subject_code, subject_name, teacher_id)
                VALUES (:code, :name, :teacher_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':code' => $subject_code,
            ':name' => $subject_name,
            ':teacher_id' => $teacher_id
        ]);
        $message = "✅ Subject added successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

$teachers = $pdo->query("SELECT teacher_id, roll_no FROM teachers")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $pdo->query("SELECT * FROM subjects")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .container { width: 700px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #007bff; color: white; }
        input, select { width: 100%; padding: 8px; margin: 8px 0; }
        button { background: #007bff; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h2>📚 Manage Subjects</h2>
    <?php if ($message) echo "<p>$message</p>"; ?>

    <form method="POST">
        <input type="text" name="subject_code" placeholder="Subject Code" required>
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <select name="teacher_id" required>
            <option value="">Assign Teacher</option>
            <?php foreach ($teachers as $t): ?>
                <option value="<?php echo $t['teacher_id']; ?>"><?php echo $t['roll_no']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Subject</button>
    </form>

    <h3>All Subjects</h3>
    <table>
        <thead>
            <tr><th>Code</th><th>Name</th><th>Teacher ID</th></tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['subject_code']); ?></td>
                    <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['teacher_id']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
