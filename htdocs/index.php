<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Expiry & Donation Management</title>
    <style>
        /* ===== Global Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #eef2ff, #f0fdfa);
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ===== Navbar ===== */
        nav {
            background: #6366F1;
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav h2 {
            font-size: 22px;
            letter-spacing: 1px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #dbeafe;
        }

        /* ===== Hero Section ===== */
        .hero {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            padding: 80px 50px;
        }

        .hero-text {
            flex: 1;
            max-width: 500px;
            padding: 20px;
        }

        .hero-text h1 {
            font-size: 38px;
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        .hero-text p {
            font-size: 18px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .hero-text a {
            text-decoration: none;
            background: #6366F1;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .hero-text a:hover {
            background: #4f46e5;
        }

        .hero-img {
            flex: 1;
            text-align: center;
        }

        .hero-img img {
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        /* ===== Features Section ===== */
        .features {
            background: white;
            padding: 80px 50px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .feature-box {
            background: #f9fafb;
            border-radius: 12px;
            padding: 30px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .feature-box h3 {
            color: #1e3a8a;
            margin-bottom: 15px;
        }

        .feature-box p {
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
        }

        /* ===== Footer ===== */
        footer {
            background: #1e3a8a;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 10px;
            }
            .hero {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <nav>
        <h2>MEDMS</h2>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Medicine Expiry & Donation Management System</h1>
            <p>
                An intelligent web-based platform designed to efficiently track, monitor, and manage
                medicine expiry while promoting donation of near-expiry but safe medicines.
                Simplifying healthcare inventory management while reducing wastage.
            </p>
            <a href="register.php">Get Started →</a>
        </div>
        <div class="hero-img">
            <img src="https://cdn-icons-png.flaticon.com/512/2966/2966482.png" alt="Medicine Management">
        </div>
    </section>

    <section class="features">
        <div class="feature-box">
            <h3>💊 Expiry Alerts</h3>
            <p>Get real-time notifications for expired or near-expiry medicines to take timely action.</p>
        </div>
        <div class="feature-box">
            <h3>🎁 Donation Suggestions</h3>
            <p>Identify safe-to-donate medicines and contribute to social welfare efficiently.</p>
        </div>
        <div class="feature-box">
            <h3>📊 Reports & Insights</h3>
            <p>Analyze stock trends, expiry statistics, and overall medicine utilization reports.</p>
        </div>
        <div class="feature-box">
            <h3>🧾 Audit Logs</h3>
            <p>Track all user actions and system activities for enhanced transparency and accountability.</p>
        </div>
    </section>

    <footer>
        © 2025 MEDMS | Designed for Efficient & Safe Healthcare
    </footer>

</body>
</html>
