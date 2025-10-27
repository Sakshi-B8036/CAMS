<?php
require_once 'config.php';

// Ensure only teachers can access (you might restrict this to Admin later)
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

$error = "";
$success = "";
$teachers = [];

try {
    // 1. Fetch all teachers for the dropdown list
    $sql_teachers = "SELECT t.teacher_id, u.name 
                     FROM teachers t 
                     JOIN users u ON t.roll_no = u.roll_no 
                     ORDER BY u.name";
    $stmt_teachers = $pdo->query($sql_teachers);
    $teachers = $stmt_teachers->fetchAll(PDO::FETCH_ASSOC);

    // 2. Process Form Submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Sanitize input
        $code = filter_input(INPUT_POST, 'subject_code', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'subject_name', FILTER_SANITIZE_STRING);
        $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
        $stream = filter_input(INPUT_POST, 'stream', FILTER_SANITIZE_STRING);
        $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);

        if (empty($code) || empty($name) || empty($class) || empty($stream) || empty($teacher_id)) {
            $error = "Error: All fields are required.";
        } else {
            // Insert into the subjects table
            $sql_insert = "INSERT INTO subjects (subject_code, subject_name, class, stream, teacher_id) 
                           VALUES (:code, :name, :class, :stream, :teacher_id)";
            $stmt_insert = $pdo->prepare($sql_insert);
            
            $stmt_insert->bindParam(':code', $code);
            $stmt_insert->bindParam(':name', $name);
            $stmt_insert->bindParam(':class', $class);
            $stmt_insert->bindParam(':stream', $stream);
            $stmt_insert->bindParam(':teacher_id', $teacher_id);
            $stmt_insert->execute();

            $success = "Subject **$code ($name)** added successfully.";
        }
    }

} catch (PDOException $e) {
    // Handle duplicate subject code error (23000) or other DB errors
    if ($e->getCode() === '23000') {
        $error = "Error: Subject Code **$code** already exists.";
    } else {
        $error = "Database error: Could not process request. " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Subject</title>
    <link rel="stylesheet" href="style.css">
    <style> /* Add styling if necessary to match your site's look */
        .container { max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        input[type="text"], select { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; }
        .alert-success { color: green; margin-bottom: 15px; }
        .alert-danger { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Subject</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (empty($teachers)): ?>
            <div class="alert-danger">No teachers found in the system. Cannot add subject.</div>
        <?php else: ?>

            <form action="add_subject.php" method="POST">
                
                <label for="subject_code">Subject Code (e.g., CS402):</label>
                <input type="text" id="subject_code" name="subject_code" required>
                
                <label for="subject_name">Subject Name:</label>
                <input type="text" id="subject_name" name="subject_name" required>
                
                <label for="class">Class (e.g., Sem-2):</label>
                <input type="text" id="class" name="class" required>
                
                <label for="stream">Stream (e.g., CS):</label>
                <input type="text" id="stream" name="stream" required>
                
                <label for="teacher_id">Assign Teacher:</label>
                <select id="teacher_id" name="teacher_id" required>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>">
                            <?php echo htmlspecialchars($teacher['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn">Add Subject</button>
                
                <a href="teacher_dashboard.php" style="display: block; margin-top: 15px;">← Back to Dashboard</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>