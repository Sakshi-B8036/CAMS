<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Team | CAMS</title>
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
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1rem;
      text-align: center;
      color: white;
    }

    h2 {
      font-size: 2rem;
      color: #fff;
      margin-bottom: 1rem;
    }

    .team-grid {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-top: 2rem;
    }

    .member-card {
      background: rgba(0, 0, 0, 0.3);
      border-radius: 10px;
      padding: 1.5rem;
      width: 280px;
      text-align: center;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }

    .member-card:hover {
      background: #1abc9c;
      transform: translateY(-8px);
    }

    .member-card h3 {
      margin: 0.5rem 0;
      color: #4d8bc8;
    }

    .member-card p {
      font-size: 0.95rem;
      line-height: 1.5;
      color: #f1f1f1;
      margin: 0.3rem 0;
    }

    .role {
      font-weight: bold;
      color: #ffd700;
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
    <h1>CAMS Team</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="team.php" class="active">Our Team</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </nav>
  </header>

  <main class="container">
    <h2>Meet the Project Team</h2>
    <p>Our dedicated team has worked together to design, develop, and deploy the College Attendance Management System (CAMS) — a powerful, efficient, and user-friendly solution for academic institutions.</p>

    <div class="team-grid">
      <!-- Member 1 -->
      <div class="member-card">
        <h3>Shivam Joshi</h3>
        <p><strong>Roll No:</strong> 2472012</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Backend Developer</p>
        <p>Responsible for backend logic, server-side scripting, and database management to ensure smooth system performance.</p>
      </div>

      <!-- Member 2 -->
      <div class="member-card">
        <h3>Soham Gaikwad</h3>
        <p><strong>Roll No:</strong> 2472013</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Frontend Developer</p>
        <p>Focuses on the user interface design, responsiveness, and visual experience of CAMS using HTML, CSS, and JavaScript.</p>
      </div>

      <!-- Member 3 -->
      <div class="member-card">
        <h3>Sakshi Bingardive</h3>
        <p><strong>Roll No:</strong> 2472014</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Database Administrator</p>
        <p>Manages the CAMS database, ensuring data accuracy, integrity, and smooth interaction between the frontend and backend.</p>
      </div>

      <!-- Member 4 -->
      <div class="member-card">
        <h3>Shubham Deshmukh</h3>
        <p><strong>Roll No:</strong> 2472015</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Project Designer & Tester</p>
        <p>Handles system testing, documentation, and project design layout, ensuring quality and user satisfaction.</p>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2025 CAMS | Developed by Team TYBCS(A) | All Rights Reserved</p>
  </footer>
</body>
</html>
