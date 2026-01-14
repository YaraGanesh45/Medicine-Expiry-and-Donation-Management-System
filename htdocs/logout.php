
<?php
session_start();

// ✅ Save user_id before destroying session
$user_id = $_SESSION['user_id'] ?? 0;

// ✅ DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// ✅ Log function
function log_action($con, $user_id, $action, $details = null) {
    $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// ✅ Log logout action
if ($user_id > 0) {
    log_action($con, $user_id, "Logout", "User logged out");
}

// ✅ Close DB
$con->close();

// ✅ Destroy session and redirect
session_destroy();
header("Location: index.php");
exit();
?>

