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
/* ============================================= */
/* GLOBAL RESET + BACKGROUND */
/* ============================================= */
body {
    font-family: 'Poppins', 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6f9;          /* Light academic background */
    color: #1f2937;               /* Dark text for readability */
}

/* CONTAINER */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* ============================================= */
/* HEADER – University Look */
/* ============================================= */
header {
    background: #1a4b84;          /* University Blue */
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    position: sticky;
    top: 0;
    z-index: 1000;
}

header h1 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 1px;
}

/* NAVIGATION */
nav ul.nav-links {
    list-style: none;
    display: flex;
    gap: 2rem;
    margin: 0;
    padding: 0;
}

nav ul.nav-links li a {
    text-decoration: none;
    font-weight: 500;
    color: #e5e7eb;
    font-size: 1rem;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    transition: 0.3s ease;
}

nav ul.nav-links li a:hover,
nav ul.nav-links li a.active {
    background: #245fa6;          /* brighter hover blue */
    color: #fff;
}

/* ============================================= */
/* HERO – Official Academic Feel */
/* ============================================= */
.hero {
    margin-top: 4rem;
    text-align: center;
}

.hero h2 {
    font-size: 2.7rem;
    font-weight: 800;
    color: #1a4b84;
}

.hero p {
    font-size: 1.15rem;
    max-width: 800px;
    margin: 1rem auto 3rem;
    color: #4b5563;
}

/* ============================================= */
/* FEATURE CARDS – Clean & Professional */
/* ============================================= */
.features {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.feature-card {
    flex: 1 1 300px;
    background: #ffffff;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    transition: 0.3s ease;
    text-align: center;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.feature-card h3 {
    color: #1a4b84;
    font-size: 1.4rem;
    margin-bottom: 1rem;
}

/* ============================================= */
/* LOGIN BUTTONS – Modern Academic */
/* ============================================= */
.login-buttons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 3.5rem;
    flex-wrap: wrap;
}

.login-btn {
    background: #1a4b84;
    color: #fff;
    padding: 0.9rem 2rem;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 8px;
    transition: 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.login-btn:hover {
    background: #245fa6;
    transform: translateY(-3px);
}

/* ============================================= */
/* FOOTER */
/* ============================================= */
footer {
    text-align: center;
    padding: 1.5rem;
    margin-top: 5rem;
    background: #1a4b84;
    color: white;
    font-size: 0.95rem;
}

/* ============================================= */
/* RESPONSIVE */
/* ============================================= */
@media (max-width: 768px) {
    header {
        flex-direction: column;
        gap: 1rem;
    }
    .hero h2 {
        font-size: 2.2rem;
    }
    .features {
        flex-direction: column;
        align-items: center;
    }
    .login-btn {
        width: 100%;
        text-align: center;
    }
}

</style></head>
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
