<?php
<<<<<<< HEAD
require_once "config.php";

$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "⚠️ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Invalid email address.";
    } else {
        try {
            $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                    VALUES (:name, :email, :subject, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);
            $success = "✅ Message sent successfully!";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us | CAMS</title>

  <style>
/* ============================================= */
/* GLOBAL RESET + BASE */
/* ============================================= */
body {
    font-family: 'Poppins', 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6f9;
    color: #1f2937;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* ============================================= */
/* HEADER */
/* ============================================= */
header {
    background: #1a4b84;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

header h1 {
    color: #fff;
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
}

nav ul {
    list-style: none;
    display: flex;
    gap: 2rem;
    margin: 0;
}

nav a {
    text-decoration: none;
    font-weight: 500;
    color: #e5e7eb;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    transition: 0.3s ease;
}

nav a:hover,
nav .active {
    background: #245fa6;
    color: #fff;
}

/* ============================================= */
/* CONTACT PAGE SECTION */
/* ============================================= */
.contact-section {
    max-width: 700px;
    margin: 4rem auto;
    padding: 3rem;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    animation: fadeIn 0.5s ease-in-out;
}

.contact-section h2 {
    text-align: center;
    color: #1a4b84;
    font-size: 2.4rem;
    margin-bottom: 1rem;
    font-weight: 800;
}

.contact-section p {
    text-align: center;
    color: #4b5563;
    font-size: 1rem;
    margin-bottom: 2rem;
}

/* ============================================= */
/* FORM */
/* ============================================= */
form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

input, textarea {
    width: 100%;
    padding: 0.9rem;
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    font-size: 1rem;
    transition: 0.3s ease;
}

input:focus,
textarea:focus {
    border-color: #1a4b84;
    box-shadow: 0 0 0 3px rgba(26,75,132,0.2);
    outline: none;
}

textarea {
    height: 140px;
    resize: none;
}

/* BUTTON */
button {
    padding: 0.9rem;
    background: #1a4b84;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

button:hover {
    background: #245fa6;
    transform: translateY(-3px);
}

/* ALERT BOXES */
.alert {
    padding: 0.9rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    text-align: center;
    font-weight: 600;
}

.alert-danger {
    background: #ffe5e5;
    color: #b30000;
    border: 1px solid #ffb3b3;
}

.alert-success {
    background: #e6f4ff;
    color: #1a4b84;
    border: 1px solid #b6dcff;
}

/* ANIMATION */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* FOOTER */
footer {
    text-align: center;
    padding: 1.5rem;
    margin-top: 5rem;
    background: #1a4b84;
    color: #fff;
    font-size: 0.95rem;
}

  </style>
</head>

<body>

<header>
  <h1>CAMS Portal</h1>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="team.php">Our Team</a></li>
      <li><a href="contact.php" class="active">Contact</a></li>
    </ul>
  </nav>
</header>

<div class="contact-section">

  <h2>📬 Contact Us</h2>
  <p>Send us a message and our team will get back to you soon.</p>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php elseif (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <input type="text" name="subject" placeholder="Subject" required>
    <textarea name="message" placeholder="Your Message..." required></textarea>
    <button type="submit">Send Message</button>
  </form>
</div>

<footer>
  <p>© 2025 CAMS | College Attendance Management System | All Rights Reserved</p>
</footer>

</body>
</html>
=======
require_once 'config.php';
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name===''||$email===''||$message==='') $error='All fields required.';
    elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Invalid email';
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name,email,subject,message) VALUES (:n,:e,:s,:m)");
            $stmt->execute([':n'=>$name,':e'=>$email,':s'=>$subject,':m'=>$message]);
            $success = "Message sent. Thank you!";
        } catch (PDOException $e) { $error="DB error: ".$e->getMessage(); }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Contact</title></head>
<body style="font-family:Segoe UI;background:linear-gradient(90deg,#74617c,#3498db);color:#fff;padding:30px">
<div style="max-width:700px;margin:0 auto;background:rgba(0,0,0,0.28);padding:20px;border-radius:10px">
<h2>Contact Us</h2>
<?php if ($error) echo "<div style='background:#c0392b;padding:8px;border-radius:6px'>".htmlspecialchars($error)."</div>"; ?>
<?php if ($success) echo "<div style='background:#2ecc71;padding:8px;border-radius:6px;color:#052d12'>".htmlspecialchars($success)."</div>"; ?>
<form method="post">
<input name="name" placeholder="Your name" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px"><br>
<input name="email" placeholder="Your email" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px"><br>
<input name="subject" placeholder="Subject" style="width:100%;padding:8px;margin:6px 0;border-radius:6px"><br>
<textarea name="message" placeholder="Message" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px" rows="6"></textarea><br>
<button style="padding:10px 14px;background:#1abc9c;border:none;border-radius:6px;color:#fff">Send</button>
</form>
<p><a href="index.php" style="color:#fff">← Back to Home</a></p>
</div></body></html>
>>>>>>> 622b4159278408dca9ceac0839687e8dc5e3fb37
