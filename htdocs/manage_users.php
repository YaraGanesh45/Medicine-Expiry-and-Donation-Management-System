<?php
session_start();
if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id']   = $_COOKIE['user_id'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['role']      = $_COOKIE['role'];
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
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

// ✅ Delete staff
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    // prevent deleting admin
    $check_admin = $con->prepare("SELECT role FROM users WHERE id = ?");
    $check_admin->bind_param("i", $delete_id);
    $check_admin->execute();
    $check_admin->bind_result($role);
    $check_admin->fetch();
    $check_admin->close();

    if ($role !== "admin") {
        $del = $con->prepare("DELETE FROM users WHERE id = ?");
        $del->bind_param("i", $delete_id);
        $del->execute();
        $del->close();
    }
    header("Location: manage_users.php");
    exit();
}

// ✅ Fetch all users
$sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
            max-width: 900px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2d3748;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #3b82f6;
            color: #fff;
        }
        a {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
        }
        .delete-btn {
            background: #ef4444;
            color: white;
        }
        .delete-btn:hover {
            background: #dc2626;
        }
        .back-btn {
            display: inline-block;
            margin-top: 15px;
            background: #3b82f6;
            color: #fff;
            padding: 8px 16px;
        }
        .back-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['role']}</td>
                        <td>{$row['created_at']}</td>
                        <td>";
                if ($row['role'] !== 'admin') {
                    echo "<a class='delete-btn' href='manage_users.php?delete={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                } else {
                    echo "❌ Not Allowed";
                }
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No users found.</td></tr>";
        }
        ?>
    </table>
    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>
</body>
</html>
<?php $con->close(); ?>
