<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Team | CAMS</title>

  <style>
    /* GLOBAL */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', 'Segoe UI', sans-serif;
      background: #f4f6f9;   /* Light academic background */
      color: #1f2937;
    }

    /* HEADER */
    header {
      background: #1a4b84;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    header h1 {
      margin: 0;
      font-size: 1.8rem;
      color: #ffffff;
      font-weight: 700;
    }

    /* NAVIGATION */
    nav ul.nav-links {
      list-style: none;
      display: flex;
      gap: 1.2rem;
      margin: 0;
      padding: 0;
    }

    nav ul.nav-links li a {
      color: #e5e7eb;
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      transition: 0.3s ease;
    }

    nav ul.nav-links li a:hover,
    nav ul.nav-links li a.active {
      background: #245fa6;
      color: #fff;
    }

    /* CONTAINER */
    .container {
      max-width: 1100px;
      margin: 3rem auto;
      padding: 0 1.5rem;
      text-align: center;
    }

    .container h2 {
      font-size: 2.4rem;
      color: #1a4b84;
      margin-bottom: 1rem;
      font-weight: 700;
    }

    .container p {
      font-size: 1.15rem;
      color: #4b5563;
      max-width: 850px;
      margin: 0 auto 2.5rem auto;
      line-height: 1.7;
    }

    /* TEAM GRID */
    .team-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
    }

    .member-card {
      width: 260px;
      background: white;
      padding: 1.8rem;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.10);
      transition: 0.3s ease;
      animation: fadeIn 1s ease;
    }

    .member-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .member-card h3 {
      margin: 0.5rem 0;
      font-size: 1.3rem;
      color: #1a4b84;
      font-weight: 700;
    }

    .role {
      font-weight: bold;
      color: #fbbf24; /* Gold */
      margin: 0.4rem 0 0.8rem;
    }

    .member-card p {
      color: #4b5563;
      font-size: 0.95rem;
      line-height: 1.5;
      margin: 0.3rem 0;
    }

    /* FOOTER */
    footer {
      background: #1a4b84;
      text-align: center;
      padding: 1rem;
      color: white;
      margin-top: 3rem;
    }

    footer p:hover {
      cursor: pointer;
      color: #dbeafe;
    }

    /* RESPONSIVE */
    @media (max-width: 700px) {
      header {
        flex-direction: column;
        gap: 1rem;
      }
      nav ul.nav-links {
        flex-direction: column;
        gap: 0.5rem;
      }
    }

    /* ANIMATION */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

  </style>
</head>

<body>

  <!-- HEADER -->
  <header>
    <h1>CAMS Team</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="team.php" class="active">Our Team</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- TEAM SECTION -->
  <main class="container">
    <h2>Meet the Project Team</h2>
    <p>Our dedicated team collaborated to build the College Attendance Management System (CAMS), ensuring a reliable, efficient, and modern solution for colleges.</p>

    <div class="team-grid">

      <!-- Member 1 -->
      <div class="member-card">
        <h3>Shivam Joshi</h3>
        <p><strong>Roll No:</strong> 2472012</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Backend Developer</p>
        <p>Developed backend logic, server APIs, and database integration ensuring secure and smooth system performance.</p>
      </div>

      <!-- Member 2 -->
      <div class="member-card">
        <h3>Soham Gaikwad</h3>
        <p><strong>Roll No:</strong> 2472013</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Frontend Developer</p>
        <p>Designed the user interface and built responsive layouts using HTML, CSS, and JavaScript.</p>
      </div>

      <!-- Member 3 -->
      <div class="member-card">
        <h3>Sakshi Bingardive</h3>
        <p><strong>Roll No:</strong> 2472014</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Database Administrator</p>
        <p>Maintained the database structure, optimized queries, and ensured accurate data flow.</p>
      </div>

      <!-- Member 4 -->
      <div class="member-card">
        <h3>Shubham Deshmukh</h3>
        <p><strong>Roll No:</strong> 2472015</p>
        <p><strong>Class:</strong> TYBCS (A)</p>
        <p class="role">Project Designer & Tester</p>
        <p>Managed UI flow, documentation, and performed thorough testing for user experience and quality control.</p>
      </div>

    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <p>© 2025 CAMS | Developed by Team TYBCS(A) | All Rights Reserved</p>
  </footer>

</body>
</html>
