<?php
session_start();
require '../function.php';

$error_message = '';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {

    if ($_SESSION['role'] == 'admin') {
        header('Location: ../dashboard/admin_dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_user, name, email, password, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password']) || $password === $user['password']) {

            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header('Location: ../dashboard/admin_dashboard.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        } else {

            $error_message = 'Email atau password salah!';
        }
    } else {

        $error_message = 'Email atau password salah!';
    }
    
    $stmt->close();
}

$success_message = '';

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
    <title>Login - Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="page-wrapper page-fade" id="pageRoot">

    <div class="brand-panel">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>

        <div class="brand-inner">
            <div class="brand-logo">
                <div class="logo-box"><?= icon('swim', 32) ?></div>
                <div class="logo-box" style="display:flex;align-items:center;justify-content:center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12h20"></path>
                        <path d="M20 12c-2 0-3-2-5-2s-3 2-5 2-3-2-5-2-3 2-5 2"></path>
                        <circle cx="12" cy="6" r="2"></circle>
                        <path d="M8 9l2-2 4 2"></path>
                    </svg>
                </div>
                <span class="logo-text">Swimming Course</span>
            </div>
            <h1 class="brand-heading">Selamat Datang<br>di Swimming Course <span style="display:inline-block;vertical-align:middle;margin-left:8px;"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#60a5fa;"><path d="M2 12h20"></path><path d="M20 12c-2 0-3-2-5-2s-3 2-5 2-3-2-5-2-3 2-5 2"></path></svg></span></h1>
            <h1 class="brand-heading">Selamat Datang<br>di Swimming Course <?= icon('wave', 40, 'inline-icon') ?></h1>
            <p class="brand-desc">Platform manajemen kursus renang modern. Kelola peserta, jadwal, dan program dengan mudah dari satu dashboard.</p>
            <div class="brand-stats">
                <div class="stat"><span class="n">100+</span><span class="l">Peserta</span></div>
                <div class="stat"><span class="n">3</span><span class="l">Program</span></div>
                <div class="stat"><span class="n">2</span><span class="l">Jadwal</span></div>
            </div>
        </div>

        <div class="brand-wave right">
            <svg viewBox="0 0 64 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M64,0 C32,150 0,200 0,450 C0,700 32,750 64,900 L64,0 Z" fill="#fff"/>
            </svg>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-inner">

            <span class="form-badge"><?= icon('swim', 14) ?> Swimming Course</span>
            <h2 class="form-title">Masuk Akun</h2>
            <p class="form-sub">Silakan masukkan email dan password kamu</p>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" style="display:flex;align-items:center;gap:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error" style="display:flex;align-items:center;gap:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <div class="alert alert-success"><?= icon('check', 16) ?> <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?= icon('warning', 16) ?> <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">

                <div class="fg">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="ico">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </span>
                        <input type="email" id="email" name="email"
                               placeholder="Masukkan Email" required autocomplete="email"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="fg">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="ico">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input type="password" id="password" name="password"
                               placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="eye-btn" onclick="toggleEye('password', this)"><?= icon('eye', 18) ?></button>
                        <button type="button" class="eye-btn" onclick="toggleEye('password', this)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-submit" id="loginBtn">
                    Masuk Akun
                </button>
            </form>

            <div class="form-footer">
                Belum punya akun? <a href="register.php">Daftar sekarang</a><br>
                <small>© 2026 AquaLearn Swimming Course</small>
            </div>
        </div>
    </div>

</div>

<script>
    const root = document.getElementById('pageRoot');

    function navigateWithAnim(url) {
        root.classList.add('exiting');
        setTimeout(() => { window.location.href = url; }, 400);
    }

    document.querySelectorAll('a[href]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            navigateWithAnim(a.href);
        });
    });

    function toggleEye(fieldId, btn) {
        const f = document.getElementById(fieldId);
        f.type = f.type === 'password' ? 'text' : 'password';
        const svgEye = `<?= icon('eye', 18) ?>`;
        const svgEyeOff = `<?= icon('eye-off', 18) ?>`;
        btn.innerHTML = f.type === 'password' ? svgEye : svgEyeOff;
        btn.innerHTML = f.type === 'password' ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
    }

    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('loginBtn');

        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<span class="spinner"></span> Memuat...';

        if (!document.querySelector('input[name="login"]')) {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'login'; h.value = '1';
            this.appendChild(h);
        }
        setTimeout(() => root.classList.add('exiting'), 600);
    });

    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    if (window.location.search) history.replaceState(null, '', window.location.pathname);
</script>
</body>
</html>
