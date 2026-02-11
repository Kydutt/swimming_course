<?php
session_start();
require 'function.php';

$error_message = '';
$success_message = '';

// Check if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Handle registration form submission
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Konfirmasi password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal 6 karakter!';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = 'Email sudah terdaftar!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Default role for registration

            // Insert new user
            $insert_stmt = $conn->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                header('Location: login.php?registered=success');
                exit;
            } else {
                $error_message = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Swimming Course</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1> Daftar Akun Baru</h1>
                <p>Bergabunglah dengan Kursus Renang Kami</p>
            </div>

            <div class="register-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-input" required placeholder="Nama Anda" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" required placeholder="email@contoh.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" required placeholder="Minimal 6 karakter">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required placeholder="Ulangi password">
                    </div>

                    <button type="submit" name="register" class="btn-register">
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            <div class="register-footer">
                Sudah punya akun? <a href="login.php" style="color: #2563eb; font-weight: 600; text-decoration: none;">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
