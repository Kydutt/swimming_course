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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        h1, h2, h3, h4, h5, h6 {
            writing-mode: horizontal-tb !important;
            display: block !important;
            width: 100% !important;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            opacity: 0.95;
            font-size: 0.95rem;
        }

        .login-body {
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.95rem;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            padding: 1.5rem 2rem;
            background-color: #f9fafb;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .login-body {
                padding: 1.5rem;
            }

            .login-footer {
                padding: 1.25rem 1.5rem;
            }
        }
    </style>
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
                            placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢"
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
