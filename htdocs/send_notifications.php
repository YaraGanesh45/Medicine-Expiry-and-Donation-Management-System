<?php
// DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";
$con = new mysqli($servername, $username, $dbpassword, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// ✅ Fetch admin email
$admin_sql = "SELECT email FROM users WHERE role = 'admin' LIMIT 1";
$admin_result = $con->query($admin_sql);
$admin_email = "";
if ($admin_result->num_rows > 0) {
    $row = $admin_result->fetch_assoc();
    $admin_email = $row['email'];
}

// ✅ Fetch near-expiry medicines (next 30 days)
$today = date("Y-m-d");
$near_expiry = date("Y-m-d", strtotime("+30 days"));

$sql = "SELECT name, batch_no, expiry_date, quantity FROM medicines WHERE expiry_date BETWEEN ? AND ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $today, $near_expiry);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0 && !empty($admin_email)) {
    $subject = "⚠️ Medicine Expiry Alert - Next 30 Days";
    $message = "Hello Admin,\n\nThe following medicines are expiring within 30 days:\n\n";
    
    while ($row = $result->fetch_assoc()) {
        $message .= "Name: {$row['name']}, Batch: {$row['batch_no']}, Qty: {$row['quantity']}, Expiry: {$row['expiry_date']}\n";
    }
    
    $message .= "\nPlease take necessary action.\n\nRegards,\nMedicine Expiry Management System";

    $headers = "From: noreply@yourdomain.com";

    if (mail($admin_email, $subject, $message, $headers)) {
        echo "✅ Notification email sent to Admin ($admin_email).";
    } else {
        echo "❌ Failed to send email. (Check hosting mail settings)";
    }
} else {
    echo "✅ No medicines expiring soon OR no admin found.";
}

$stmt->close();
$con->close();
?>
