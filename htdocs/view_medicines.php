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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// ✅ Ensure medicines table exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    batch_no VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    expiry_date DATE NOT NULL,
    supplier VARCHAR(100) NOT NULL
)";
$con->query($createTableSQL);

// ✅ Handle search, filters, sorting
$search = $_GET['search'] ?? '';
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';
$sort   = $_GET['sort'] ?? 'expiry_asc';

// ✅ Pagination
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Base query
$where = "WHERE 1=1";
if (!empty($search)) {
    $search_sql = "%$search%";
    $where .= " AND (name LIKE '$search_sql' OR supplier LIKE '$search_sql')";
}
if (!empty($from) && !empty($to)) {
    $where .= " AND expiry_date BETWEEN '$from' AND '$to'";
} elseif (!empty($from)) {
    $where .= " AND expiry_date >= '$from'";
} elseif (!empty($to)) {
    $where .= " AND expiry_date <= '$to'";
}

// Sorting
$order = "ORDER BY expiry_date ASC";
switch ($sort) {
    case "expiry_desc": $order = "ORDER BY expiry_date DESC"; break;
    case "quantity_asc": $order = "ORDER BY quantity ASC"; break;
    case "quantity_desc": $order = "ORDER BY quantity DESC"; break;
    case "name_asc": $order = "ORDER BY name ASC"; break;
    case "name_desc": $order = "ORDER BY name DESC"; break;
}

// ✅ Count total rows
$count_sql = "SELECT COUNT(*) AS total FROM medicines $where";
$total_result = $con->query($count_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// ✅ Fetch with pagination
$sql = "SELECT * FROM medicines $where $order LIMIT $limit OFFSET $offset";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medicine List</title>
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
        .container { max-width: 1000px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#2d3748; margin-bottom:20px; }
        form { margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap; justify-content:center; }
        input[type="text"], input[type="date"], select, button { padding:8px; border-radius:6px; border:1px solid #ccc; font-family:inherit; }
        button { background:#3b82f6; color:white; border:none; cursor:pointer; font-weight:500; }
        button:hover { background:#2563eb; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        th, td { padding:12px; text-align:center; border:1px solid #ddd; }
        th { background:#3b82f6; color:white; }
        tr.expired { background:#fecaca; }
        tr.near-expiry { background:#fef3c7; }
        tr.low-stock { background:#fde68a; }
        a { text-decoration:none; color:#3b82f6; font-weight:500; margin:0 8px; }
        a:hover { color:#1e3a8a; }
        .pagination { text-align:center; margin-top:20px; }
        .pagination a { padding:8px 12px; margin:0 4px; border:1px solid #3b82f6; border-radius:6px; text-decoration:none; }
        .pagination a.active { background:#3b82f6; color:white; }
        .pagination a:hover { background:#2563eb; color:white; }
    </style>
</head>
<body>
<div class="container">
    <h2>Medicine List</h2>

    <!-- ✅ Search, Filter & Sort Form -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by name/supplier" value="<?php echo htmlspecialchars($search); ?>">
        <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>">
        <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>">

        <select name="sort">
            <option value="expiry_asc" <?php if($sort=="expiry_asc") echo "selected"; ?>>Expiry (Soonest First)</option>
            <option value="expiry_desc" <?php if($sort=="expiry_desc") echo "selected"; ?>>Expiry (Latest First)</option>
            <option value="quantity_asc" <?php if($sort=="quantity_asc") echo "selected"; ?>>Quantity (Low → High)</option>
            <option value="quantity_desc" <?php if($sort=="quantity_desc") echo "selected"; ?>>Quantity (High → Low)</option>
            <option value="name_asc" <?php if($sort=="name_asc") echo "selected"; ?>>Name (A → Z)</option>
            <option value="name_desc" <?php if($sort=="name_desc") echo "selected"; ?>>Name (Z → A)</option>
        </select>

        <button type="submit">Apply</button>
        <a href="view_medicines.php" style="padding:8px; background:#ef4444; color:white; border-radius:6px; text-decoration:none;">Reset</a>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Batch No</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Supplier</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            $today = date("Y-m-d");
            $near_expiry = date("Y-m-d", strtotime("+30 days"));

            while ($row = $result->fetch_assoc()) {
                $row_class = "";
                if ($row['expiry_date'] < $today) $row_class = "expired";
                elseif ($row['expiry_date'] <= $near_expiry) $row_class = "near-expiry";
                if ($row['quantity'] < 10) $row_class = "low-stock";

                echo "<tr class='$row_class'>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['batch_no']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['expiry_date']}</td>
                        <td>{$row['supplier']}</td>
                        <td>
                            <a href='edit_medicine.php?id={$row['id']}'>✏️ Edit</a> | 
                            <a href='delete_medicine.php?id={$row['id']}' onclick='return confirm(\"Are you sure?\")'>🗑 Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No medicines found.</td></tr>";
        }
        ?>
    </table>

    <!-- ✅ Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$page-1])); ?>">⬅ Prev</a>
        <?php endif; ?>

        <?php for ($i=1; $i <= $total_pages; $i++): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$i])); ?>" class="<?php if($i==$page) echo 'active'; ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$page+1])); ?>">Next ➡</a>
        <?php endif; ?>
    </div>

    <p style="text-align:center; margin-top:20px;">
        <a href="add_medicine.php">➕ Add New Medicine</a> | 
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </p>
</div>
</body>
</html>
<?php $con->close(); ?>

