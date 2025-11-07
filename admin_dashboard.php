<?php
session_start();
require_once 'config.php';

// Ensure only Admin can access this page
if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

$error = $success = "";
$teachers = []; // Initialize array for subject assignment dropdown


//  Add User (Teacher or Student)

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $role = $_POST["role"];
    $roll_no = trim($_POST["roll_no"]);
    $name = trim($_POST["name"]);
    $password = trim($_POST["password"]);
    $user_role_char = strtoupper(substr($role, 0, 1));
    
    // Student-specific fields
    $class = isset($_POST['class']) ? trim($_POST['class']) : '';
    $stream = isset($_POST['stream']) ? trim($_POST['stream']) : '';
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
    
    // ⚠️ WARNING: Storing plain text password as requested.
    $plain_password = $password; 

    if (empty($role) || empty($roll_no) || empty($name) || empty($password)) {
        $error = "All fields are required!";
    } elseif ($user_role_char === 'S' && (empty($class) || empty($stream) || empty($semester))) {
        // Validation for Student role
        $error = "Class, Stream, and Semester are required for Students!";
    } else {
        try {
            // Start transaction to ensure both inserts succeed
            $pdo->beginTransaction();

            // A. INSERT INTO USERS TABLE (Core Login Record)
            $sql_user = "INSERT INTO users (roll_no, name, password, user_role) 
                         VALUES (:roll_no, :name, :password, :role_char)";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([
                ':roll_no' => $roll_no,
                ':name' => $name,
                ':password' => $plain_password, 
                ':role_char' => $user_role_char
            ]);

            // B. INSERT INTO DETAIL TABLE (teachers or students)
            if ($user_role_char === 'T') {
                $sql_detail = "INSERT INTO teachers (roll_no) VALUES (:roll_no)";
                $stmt_detail = $pdo->prepare($sql_detail);
                $stmt_detail->execute([':roll_no' => $roll_no]);
                
            } elseif ($user_role_char === 'S') {
                $sql_detail = "INSERT INTO students (roll_no, class, stream, semester) 
                                 VALUES (:roll_no, :class, :stream, :semester)";
                $stmt_detail = $pdo->prepare($sql_detail);
                $stmt_detail->execute([
                    ':roll_no' => $roll_no,
                    ':class' => $class,
                    ':stream' => $stream,
                    ':semester' => $semester
                ]);
            }
            
            $pdo->commit();
            $success = "$role added successfully!";

        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') {
                $error = "Error: Roll number '$roll_no' already exists.";
            } else {
                $error = "Error adding user: " . $e->getMessage();
            }
        }
    }
}
// Add Subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_subject"])) {
    $code = filter_input(INPUT_POST, 'subject_code', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'subject_name', FILTER_SANITIZE_STRING);
    $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING); 
    $stream = filter_input(INPUT_POST, 'stream', FILTER_SANITIZE_STRING); 
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);

    if (empty($code) || empty($name) || empty($class) || empty($stream) || empty($teacher_id)) {
        $error = "Error: All subject fields (code, name, class, stream, teacher) are required!";
    } else {
        try {
            // Insert into the subjects table
            $sql_insert = "INSERT INTO subjects (subject_code, subject_name, class, stream, teacher_id) 
                             VALUES (:code, :name, :class, :stream, :teacher_id)";
            $stmt_insert = $pdo->prepare($sql_insert);
            
            $stmt_insert->execute([
                ':code' => $code,
                ':name' => $name,
                ':class' => $class,
                ':stream' => $stream,
                ':teacher_id' => $teacher_id
            ]);

            $success = "Subject **$code ($name)** added successfully.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = "Error: Subject Code **$code** already exists.";
            } else {
                $error = "Database error: Could not process subject request. " . $e->getMessage();
            }
        }
    }
}

