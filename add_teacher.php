<?php
require_once "config.php";

if (!isLoggedIn() || $_SESSION["user_role"] !== 'A') {
    redirect('login.php');
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $roll_no = trim($_POST["roll_no"]);
    $name = trim($_POST["name"]);
    $password = trim($_POST["password"]);

    if (empty($roll_no) || empty($name) || empty($password)) {
        $error = "⚠️ All fields are required.";
    } else {
        try {
            $pdo->beginTransaction();

            $sql_user = "INSERT INTO users (roll_no, name, password, user_role)
                         VALUES (:roll, :name, :password, 'T')";
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute([
                ':roll' => $roll_no,
                ':name' => $name,
                ':password' => $password
            ]);

            $pdo->commit();
            $success = "✅ Teacher $name ($roll_no) added successfully.";

        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') {
                $error = "⚠️ Roll Number already exists.";
            } else {
                $error = "Database Error: " . $e->getMessage();
            }
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
button:hover { background: #148f77; }
.alert { padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: center; }
.alert-danger { background: #e74c3c; }
.alert-success { background: #2ecc71; }
a { color: #fff; text-decoration: none; display: block; text-align: center; margin-top: 10px; }
</style>
</head>
<body>

<div class="container">
    <h2>Add New Teacher</h2>

    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

    <form method="POST">
        <input type="text" name="roll_no" placeholder="Teacher ID (Ex. T101)" required>
        <input type="text" name="name" placeholder="Teacher Name" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Add Teacher</button>
        <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
    </form>
</div>

</body>
</html>
