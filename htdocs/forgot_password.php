<?php
session_start();

// ✅ DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);

if ($con->connect_error) die("Connection failed: " . $con->connect_error);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $new_pass = $_POST['password'];

    $sql = "SELECT id FROM users WHERE username=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

        $update = $con->prepare("UPDATE users SET password=? WHERE username=?");
        $update->bind_param("ss", $hashed_pass, $username);
        $update->execute();

        $message = "✅ Password reset successful. <a href='index.php'>Login here</a>";
    } else {
        $message = "❌ Username not found!";
    }
}
$con->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
        .box { background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); width:350px; text-align:center; }
        input { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:6px; }
        button { width:100%; padding:10px; background:#6366F1; border:none; color:white; border-radius:6px; font-weight:bold; cursor:pointer; }
        button:hover { background:#4f46e5; }
        a { text-decoration:none; color:#6366F1; }
    </style>
</head>
<body>
<div class="box">
    <h2>Reset Password</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Enter your username" required>
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit">Reset</button>
    </form>
    <p><?php echo $message; ?></p>
    <a href="index.php"> Back </a>
</div>
</body>
</html>

