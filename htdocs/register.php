<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>MediSafe Registration</title>
  <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

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

    .login-container {
        width: 100%;
        max-width: 400px;
    }

    .login-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px 32px 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                    0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f1f5f9;
        position: relative;
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .login-header h1 {
        color: #1e293b;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 6px;
        letter-spacing: -0.025em;
    }

    .login-header p {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        color: #1e293b;
        font-size: 15px;
        font-weight: 400;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #6366F1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-group input::placeholder {
        color: #94a3b8;
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 44px;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #64748b;
        padding: 6px;
        border-radius: 4px;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #6366F1;
    }

    .login-btn {
        width: 100%;
        background: #6366F1;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        cursor: pointer;
        font-family: inherit;
        font-size: 15px;
        font-weight: 600;
        position: relative;
        margin-bottom: 20px;
        transition: all 0.2s ease;
        overflow: hidden;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-btn:hover {
        background: #4f46e5;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .login-btn:active {
        transform: translateY(0);
    }

    .login-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 480px) {
        body {
            padding: 16px;
        }

        .login-card {
            padding: 32px 24px 24px;
            border-radius: 12px;
        }

        .login-header h1 {
            font-size: 1.5rem;
        }
    }
  </style>
</head>
<body>

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
    die("<p style='color:red;'>Connection failed: " . $con->connect_error . "</p>");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $con->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<p style='color:red; text-align:center; margin-bottom:16px;'>Email already registered. Please use another.</p>";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "<p style='color:red; text-align:center; margin-bottom:16px;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check_stmt->close();
}
?>

 <div class="login-container">
  <div class="login-card">
    <div class="login-header">
      <h1>MediSafe Registration</h1>
      <p>Register a new account</p>
    </div>

    <form method="POST" action="">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" placeholder="John Doe" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="email@example.com" required>
      </div>

      <div class="form-group password-wrapper">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter a secure password" required>
        <button type="button" class="password-toggle" onclick="togglePasswordVisibility()">👁️</button>
      </div>

      <div class="form-group">
        <label for="role">Select Role</label>
        <select name="role" id="role" required>
          <option value="staff">Staff</option>
          <?php
            // ✅ Only show Admin if none exists
            $check_admin = $con->query("SELECT id FROM users WHERE role='admin' LIMIT 1");
            if ($check_admin->num_rows === 0) {
                echo '<option value="admin">Admin</option>';
            }
          ?>
        </select>
      </div>

      <button type="submit" class="login-btn">Register</button>
    </form>

    <div style="text-align:center; margin: 10px 0; color:#64748b; font-size:14px;">
        Already have an account?
    </div>
    <a href="login.php" class="login-btn" style="text-align:center; display:flex; justify-content:center; align-items:center; text-decoration:none;">Login</a>
  </div>
</div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById("password");
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
    }
  </script>
</body>
</html>
<?php $con->close(); ?>
