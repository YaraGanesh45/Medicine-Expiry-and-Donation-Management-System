<?php
session_start();
if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id']   = $_COOKIE['user_id'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['role']      = $_COOKIE['role'];
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// ✅ Date ranges
$today = date("Y-m-d");
$near_expiry = date("Y-m-d", strtotime("+30 days"));

// ✅ Count expired
$sql = "SELECT COUNT(*) AS cnt FROM medicines WHERE expiry_date < ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$expired = $res['cnt'];
$stmt->close();

// ✅ Count near-expiry
$sql = "SELECT COUNT(*) AS cnt FROM medicines WHERE expiry_date BETWEEN ? AND ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $today, $near_expiry);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$near_expiry_count = $res['cnt'];
$stmt->close();

// ✅ Count safe
$sql = "SELECT COUNT(*) AS cnt FROM medicines WHERE expiry_date > ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $near_expiry);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$safe = $res['cnt'];
$stmt->close();

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medicine Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
         body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: url("images/back.jpg") no-repeat center center fixed; /* ✅ single image */
        background-size: cover;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        line-height: 1.6;
    }
        .report-container {
            max-width: 900px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #1e40af;
        }
        .chart-container {
            width: 70%;
            margin: 30px auto;
        }
        a {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            padding: 12px 24px;
            background: #6366F1;
            color: white;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        a:hover {
            background: #4f46e5;
            transform: translateY(-2px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h2>📊 Medicine Expiry Report</h2>
        <div class="chart-container">
            <canvas id="expiryChart"></canvas>
        </div>
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>

    <script>
        const ctx = document.getElementById('expiryChart').getContext('2d');
        const expiryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Expired', 'Near Expiry (30 days)', 'Safe'],
                datasets: [{
                    data: [<?php echo $expired; ?>, <?php echo $near_expiry_count; ?>, <?php echo $safe; ?>],
                    backgroundColor: ['#ef4444', '#facc15', '#22c55e'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 14, family: 'Poppins' },
                            color: '#374151'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

