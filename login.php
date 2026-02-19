<?php
session_start();
require 'function.php';

$error_message = '';

// Check if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    // Redirect based on role
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Handle login form submission
// Handle login form submission
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Query database for user (fetch password hash)
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Login successful
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            // Password incorrect
            $error_message = 'Email atau password salah!';
        }
    } else {
        // User not found
        $error_message = 'Email atau password salah!';
    }
    
    $stmt->close();
}

// Initialize success message
$success_message = '';

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = 'Anda telah berhasil logout.';
}

if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $success_message = 'Registrasi berhasil! Silakan login.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Swimming Course</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1> Login Admin</h1>
                <p>Swimming Course Management System</p>
            </div>

            <div class="login-body">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="admin@swimming.com"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="********"
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <button type="submit" name="login" class="btn-login">
                        Login
                    </button>
                </form>
            </div>

            <div class="login-footer">
                Belum punya akun? <a href="register.php" style="color: #2563eb; font-weight: 600; text-decoration: none;">Daftar di sini</a><br>
                © 2026 Swimming Course Management
            </div>
        </div>
    </div>
</body>
</html>
