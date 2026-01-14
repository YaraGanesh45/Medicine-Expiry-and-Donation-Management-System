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

// DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch medicines
$sql = "SELECT id, name, batch_no, quantity, expiry_date, supplier FROM medicines ORDER BY expiry_date ASC";
$result = $con->query($sql);

// File headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=medicines_report.csv');

// Open output stream
$output = fopen("php://output", "w");

// Add CSV column headers
fputcsv($output, ['ID', 'Name', 'Batch No', 'Quantity', 'Expiry Date', 'Supplier']);

// Add data rows
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();
?>
