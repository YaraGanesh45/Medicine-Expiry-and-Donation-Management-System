<?php
session_start();

// Redirect if user not logged in
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

// DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";

$con = new mysqli($servername, $username, $dbpassword, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Dates for comparison
$today = date("Y-m-d");
$near_expiry = date("Y-m-d", strtotime("+30 days"));

// Fetch expired medicines
$sql_expired = "SELECT * FROM medicines WHERE expiry_date < ? ORDER BY expiry_date ASC";
$stmt_expired = $con->prepare($sql_expired);
$stmt_expired->bind_param("s", $today);
$stmt_expired->execute();
$result_expired = $stmt_expired->get_result();

// Fetch near expiry medicines
$sql_near = "SELECT * FROM medicines WHERE expiry_date BETWEEN ? AND ? ORDER BY expiry_date ASC";
$stmt_near = $con->prepare($sql_near);
$stmt_near->bind_param("ss", $today, $near_expiry);
$stmt_near->execute();
$result_near = $stmt_near->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expiry Alerts</title>
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
        .container {
            max-width: 1000px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background: #6366F1;
            color: white;
        }
        tr.expired {
            background: #fecaca; /* red */
        }
        tr.near-expiry {
            background: #fef3c7; /* yellow */
        }
        a {
            text-decoration: none;
            color: #6366F1;
            font-weight: bold;
        }
        a:hover {
            color: #1e3a8a;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>⚠️ Expiry Alerts</h2>

    <h3>❌ Expired Medicines</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Batch No</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Supplier</th>
        </tr>
        <?php
        if ($result_expired->num_rows > 0) {
            while ($row = $result_expired->fetch_assoc()) {
                echo "<tr class='expired'>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['batch_no']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['expiry_date']}</td>
                        <td>{$row['supplier']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>✅ No expired medicines.</td></tr>";
        }
        ?>
    </table>

    <h3>⏳ Near Expiry Medicines (within 30 days)</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Batch No</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Supplier</th>
        </tr>
        <?php
        if ($result_near->num_rows > 0) {
            while ($row = $result_near->fetch_assoc()) {
                echo "<tr class='near-expiry'>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['batch_no']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['expiry_date']}</td>
                        <td>{$row['supplier']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>✅ No medicines near expiry.</td></tr>";
        }
        ?>
    </table>

    <p style="text-align:center;">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </p>
</div>
</body>
</html>
<?php
$con->close();
?>
