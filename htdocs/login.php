<?php
session_start();

// DB connection
$servername = "sql105.infinityfree.com";
$username   = "if0_39907293";
$dbpassword = "Ganeshyara807";
$database   = "if0_39907293_MedicineEDMSinfo";

$con = new mysqli($servername, $username, $dbpassword, $database);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$error_message = "";

// ✅ Log function
function log_action($con, $user_id, $action, $details = null) {
    $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// ✅ Auto-login using cookies if session not set
if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id']   = $_COOKIE['user_id'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['role']      = $_COOKIE['role'];
    header("Location: dashboard.php");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // ✅ Check if Remember Me is checked

    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role']      = $row['role'];

            // ✅ Set cookies if Remember Me is checked (30 days)
            if($remember) {
                setcookie("user_id", $row['id'], time() + (30*24*60*60), "/");
                setcookie("user_name", $row['name'], time() + (30*24*60*60), "/");
                setcookie("role", $row['role'], time() + (30*24*60*60), "/");
            }

            log_action($con, $row['id'], "Login", "Successful login");
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
            log_action($con, 0, "Login Failed", "Wrong password for $email");
        }
    } else {
        $error_message = "No user found with that email.";
        log_action($con, 0, "Login Failed", "Email not found: $email");
    }
    $stmt->close();
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediSafe Login</title>
<link rel="stylesheet" href="styles.css">
<style>
 * { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: url("images/bg.jpg") no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    line-height: 1.6;
}
.login-container { width: 100%; max-width: 400px; }
.login-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 40px 32px 32px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    position: relative;
}
.login-header { text-align: center; margin-bottom: 32px; }
.login-header h1 { color: #1e293b; font-size: 1.75rem; font-weight: 700; margin-bottom: 6px; }
.login-header p { color: #64748b; font-size: 14px; font-weight: 500; }
.form-group { margin-bottom: 20px; position: relative; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.form-group input {
    width: 100%; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 12px 14px; color: #1e293b; font-size: 15px; font-weight: 400;
    outline: none; transition: all 0.2s ease; font-family: inherit;
}
.form-group input:focus { border-color: #6366F1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
.form-group input::placeholder { color: #94a3b8; }
.password-wrapper { position: relative; }
.password-wrapper input { padding-right: 44px; }
.password-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #64748b; padding: 6px;
}
.login-btn {
    width: 100%; background: #6366F1; color: #ffffff; border: none; border-radius: 8px;
    padding: 12px 20px; cursor: pointer; font-size: 15px; font-weight: 600;
    margin-bottom: 20px; transition: all 0.2s ease; display: flex; justify-content: center; align-items: center; text-decoration:none;
}
.login-btn:hover { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
.error { color: red; text-align: center; margin-bottom: 15px; }
.link-row { text-align:center; margin: 10px 0; color:#64748b; font-size:14px; }
.link-row a { color:#6366F1; text-decoration:none; font-weight:500; }
.link-row a:hover { text-decoration:underline; }

/* ✅ Make Remember Me inline */
.remember-me {
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:20px;
}
.remember-me input { margin:0; }
.remember-me label { margin:0; font-weight:500; }
</style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1>MediSafe Login</h1>
            <p>Login to your account</p>
        </div>

        <?php if(!empty($error_message)) { ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" placeholder="email@example.com" required>
            </div>

            <div class="form-group password-wrapper">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility()">👁️</button>
            </div>

            <!-- ✅ Remember Me Checkbox Inline -->
            <!-- ✅ Remember Me Checkbox Inline and tight -->
           <div class="remember-me" style="display:flex; align-items:center; gap:4px; margin:0 0 12px 0;">
              <input type="checkbox" name="remember" id="remember" style="margin:0;">
              <label for="remember" style="margin:0; font-weight:500;">Remember Me</label>
           </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <!-- Links -->
        <div class="link-row">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
        <div class="link-row">
            Don’t have an account? <a href="register.php">Register</a>
        </div>
    </div>
</div>
<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById("password");
    passwordInput.type = passwordInput.type === "password" ? "text" : "password";
}
</script>
</body>
</html>










