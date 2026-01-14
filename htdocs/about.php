<?php
session_start();

// ✅ Auto-login using cookies if session not set
//if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
  //  $_SESSION['user_id'] = $_COOKIE['user_id'];
   // $_SESSION['username'] = $_COOKIE['username'];
//}

// ✅ Redirect to login if neither session nor cookie exists
//if (!isset($_SESSION['user_id'])) {
  //  header("Location: index.php");
    //exit();
//}

// ✅ Set/update last visit cookie
if(isset($_COOKIE['last_visit'])){
    $lastVisit = $_COOKIE['last_visit'];
} else {
    $lastVisit = "This is your first visit!";
}
setcookie('last_visit', date('d-m-Y H:i:s'), time() + (30*24*60*60), "/");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - MediSafe</title>
    <link rel="stylesheet" href="styles.css"> <!-- Global CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2ff, #fef9c3);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            line-height: 1.8;
        }
        h2 {
            text-align: center;
            color: #4f46e5;
            margin-bottom: 20px;
        }
        h3 {
            color: #374151;
            margin-top: 20px;
        }
        p {
            margin-bottom: 12px;
        }
        .team {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .member {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .member h4 {
            margin: 5px 0;
            color: #2563eb;
        }
        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #6366F1;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }
        .back:hover {
            background: #4f46e5;
        }
        .last-visit {
            text-align: center;
            color: #4f46e5;
            margin-bottom: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ✅ Last visit message -->
        <p class="last-visit">
            <?php echo "Welcome back, " . $_SESSION['username'] . "! Last visit: " . $lastVisit; ?>
        </p>

        <h2>About MediSafe</h2>
        <p>
            <strong>MediSafe</strong> is a web-based <em>Medicine Expiry and Stock Management System</em>.  
            It helps hospitals, pharmacies, and clinics efficiently track medicines, manage stock levels, 
            and get alerts for near-expiry and low-stock items. 
        </p>

        <h3>✨ Features</h3>
        <ul>
            <li>✔ Medicine Expiry Tracking</li>
            <li>✔ Low Stock Alerts</li>
            <li>✔ User Management (Admin & Staff)</li>
            <li>✔ Audit Logs & Reports</li>
            <li>✔ Data Export (CSV & Charts)</li>
            <li>✔ Secure Login & Role-based Access</li>
        </ul>

        <h3>👨‍💻 Project Team</h3>
        <div class="team">
            <div class="member">
                <h4>YARA GANESH</h4>
                <p>Project Lead & Fullstack Developer</p>
            </div>
            <div class="member">
                <h4>MUTHUNURI SUNIL</h4>
                <p>Backend Developer</p>
            </div>
            <div class="member">
                <h4>M. BHAVITHA</h4>
                <p>Frontend Developer</p>
            </div>
        </div>

        <h3>📌 Purpose</h3>
        <p>
            The project is designed to reduce medicine wastage, improve inventory visibility, 
            and support timely donations of unused medicines.  
            This ensures better patient care and cost savings for healthcare facilities.
        </p>

        <p style="text-align:center;">
            <a href="dashboard.php" class="back"> Back </a>
        </p>
    </div>
</body>
</html>

