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
$msg = "";

// Fetch medicine details
$sql = "SELECT * FROM medicines WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$medicine = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name        = $_POST['name'];
    $batch_no    = $_POST['batch_no'];
    $quantity    = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];
    $supplier    = $_POST['supplier'];

    $update_sql = "UPDATE medicines SET name=?, batch_no=?, quantity=?, expiry_date=?, supplier=? WHERE id=?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("ssissi", $name, $batch_no, $quantity, $expiry_date, $supplier, $id);

    if ($update_stmt->execute()) {
        // ✅ Log update
        $details = "Updated Medicine: $name (Batch: $batch_no)";
        log_action($con, $_SESSION['user_id'], "Updated Medicine", $details);

        header("Location: view_medicines.php");
        exit();
    } else {
        $msg = "<p style='color:red;'>❌ Error: " . $update_stmt->error . "</p>";
    }
    $update_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Medicine</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f4f8; padding: 40px; }
        .container { max-width: 500px; margin:auto; background:#fff; padding:30px; border-radius:12px; }
        h2 { text-align:center; margin-bottom:20px; }
        label { display:block; margin:8px 0 4px; }
        input { width:100%; padding:10px; margin-bottom:15px; border-radius:8px; border:1px solid #ccc; }
        button { width:100%; padding:12px; background:#3b82f6; border:none; color:#fff; border-radius:8px; }
        button:hover { background:#2563eb; cursor:pointer; }
        .links { text-align:center; margin-top:15px; }
        .links a { margin:0 6px; text-decoration:none; color:#3b82f6; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Medicine</h2>
    <?= $msg; ?>
    <form method="POST">
        <label for="name">Medicine Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($medicine['name']); ?>" required>

        <label for="batch_no">Batch No</label>
        <input type="text" name="batch_no" value="<?= htmlspecialchars($medicine['batch_no']); ?>" required>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($medicine['quantity']); ?>" required>

        <label for="expiry_date">Expiry Date</label>
        <input type="date" name="expiry_date" value="<?= htmlspecialchars($medicine['expiry_date']); ?>" required>

        <label for="supplier">Supplier</label>
        <input type="text" name="supplier" value="<?= htmlspecialchars($medicine['supplier']); ?>" required>

        <button type="submit">💾 Update Medicine</button>
    </form>
    <div class="links">
        <a href="view_medicines.php">⬅ Back</a>
    </div>
</div>
</body>
</html>
<?php $con->close(); ?>

