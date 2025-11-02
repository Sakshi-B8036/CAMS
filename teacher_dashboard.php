<?php
session_start();
require_once 'config.php'; 

// Ensure the user is a logged-in teacher
if (!isLoggedIn() || $_SESSION["user_role"] !== 'T') {
    redirect('login.php');
}

$teacher_roll = $_SESSION["roll_no"]; 
$teacher_name = $_SESSION["name"]; 
$subjects = []; 
$error = "";

try {
    // 1️⃣ Get teacher_id using the teacher's roll number
    $sql_teacher = "SELECT teacher_id FROM teachers WHERE roll_no = :roll_no";
    $stmt_teacher = $pdo->prepare($sql_teacher);
    $stmt_teacher->bindParam(':roll_no', $teacher_roll, PDO::PARAM_STR);
    $stmt_teacher->execute();
    $teacher_id = $stmt_teacher->fetchColumn();

    if ($teacher_id) {
        // 2️⃣ Fetch subjects assigned to this teacher
        $sql_subjects = "SELECT subject_code, subject_name, class, stream FROM subjects WHERE teacher_id = :teacher_id";
        $stmt_subjects = $pdo->prepare($sql_subjects);
        $stmt_subjects->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
        $stmt_subjects->execute();
        $subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = "Teacher record not linked. Please contact admin.";
    }

} catch (PDOException $e) {
    $error = "Database Error: Could not fetch assigned subjects.";
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <style> 
        /* General Styles */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9; /* Light, professional background */
            margin: 0; 
            padding: 0; 
            color: #333;
        }

        /* Wrapper/Card Style */
        .dashboard-wrapper { 
            max-width: 700px; 
            margin: 60px auto; 
            padding: 30px; 
            background: #ffffff; 
            border-radius: 12px; 
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); 
            text-align: center;
        }

        /* Header and Logout */
        .header-nav {
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 { 
            font-size: 24px;
            color: #007bff; /* Primary Blue */
            font-weight: 700;
            margin: 0;
            text-align: left;
        }
        h2 {
            font-size: 20px;
            color: #495057;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .welcome-text {
             font-size: 16px;
             color: #6c757d;
             font-weight: 500;
             display: block;
        }
        .logout-link { 
            text-decoration: none; 
            color: #dc3545; 
            font-weight: 600; 
            padding: 8px 15px;
            border: 1px solid #dc3545;
            border-radius: 5px;
            transition: all 0.2s ease;
        }
        .logout-link:hover {
            background-color: #dc3545;
            color: white;
        }

        /* Form Controls */
        label { 
            display: block; 
            text-align: left;
            margin-top: 15px; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #555;
            font-size: 14px;
        }
        select, input[type="date"] { 
            width: 100%; 
            padding: 10px 15px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
            appearance: none; /* Remove default styling for select in some browsers */
            background-color: #f9f9f9;
        }
        select:focus, input[type="date"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        /* Buttons */
        .btn { 
            padding: 12px 25px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            text-decoration: none; 
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
            width: 100%;
            margin-top: 10px;
        }
        /* Primary Button (Load Class List) */
        .btn-primary { 
            background-color: #007bff; 
            color: white; 
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .btn-primary:hover { 
            background-color: #0056b3; 
            transform: translateY(-1px);
        }

        /* Secondary Button (View Records) */
        .btn-secondary {
            background-color: #28a745; /* Success Green */
            color: white;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        .btn-secondary:hover {
            background-color: #1e7e34;
            transform: translateY(-1px);
        }

        /* Alerts */
        .alert-danger { 
            color: #721c24; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb;
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center;
        }

        hr {
            border: 0;
            border-top: 1px solid #e0e0e0;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="header-nav">
            <h1>Teacher Dashboard</h1>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
        
        <p class="welcome-text">Welcome, **<?php echo htmlspecialchars($teacher_name); ?>** (ID: <?php echo htmlspecialchars($teacher_roll); ?>)</p>

        <h2>Mark Attendance Session</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($subjects)) : ?>
            <div class="alert-danger">No subjects are currently assigned to you. Please contact the Admin to assign subjects.</div>
        <?php else : ?>
            <form action="mark_attendance.php" method="GET" class="subject-select-form">
                <label for="subject_code">Select Subject & Class:</label>
                <select name="subject_code" id="subject_code" required>
                    <option value="">-- Choose a Subject --</option>
                    <?php foreach ($subjects as $subject) : ?>
                        <option value="<?php echo htmlspecialchars($subject['subject_code']); ?>">
                            <?php echo htmlspecialchars($subject['subject_name']); ?> 
                            (<?php echo htmlspecialchars($subject['subject_code']); ?>) - 
                            <?php echo htmlspecialchars($subject['class']); ?>/<?php echo htmlspecialchars($subject['stream']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="session_date">Session Date:</label>
                <input type="date" name="session_date" id="session_date" value="<?php echo date('Y-m-d'); ?>" required>

                <input type="submit" value="Load Class List & Mark Attendance" class="btn btn-primary">
            </form>
        <?php endif; ?>
        
        <hr>

        <a href="view_attendance.php" 
           class="btn btn-secondary"
           style="width: auto;">
           📅 View Attendance Records & Reports
        </a>
    </div>
</body>
</html>