<?php
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
        $error = "⚠️ Please enter a valid email address.";
    } else {
        try {
            $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);
            $success = "✅ Message sent successfully! We’ll get back to you soon.";
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
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #74617c, #3498db);
      color: #fff;
    }

    header {
      background: rgba(0, 0, 0, 0.3);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    header h1 {
      margin: 0;
      font-size: 1.8rem;
      color: #4d8bc8;
    }

    nav ul.nav-links {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    nav ul.nav-links li a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 0.4rem 0.8rem;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    nav ul.nav-links li a:hover,
    nav ul.nav-links li a.active {
      background-color: #1abc9c;
      color: white;
    }

    .container {
      max-width: 700px;
      margin: 2rem auto;
      background: rgba(0, 0, 0, 0.3);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    h2 {
      text-align: center;
      color: #fff;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input, textarea {
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      width: 100%;
      box-sizing: border-box;
    }

    input:focus, textarea:focus {
      outline: 2px solid #1abc9c;
      background-color: #f9f9f9;
      color: #000;
    }

    button {
      background: #1abc9c;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #148f77;
    }

    .alert {
      text-align: center;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      font-weight: 600;
    }

    .alert-success {
      background-color: #2ecc71;
      color: white;
    }

    .alert-danger {
      background-color: #e74c3c;
      color: white;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background: rgba(0, 0, 0, 0.3);
      color: white;
      margin-top: 3rem;
    }

    footer p:hover {
      cursor: pointer;
      color: #4d8bc8;
    }

    @media (max-width: 700px) {
      nav ul.nav-links {
        flex-direction: column;
        gap: 0.5rem;
      }
      header {
        flex-direction: column;
        gap: 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>CAMS Portal</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="team.php">Our Team</a></li>
        <li><a href="contact.php" class="active">Contact</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <h2>📬 Contact Us</h2>
    <p style="text-align:center;">We’d love to hear from you! Fill out the form below, and our team will respond as soon as possible.</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="contact.php" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" rows="5" placeholder="Your Message..." required></textarea>
      <button type="submit">Send Message</button>
    </form>
  </div>

  <footer>
    <p>© 2025 CAMS | College Attendance Management System | All Rights Reserved</p>
  </footer>
</body>
</html>
