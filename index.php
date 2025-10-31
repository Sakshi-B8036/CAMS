<?php
// Redirect logged-in users to their dashboards
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    switch ($_SESSION["user_role"]) {
        case 'A':
            header("location: admin_dashboard.php");
            break;
        case 'T':
            header("location: teacher_dashboard.php");
            break;
        case 'S':
            header("location: student_dashboard.php");
            break;
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to CAMS</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #74617c, #3498db);
      color: #333;
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
      max-width: 900px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .hero {
      text-align: center;
      margin-top: 2rem;
      animation: fadeIn 1.5s ease-in;
    }

    .hero h2 {
      font-size: 2rem;
      margin-bottom: 0.8rem;
      color: #fff;
    }

    .hero p {
      font-size: 1.1rem;
      color: #f1f1f1;
      margin-bottom: 1.5rem;
      animation: slideUp 2s ease;
    }

    /* Interactive Feature Cards */
    .features {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      margin-top: 2rem;
      flex-wrap: wrap;
    }

    .feature-card {
      flex: 1 1 30%;
      background: rgba(0, 0, 0, 0.3);
      padding: 1.5rem;
      border-radius: 10px;
      color: #fff;
      text-align: center;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      transform: translateY(0);
    }

    .feature-card:hover {
      transform: translateY(-8px);
      background: #1abc9c;
      color: white;
    }

    .feature-card h3 {
      color: #4d8bc8;
      margin-bottom: 0.8rem;
    }

    .feature-card p {
      color: #e6e6e6;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    /* Login Buttons */
    .login-buttons {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 2.5rem;
      flex-wrap: wrap;
    }

    .login-btn {
      background: rgba(0, 0, 0, 0.3);
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .login-btn:hover {
      background: #1abc9c;
      color: white;
      transform: scale(1.05);
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
      .features {
        flex-direction: column;
        gap: 1rem;
      }
      .login-buttons {
        flex-direction: column;
        gap: 0.8rem;
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <header>
    <h1>CAMS Portal</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="team.php">Our Team</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </nav>
  </header>

  <main class="container">
    <!-- Hero Section -->
    <section class="hero">
      <h2>Welcome to College Attendance Management System</h2>
      <p>Streamline attendance tracking with smart automation, insightful analytics, and secure access for all roles.</p>
    </section>

    <!-- Interactive Feature Cards -->
    <section class="features" id="features">
      <div class="feature-card">
        <h3>📅 Smart Attendance</h3>
        <p>Mark, manage, and monitor attendance digitally — no paperwork, no hassle, just a few clicks.</p>
      </div>
      <div class="feature-card">
        <h3>📊 Analytics Dashboard</h3>
        <p>View real-time reports, student performance graphs, and attendance summaries to make informed decisions.</p>
      </div>
      <div class="feature-card">
        <h3>🔐 Role-Based Access</h3>
        <p>Admins, teachers, and students get tailored access with secure authentication for data protection.</p>
      </div>
    </section>

    <!-- Login Buttons -->
    <div class="login-buttons">
      <a href="login.php?role=A" class="login-btn">👑 Admin Login</a>
      <a href="login.php?role=T" class="login-btn">👨‍🏫 Teacher Login</a>
      <a href="login.php?role=S" class="login-btn">🎓 Student Login</a>
    </div>
  </main>

  <footer>
    <p>© 2025 CAMS | College Attendance Management System | All Rights Reserved</p>
  </footer>
</body>
</html>
