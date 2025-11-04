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
  background: linear-gradient(to top right, #83c4f5ff 0%, #ffffffff 100%);
  color: #333;
}

/* HEADER */
header {
  background: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

header h1 {
  margin: 0;
  font-size: 1.8rem;
  color: #007bff;
  font-weight: 700;
}

nav ul.nav-links {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  gap: 1rem;
}

nav ul.nav-links li a {
  text-decoration: none;
  font-weight: 600;
  padding: 0.4rem 0.8rem;
  color: #007bff;
  border-radius: 6px;
  transition: 0.3s;
}

nav ul.nav-links li a:hover,
nav ul.nav-links li a.active {
  background-color: #007bff;
  color: white;
}

/* CONTAINER */
.container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* HERO TEXT */
.hero h2 {
  font-size: 2rem;
  margin-bottom: 0.8rem;
  color: #1a1a1a;
}

.hero p {
  font-size: 1.1rem;
  color: #444;
  margin-bottom: 1.5rem;
}

/* FEATURE CARDS - GLASSMORPHIC */
.features {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.feature-card {
  flex: 1 1 30%;
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(14px);
  padding: 1.5rem;
  border-radius: 14px;
  text-align: center;
  box-shadow: 0 12px 40px rgba(0, 50, 100, 0.18);
  transition: all 0.35s ease;
  color: #1a1a1a;
}

.feature-card:hover {
  background:rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(18px);
  transform: translateY(-8px);
  box-shadow: 0 16px 50px rgba(0, 50, 100, 0.25);
}

.feature-card h3 {
  color: #007bff;
  margin-bottom: 0.8rem;
}

/* LOGIN BUTTONS */
.login-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 2.5rem;
  flex-wrap: wrap;
}

.login-btn {
  background: #007bff;
  color: white;
  text-decoration: none;
  font-weight: 700;
  padding: 0.9rem 1.6rem;
  border-radius: 10px;
  transition: 0.25s ease-in-out;
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.login-btn:hover {
  background: #0056b3;
  transform: translateY(-4px);
  box-shadow: 0 8px 18px rgba(0, 123, 255, 0.42);
}

/* FOOTER */
footer {
  text-align: center;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.45);
  margin-top: 3rem;
  font-weight: 600;
  color: #007bff;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

/* RESPONSIVE */
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
