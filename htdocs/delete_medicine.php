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

// ✅ Log function
function log_action($con, $user_id, $action, $details = null) {
    $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    // ✅ Fetch medicine details before delete
    $fetch_sql = "SELECT name, batch_no FROM medicines WHERE id = ?";
    $fetch_stmt = $con->prepare($fetch_sql);
    $fetch_stmt->bind_param("i", $id);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();
    $medicine = $result->fetch_assoc();
    $fetch_stmt->close();

    $sql = "DELETE FROM medicines WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ✅ Log deletion
        $details = $medicine ? "Deleted Medicine: {$medicine['name']} (Batch: {$medicine['batch_no']})" : "Deleted Medicine ID: $id";
        log_action($con, $_SESSION['user_id'], "Deleted Medicine", $details);

        header("Location: view_medicines.php");
        exit();
    } else {
        echo "❌ Error deleting record: " . $stmt->error;
    }
}

$con->close();
?>

