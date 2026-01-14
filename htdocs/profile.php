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

// ✅ DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Fetch current user details
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Check if email is taken (except for this user)
    $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_stmt = $con->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $msg = "<p style='color:red;'>❌ Email already taken, choose another.</p>";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
        } else {
            $update_sql = "UPDATE users SET name=?, email=? WHERE id=?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("ssi", $name, $email, $user_id);
        }

        if ($update_stmt->execute()) {
            $msg = "<p style='color:green;'>✅ Profile updated successfully!</p>";
            $_SESSION['user_name'] = $name; // update session
        } else {
            $msg = "<p style='color:red;'>❌ Error: " . $update_stmt->error . "</p>";
        }
        $update_stmt->close();
    }
    $check_stmt->close();
}
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
        .container { max-width:500px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align:center; margin-bottom:20px; }
        label { display:block; margin:8px 0 4px; }
        input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:8px; }
        button { width:100%; padding:12px; background:#6366F1; border:none; color:#fff; border-radius:8px; font-weight:600; cursor:pointer; }
        button:hover { background:#4f46e5; }
        .msg { text-align:center; margin-bottom:15px; }
        .links { text-align:center; margin-top:15px; }
        .links a { text-decoration:none; color:#6366F1; margin:0 8px; }
    </style>
</head>
<body>
<div class="container">
    <h2>👤 My Profile</h2>
    <div class="msg"><?= $msg; ?></div>
    <form method="POST">
        <label for="name">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

        <label for="password">Password (leave blank to keep same)</label>
        <input type="password" name="password" placeholder="Enter new password">

        <button type="submit">💾 Update Profile</button>
    </form>
    <div class="links">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>
</div>
</body>
</html>
