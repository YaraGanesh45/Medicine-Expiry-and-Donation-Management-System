<?php
session_start();

// ✅ Redirect if not logged in
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

// ✅ Pagination setup
$limit = 10; // items per page
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Dates for query
$today = date("Y-m-d");
$donation_limit = date("Y-m-d", strtotime("+30 days"));

// Total rows for pagination
$count_sql = "SELECT COUNT(*) as total FROM medicines WHERE expiry_date BETWEEN ? AND ?";
$count_stmt = $con->prepare($count_sql);
$count_stmt->bind_param("ss", $today, $donation_limit);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
$count_stmt->close();

// Fetch medicines for current page
$sql = "SELECT * FROM medicines WHERE expiry_date BETWEEN ? AND ? ORDER BY expiry_date ASC LIMIT ? OFFSET ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssii", $today, $donation_limit, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Suggestions</title>
    <style>
        body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: url("images/back.jpg") no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        line-height: 1.6;
    }
        .container {
            max-width: 900px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2d3748;
        }
        p.note {
            text-align: center;
            color: #dc2626;
            font-weight: 500;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background: #3b82f6;
            color: white;
        }
        tr.near-expiry {
            background: #fef3c7; /* yellow */
        }
        tr.expired {
            background: #fecaca; /* red */
        }
        a {
            text-decoration: none;
            color: #3b82f6;
            font-weight: 500;
            margin: 0 8px;
        }
        a:hover {
            color: #1e3a8a;
        }
        /* Pagination styles */
        .pagination {
            text-align: center;
            margin-top: 15px;
        }
        .pagination a, .pagination strong {
            display: inline-block;
            margin: 0 5px;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #3b82f6;
        }
        .pagination strong {
            background-color: #3b82f6;
            color: white;
            border: 1px solid #3b82f6;
        }
        .pagination a:hover {
            background-color: #1e40af;
            color: white;
            border: 1px solid #1e40af;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🎁 Donation Suggestions</h2>
    <p class="note">These medicines are expiring within the next 30 days. Consider donating them to NGOs, hospitals, or local clinics before expiry.</p>

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
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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
            echo "<tr><td colspan='6'>✅ No medicines require donation right now.</td></tr>";
        }
        ?>
    </table>

    <!-- Pagination Links -->
    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php
        for($i = 1; $i <= $total_pages; $i++) {
            if($i == $page) {
                echo "<strong>$i</strong>";
            } else {
                echo "<a href='?page=$i'>$i</a>";
            }
        }
        ?>
    </div>
    <?php endif; ?>

    <p style="text-align:center;">
        <a href="view_medicines.php">📋 View Medicines</a> | 
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </p>
</div>
</body>
</html>
<?php 
$stmt->close(); 
$con->close(); 
?>

