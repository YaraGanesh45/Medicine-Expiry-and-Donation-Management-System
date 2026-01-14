<?php
session_start();
if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id']   = $_COOKIE['user_id'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['role']      = $_COOKIE['role'];
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);

// ✅ Pagination setup
$limit = 10; // records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ✅ Get total records
$total_sql = "SELECT COUNT(*) AS total FROM audit_logs";
$total_result = $con->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// ✅ Fetch paginated logs
$sql = "SELECT a.*, u.name AS user_name 
        FROM audit_logs a 
        JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC 
        LIMIT $limit OFFSET $offset";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs</title>
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
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #6366F1; color: white; }
        .pagination { text-align:center; margin-top:15px; }
        .pagination a {
            margin: 0 5px;
            padding: 6px 12px;
            text-decoration: none;
            background: #6366F1;
            color: white;
            border-radius: 4px;
        }
        .pagination a.active { background: #4f46e5; font-weight: bold; }
        .pagination a:hover { background: #4338ca; }
    </style>
</head>
<body>
<div class="container">
    <h2>Audit Logs</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>Timestamp</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- ✅ Pagination Controls -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">⬅ Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next ➡</a>
        <?php endif; ?>
    </div>

    <p style="text-align:center; margin-top:15px;">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </p>
</div>
</body>
</html>
<?php $con->close(); ?>

