<?php 
session_start(); 

if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) { 
    $_SESSION['user_id'] = $_COOKIE['user_id']; 
    $_SESSION['user_name'] = $_COOKIE['user_name']; 
    $_SESSION['role'] = $_COOKIE['role']; 
} 

// Check if user is logged in 
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
} 

// If user not logged in → redirect 
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
} 

$user_name = $_SESSION['user_name']; 
$user_role = $_SESSION['role']; 

// ✅ DB connection 
$servername = "sql105.infinityfree.com"; 
$username = "if0_39907293"; 
$dbpassword = "Ganeshyara807"; 
$database = "if0_39907293_MedicineEDMSinfo"; 
$con = new mysqli($servername, $username, $dbpassword, $database); 

if ($con->connect_error) { 
    die("Connection failed: " . $con->connect_error); 
} 

// ✅ Expired count 
$today = date("Y-m-d"); 
$sql_expired = "SELECT COUNT(*) AS count FROM medicines WHERE expiry_date < ?"; 
$stmt = $con->prepare($sql_expired); 
$stmt->bind_param("s", $today); 
$stmt->execute(); 
$result = $stmt->get_result(); 
$expired_count = $result->fetch_assoc()['count']; 
$stmt->close(); 

// ✅ Near expiry (30 days) 
$near_expiry = date("Y-m-d", strtotime("+30 days")); 
$sql_near = "SELECT COUNT(*) AS count FROM medicines WHERE expiry_date BETWEEN ? AND ?"; 
$stmt = $con->prepare($sql_near); 
$stmt->bind_param("ss", $today, $near_expiry); 
$stmt->execute(); 
$result = $stmt->get_result(); 
$near_expiry_count = $result->fetch_assoc()['count']; 
$stmt->close(); 

// ✅ Low stock (< 10) 
$sql_low = "SELECT COUNT(*) AS count FROM medicines WHERE quantity < 10"; 
$result = $con->query($sql_low); 
$low_stock_count = $result->fetch_assoc()['count']; 

// ✅ Total medicines 
$sql_total = "SELECT COUNT(*) AS count FROM medicines"; 
$result = $con->query($sql_total); 
$total_count = $result->fetch_assoc()['count']; 

$con->close(); 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Medicine Dashboard</title> 
    <style> 
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
            margin: 0; 
            padding: 0; 
        } 
        .navbar { 
            background: #6366F1; 
            color: white; 
            padding: 15px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        } 
        .navbar h2 { 
            margin: 0; 
        } 
        .container { 
            padding: 40px; 
            max-width: 1200px; 
            margin: auto; 
        } 
        h3 { 
            color: #374151; 
        } 
        .card { 
            background: #fff; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
            margin-bottom: 20px; 
        } 
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 20px; 
            margin: 20px 0; 
        } 
        .stat-card { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 12px; 
            text-align: center; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); 
            cursor: pointer; 
            transition: transform 0.2s, box-shadow 0.2s; 
            text-decoration: none; 
            color: inherit; 
        } 
        .stat-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
        } 
        .stat-card h4 { 
            margin: 0; 
            font-size: 16px; 
            color: #374151; 
        } 
        .stat-card p { 
            font-size: 24px; 
            font-weight: bold; 
            margin: 10px 0 0; 
        } 
        .actions { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 20px; 
            margin-top: 20px; 
        } 
        .action-btn { 
            display: block; 
            background: #6366F1; 
            color: white; 
            padding: 14px; 
            text-align: center; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 600; 
            transition: 0.3s; 
        } 
        .action-btn:hover { 
            background: #4f46e5; 
        } 
        .logout { 
            background: #dc2626; 
        } 
        .logout:hover { 
            background: #b91c1c; 
        } 
    </style> 
</head> 
<body> 
   <div class="navbar"> 
    <h2>Medicine Expiry Management</h2> 
    <div> 
        <strong><?php echo $user_name; ?></strong> (<?php echo ucfirst($user_role); ?>) 

        <!-- 🔔 Notification Icon --> 
        <a href="expiry_alerts.php" class="notifications" style="margin-left:15px; position:relative; font-size:20px; text-decoration:none; color:white;"> 
            🔔 
            <?php 
            $total_alerts = $expired_count + $near_expiry_count + $low_stock_count; 
            if ($total_alerts > 0) { ?> 
                <span style="position:absolute; top:-8px; right:-10px; background:red; color:white; font-size:12px; padding:3px 7px; border-radius:50%;"> 
                    <?php echo $total_alerts; ?> 
                </span> 
            <?php } ?> 
        </a> 
    </div> 
</div> 

    <div class="container"> 
        <div class="card"> 
            <h3>Welcome, <?php echo $user_name; ?> 👋</h3> 
            <p>You are logged in as <strong><?php echo ucfirst($user_role); ?></strong>.</p> 
        </div> 

        <!-- ✅ Summary Cards --> 
        <div class="stats"> 
            <a href="expiry_alerts.php?filter=expired" class="stat-card" style="background:#fecaca;"> 
                <h4>Expired</h4> 
                <p><?php echo $expired_count; ?></p> 
            </a> 
            <a href="expiry_alerts.php?filter=near" class="stat-card" style="background:#fef3c7;"> 
                <h4>Near Expiry (30 Days)</h4> 
                <p><?php echo $near_expiry_count; ?></p> 
            </a> 
            <a href="view_medicines.php?low=1" class="stat-card" style="background:#dbeafe;"> 
                <h4>Low Stock (&lt; 10)</h4> 
                <p><?php echo $low_stock_count; ?></p> 
            </a> 
            <a href="view_medicines.php" class="stat-card" style="background:#bbf7d0;"> 
                <h4>Total Medicines</h4> 
                <p><?php echo $total_count; ?></p> 
            </a> 
        </div> 

        <!-- ✅ Actions --> 
        <div class="actions"> 
            <a href="add_medicine.php" class="action-btn">➕ Add Medicine</a> 
            <a href="view_medicines.php" class="action-btn">📋 View All Medicines</a> 
            <a href="expiry_alerts.php" class="action-btn">⚠️ Expiry Alerts</a> 
            <a href="donation_suggestions.php" class="action-btn">🎁 Donation Suggestions</a> 
            <a href="export_csv.php" class="action-btn">📊 Export Medicines (CSV)</a> 
            <a href="profile.php" class="action-btn">👤 My Profile</a> 
            <?php if ($user_role === "admin") { ?> 
                <a href="manage_users.php" class="action-btn">👥 Manage Users</a> 
                <a href="view_logs.php" class="action-btn">📜 View Audit Logs</a> 
            <?php } ?> 
            <a href="reports.php" class="action-btn">📊 Medicine Reports</a> 
            <a href="about.php" class="action-btn">ℹ️ About Project</a> 
            <a href="logout.php" class="action-btn logout">🚪 Logout</a> 
        </div> 
    </div> 
</body> 
</html>







