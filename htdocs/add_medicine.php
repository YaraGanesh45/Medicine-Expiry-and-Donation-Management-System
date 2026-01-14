<?php
session_start();

// ✅ Auto-login using cookies if session not set
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

// ✅ Include log function
function log_action($con, $user_id, $action, $details = null) {
    $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name        = $_POST['name'];
    $batch_no    = $_POST['batch_no'];
    $quantity    = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];
    $supplier    = $_POST['supplier'];

    $sql = "INSERT INTO medicines (name, batch_no, quantity, expiry_date, supplier) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssiss", $name, $batch_no, $quantity, $expiry_date, $supplier);

    if ($stmt->execute()) {
        $message = "Medicine added successfully!";
        // ✅ Log the action
        log_action($con, $_SESSION['user_id'], "Added Medicine", "Name: $name, Batch: $batch_no, Qty: $quantity");
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #e0e7ff, #fef3c7);
    padding: 40px;
    margin: 0;
}

.container {
    max-width: 520px;
    margin: auto;
    background: #ffffff;
    padding: 30px 25px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.container:hover {
    transform: translateY(-3px);
}

h2 {
    text-align: center;
    color: #4338ca;
    margin-bottom: 20px;
}

input, button {
    width: 100%;
    padding: 12px 14px;
    margin: 10px 0;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 15px;
    transition: border 0.3s, box-shadow 0.3s;
    box-sizing: border-box;
}

input:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

button {
    background: #6366f1;
    color: white;
    border: none;
    font-weight: bold;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.3s ease;
}

button:hover {
    background: #4f46e5;
}

.msg {
    text-align: center;
    color: #16a34a;
    font-weight: 600;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}

a {
    text-decoration: none;
    color: #4f46e5;
    transition: color 0.2s ease;
}

a:hover {
    color: #4338ca;
}

p {
    font-size: 14px;
    color: #374151;
}

    </style>
</head>
<body>
<div class="container">
    <h2>Add Medicine</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Medicine Name" required>
        <input type="text" name="batch_no" placeholder="Batch No" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="date" name="expiry_date" required>
        <input type="text" name="supplier" placeholder="Supplier">
        <button type="submit">Add Medicine</button>
    </form>
    <p style="text-align:center;">
        <a href="view_medicines.php">View Medicines</a> | 
        <a href="dashboard.php">Back to Dashboard</a>
    </p>
</div>
</body>
</html>


