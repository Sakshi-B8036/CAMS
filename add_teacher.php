<?php
require_once 'config.php';
if (!isLoggedIn() || $_SESSION['user_role'] !== 'A') redirect('login.php');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll = trim($_POST['roll_no']);
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $department = trim($_POST['department']);

    if ($roll === '' || $name === '' || $password === '') {
        $error = 'All fields required.';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (roll_no, name, password, user_role) VALUES (:roll, :name, :pwd, 'T')");
            $stmt->execute([':roll'=>$roll, ':name'=>$name, ':pwd'=>$password]);

            $stmt2 = $pdo->prepare("INSERT INTO teachers (roll_no, department) VALUES (:roll, :dept)");
            $stmt2->execute([':roll'=>$roll, ':dept'=>$department]);

            $pdo->commit();
            $success = "Teacher added.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "DB error: ".$e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Teacher | Admin</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #74617c, #3498db);
    color: #fff;
}
.container {
    max-width: 450px;
    margin: 60px auto;
    background: rgba(0,0,0,0.3);
    padding: 25px;
    border-radius: 10px;
}
input, button {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: none;
    border-radius: 5px;
}
button {
    background: #1abc9c;
    color: white;
    font-size: 16px;
    cursor: pointer;
}
.from{
    margin:80px;
}
.back{
    margin-left:200px;
    margin-right:200px;
}
button:hover { background: #148f77; }
.alert { padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: center; }
.alert-danger { background: #e74c3c; }
.alert-success { background: #2ecc71; }
a { color: #fff; text-decoration: none; display: block; text-align: center; margin-top: 10px; }
</style>
</head>
<body>
<h2>Add Teacher</h2>
<?php if ($error) echo "<div style='color:red;'>$error</div>"; if ($success) echo "<div style='color:green;'>$success</div>"; ?>
<form method="post" class="from">
<input name="roll_no" placeholder="T101" required>
<input name="name" placeholder="Teacher Name" required>
<input name="password" placeholder="password" required>
<input name="department" placeholder="Department">
<button type="submit">Add</button>
</form>
<a href="admin_dashboard.php" class="back">Back</a>
</body>
</html>