// Fetch Data for Forms/Summary
try {
    // Fetch all teachers for the dropdown list in the Add Subject form
    $sql_teachers = "SELECT t.teacher_id, u.name 
                     FROM teachers t 
                     JOIN users u ON t.roll_no = u.roll_no 
                     ORDER BY u.name";
    $stmt_teachers = $pdo->query($sql_teachers);
    $teachers = $stmt_teachers->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Attendance Summary
    $sql_summary = "
        SELECT s.subject_code, s.subject_name,
               COUNT(a.attendance_id) AS total_records,
               SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) AS total_present,
               ROUND(SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) / COUNT(a.attendance_id) * 100, 1) AS percentage
        FROM attendance a
        JOIN subjects s ON a.subject_code = s.subject_code
        GROUP BY s.subject_code, s.subject_name
        ORDER BY s.subject_code ASC";
    $summary_stmt = $pdo->prepare($sql_summary);
    $summary_stmt->execute();
    $attendance_summary = $summary_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $attendance_summary = [];
    $teachers = [];
    $error .= " | Error fetching system data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Modern Font Stack and Light Background */
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #dcecf6ff; /* Very light, professional background */
            margin: 0; 
            padding: 0; 
            color: #333;
        }
        .container { 
            width: 95%; 
            max-width: 1200px; 
            margin: 40px auto; 
        }

        /* Header and Logout Link */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        h1 { 
            font-size: 32px;
            color: #007bff; /* Primary Blue */
            font-weight: 700;
        }
        .logout-link { 
            text-decoration: none; 
            color: #dc3545; /* Bootstrap Red for danger/logout */
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

        /* Action Buttons */
        .dashboard-actions { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 30px; 
            gap: 20px;
        }
        .action-button { 
            flex-grow: 1;
            padding: 20px 10px; 
            font-size: 18px; 
            font-weight: 600;
            background-color: #007bff; 
            color: white; 
            /* Added styles below to ensure the new <a> tag button looks correct */
            border: none; 
            border-radius: 8px; 
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
            transition: all 0.3s ease;
            text-decoration: none; /* For the new anchor tag */
            text-align: center; /* For the new anchor tag */
            display: flex; /* To vertically center text and ensure equal height */
            align-items: center;
            justify-content: center;
        }
        .action-button:hover { 
            background-color: #0056b3; 
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Card (Content Panel) Styles */
        .card {
            background: #ffffff; 
            padding: 30px; 
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            margin-bottom: 30px;
            border-top: 5px solid #007bff; /* A nice visual stripe */
        }
        .card h2 { 
            margin-top: 0;
            margin-bottom: 25px; 
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        /* Form Controls */
        label { 
            display: block; 
            margin-top: 15px; 
            margin-bottom: 5px; 
            font-weight: 600; 
            color: #555;
        }
        input:not(.action-button), select { 
            width: 100%; 
            padding: 10px 12px; 
            margin-bottom: 10px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box;
            font-size: 16px;
        }
        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        /* Form Button (for submit) */
        .btn-form-submit {
             background-color: #28a745; /* Green for successful action */
             color: white; 
             padding: 12px 20px; 
             border: none; 
             border-radius: 5px; 
             cursor: pointer; 
             margin-top: 20px;
             width: 100%;
             font-size: 18px;
             font-weight: 600;
             transition: background-color 0.3s ease;
        }
        .btn-form-submit:hover {
            background-color: #1e7e34;
        }

        /* Table Styling (Summary) */
        table { 
            width: 100%; 
            border-collapse: separate; /* Use separate for rounded corners on cells */
            border-spacing: 0;
            margin-top: 20px; 
            border-radius: 8px;
            overflow: hidden; /* Ensures borders/corners are respected */
        }
        th, td { 
            padding: 12px; 
            text-align: center; 
            border-bottom: 1px solid #eee; 
        }
        th { 
            background-color: #007bff; 
            color: white; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; /* Zebra striping */
        }
        tr:last-child td {
            border-bottom: none;
        }
        .highlight-percent {
            font-weight: 700;
            color: #28a745; /* Green for good performance */
        }

        /* Alert Messages */
        .alert { 
            padding: 15px; 
            border-radius: 8px; 
            text-align: center; 
            margin-bottom: 25px; 
            font-weight: 500;
        }
        .alert-success { 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        .alert-danger { 
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        
        /* Toggle Styles */
        .content-panel { 
            display: none; 
        } 
        .hidden-fields { 
            display: none; 
        } 
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CAMS Admin Dashboard</h1>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="dashboard-actions">
            <button class="action-button" onclick="showPanel('user_management_panel')">Add Teacher/Student</button>
            <button class="action-button" onclick="showPanel('subject_management_panel')">Manage Subjects</button>
            <button class="action-button" onclick="showPanel('summary_panel')">Overall Attendance Summary</button>
            
            <a href="admin_defaulters.php" class="action-button">
                View Defaulters & Non-Defaulters List 🚨
            </a>
        </div>

        <div class="card content-panel" id="user_management_panel">
            <h2>➕ Add New User</h2>
            <form method="POST">
                <label>User Role:</label>
                <select name="role" id="user_role_select">
                    <option value="Teacher" <?php echo (isset($_POST['role']) && $_POST['role'] == 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                    <option value="Student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                </select>
                <label>Roll No / Unique ID:</label>
                <input type="text" name="roll_no" required value="<?php echo htmlspecialchars($roll_no ?? ''); ?>" placeholder="e.g., T101 or S2023001">
                <label>Full Name:</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>" placeholder="e.g., Dr. Jane Doe">
                <label>Initial Password:</label>
                <input type="text" name="password" value="12345" required placeholder="Default password is 12345">

                <div id="student_details_fields" class="hidden-fields">
                    <label>Class:</label>
                    <input type="text" name="class" value="<?php echo htmlspecialchars($class ?? ''); ?>" placeholder="e.g., TYBCS">
                    <label>Stream:</label>
                    <input type="text" name="stream" value="<?php echo htmlspecialchars($stream ?? ''); ?>" placeholder="e.g., Science or Arts">
                    <label>Semester:</label>
                    <input type="text" name="semester" value="<?php echo htmlspecialchars($semester ?? ''); ?>" placeholder="e.g., 5 or VI">
                </div>

                <button type="submit" name="add_user" class="btn-form-submit">Create User Account</button>
            </form>
        </div>

        <div class="card content-panel" id="subject_management_panel">
            <h2>📚 Manage Subjects</h2>
            <?php if (empty($teachers)): ?>
                <div class="alert alert-danger">No teachers found in the system. Please add a teacher first before adding a subject.</div>
            <?php else: ?>
                <form method="POST">
                    <label>Subject Code (e.g., CS402):</label>
                    <input type="text" name="subject_code" required placeholder="e.g., CS-301">
                    <label>Subject Name:</label>
                    <input type="text" name="subject_name" required placeholder="e.g., Web Development">
                    <label>Class:</label>
                    <input type="text" name="class" required placeholder="e.g., TYBCS">
                    <label>Stream:</label>
                    <input type="text" name="stream" required placeholder="e.g., Science">
                    <label>Assign Teacher:</label>
                    <select name="teacher_id" required>
                        <option value="">-- Select Teacher --</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>">
                                <?php echo htmlspecialchars($teacher['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="add_subject" class="btn-form-submit">Add New Subject</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="card content-panel" id="summary_panel">
            <h2>📊 Overall Attendance Summary</h2>
            <?php if (!empty($attendance_summary)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Total Records</th>
                            <th>Total Present</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_summary as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_records']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_present']); ?></td>
                                <td class="highlight-percent"><?php echo htmlspecialchars($row['percentage']); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No attendance records found yet to display a summary.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('user_role_select');
            const studentFields = document.getElementById('student_details_fields');
            const panels = document.querySelectorAll('.content-panel');

            // Function to toggle student-specific fields
            function toggleStudentFields() {
                const isStudent = roleSelect.value === 'Student';
                studentFields.classList.toggle('hidden-fields', !isStudent);
                // Make student fields required only if 'Student' is selected
                studentFields.querySelectorAll('input').forEach(input => input.required = isStudent);
            }
            roleSelect.addEventListener('change', toggleStudentFields);
            
            // Function to show the selected panel and hide others
            window.showPanel = function(panelId) {
                panels.forEach(panel => {
                    panel.style.display = 'none';
                });
                document.getElementById(panelId).style.display = 'block';
                
                // Re-run the field toggle when the User Management panel is opened
                if (panelId === 'user_management_panel') {
                    toggleStudentFields();
                }
            }

            // Initially hide all content panels and set student fields visibility
            panels.forEach(panel => panel.style.display = 'none');
            // This ensures that if the page reloads with 'Student' selected, the fields are visible.
            toggleStudentFields();
        });
    </script>
</body>
</html>