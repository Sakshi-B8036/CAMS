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
/* ---------------------------------- */
/* GLOBAL RESET & BACKGROUND */
/* ---------------------------------- */
body {
    /* Deep Indigo/Charcoal background for authority and contrast */
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    /* Deep, professional dark blue/purple gradient */
    background: linear-gradient(145deg, #181133 0%, #2f255c 100%); 
    color: #ffffff; 
    min-height: 100vh;
    overflow-x: hidden;
}

/* CONTAINER */
.container {
    max-width: 1200px; 
    margin: 0 auto;
    padding: 0 2rem;
}

/* ---------------------------------- */
/* HEADER & NAVIGATION (Structured) */
/* ---------------------------------- */
header {
    background: #181133; /* Solid, dark background for stability */
    border-bottom: 3px solid #8a2be2; /* Vivid Violet/Purple Accent border */
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    position: sticky;
    top: 0;
    z-index: 1000;
}

header h1 {
    margin: 0;
    font-size: 1.8rem; 
    color: #8a2be2; /* Main Violet Accent Color */
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* NAVIGATION LINKS */
nav ul.nav-links {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 2rem;
}

nav ul.nav-links li a {
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    color: #c5c1e0; /* Light lavender/gray for readability */
    border-radius: 4px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

nav ul.nav-links li a:hover,
nav ul.nav-links li a.active {
    background: #8a2be2; 
    color: #fff;
    border: 1px solid #8a2be2;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(138, 43, 226, 0.4);
}

/* ---------------------------------- */
/* MAIN CONTENT & HERO (Authoritative) */
/* ---------------------------------- */
main.container {
    padding-top: 5rem;
    padding-bottom: 5rem;
}

/* HERO TEXT */
.hero h2 {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    color: #ffffff;
    text-align: center;
    font-weight: 900;
    line-height: 1.2;
}
/* Highlight the key phrase in the heading */
.hero h2 strong {
    color: #8a2be2;
}

.hero p {
    font-size: 1.2rem;
    color: #a0a0ff; /* Light purple for body text */
    text-align: center;
    max-width: 900px;
    margin: 0 auto 5rem auto;
    font-weight: 300;
}

/* ---------------------------------- */
/* FEATURE CARDS (Clean and Purposeful) */
/* ---------------------------------- */
.features {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.feature-card {
    flex: 1 1 300px;
    background: #2f255c; /* Card background is slightly lighter than body */
    border: 1px solid #4a3b83; /* Dark purple border */
    padding: 2.5rem;
    border-radius: 8px; 
    text-align: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3); 
    transition: all 0.4s ease-in-out;
}

.feature-card:hover {
    background: #3c3070; 
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
    border-color: #8a2be2; /* Accent border on hover */
}

.feature-card h3 {
    color: #8a2be2; /* Main accent color */
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 700;
}

.feature-card p {
    color: #c5c1e0; 
    font-weight: 300;
}

/* ---------------------------------- */
/* LOGIN BUTTONS (Clear Call-to-Action) */
/* ---------------------------------- */
.login-buttons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 5rem;
    flex-wrap: wrap;
}

.login-btn {
    background: #8a2be2; /* Main accent CTA color */
    color: #fff; 
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.9rem 2rem;
    border-radius: 6px;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 6px 15px rgba(138, 43, 226, 0.5);
    border: 1px solid transparent;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.login-btn:hover {
    background: #7a1ee0; /* Slightly darker purple on hover */
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(138, 43, 226, 0.7);
}

/* ---------------------------------- */
/* FOOTER */
/* ---------------------------------- */
footer {
    text-align: center;
    padding: 1.5rem;
    background: #181133;
    margin-top: 5rem;
    font-weight: 400;
    color: #a0a0ff;
    border-top: 1px solid #2f255c;
}

/* ---------------------------------- */
/* RESPONSIVE */
/* ---------------------------------- */
@media (max-width: 900px) {
    .hero h2 {
        font-size: 2.5rem;
    }
    .features {
        gap: 2rem;
    }
}
@media (max-width: 700px) {
    header {
        flex-direction: column;
        gap: 0.8rem;
    }
    .hero h2 {
        font-size: 2rem;
    }
    .features {
        flex-direction: column;
        align-items: center;
    }
    .feature-card {
        flex: 1 1 100%;
        max-width: 90%;
        margin: 0 auto;
    }
    .login-buttons {
        flex-direction: column;
        gap: 1rem;
        padding: 0 1.5rem;
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
