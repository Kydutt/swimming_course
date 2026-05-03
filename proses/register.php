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

if (isset($_POST['register'])) {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Konfirmasi password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal 6 karakter!';
    } else {
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = 'Email sudah terdaftar!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';

            $insert_stmt = $conn->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                header('Location: login.php?registered=success');
                exit;
            } else {
                $error_message = 'Terjadi kesalahan. Silakan coba lagi.';
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
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="page-wrapper page-fade" id="pageRoot" style="grid-template-columns: 1fr 1fr;">

    <div class="form-panel" style="border-right: 1px solid var(--gray-100);">
        <div class="form-inner">

            
            <div class="form-badge" style="display:inline-flex;align-items:center;gap:6px;">
                <div class="logo-box" style="display:flex;align-items:center;justify-content:center;">
                    <?= icon('swim', 18) ?>
                </div>
                Swimming Course
            </div>
            <h2 class="form-title">Buat Akun</h2>
            <p class="form-sub">Bergabung dan mulai perjalanan renangmu!</p>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error" id="alertBox" style="display:flex;align-items:center;gap:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <div class="alert alert-error" id="alertBox"><?= icon('warning', 16) ?> <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">

                <div class="fg">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-wrap">
                        <span class="ico">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="name" name="name"
                               placeholder="Masukkan Nama Lengkap" required autocomplete="name"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                </div>

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
                               placeholder="Masukkan Password" required autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <button type="button" class="eye-btn" onclick="toggleEye('password', this)"><?= icon('eye', 18) ?></button>
                        <button type="button" class="eye-btn" onclick="toggleEye('password', this)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                    <div class="strength-text" id="strengthText"></div>
                </div>

                <div class="fg">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <span class="ico">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password"
                               placeholder="Konfirmasi Password" required autocomplete="new-password"
                               oninput="checkMatch(this)">
                        <button type="button" class="eye-btn" onclick="toggleEye('confirm_password', this)"><?= icon('eye', 18) ?></button>
                        <button type="button" class="eye-btn" onclick="toggleEye('confirm_password', this)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-submit" id="regBtn">
                    Daftar Akun
                </button>
            </form>

            <div class="form-footer">
                Sudah punya akun? <a href="login.php">Masuk sekarang</a><br>
                <small>© 2026 Swimming Course</small>
            </div>
        </div>
    </div>

    <div class="brand-panel">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>

        <div class="brand-wave left">
            <svg viewBox="0 0 64 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M64,0 C32,150 0,200 0,450 C0,700 32,750 64,900 L64,0 Z" fill="#fff"/>
            </svg>
        </div>

        <div class="brand-inner">
            <div class="brand-logo">
                <div class="logo-box" style="display:flex;align-items:center;justify-content:center;">
                    <?= icon('swim', 32) ?>
                </div>
                <span class="logo-text">Swimming Course</span>
            </div>
            
            <h1 class="brand-heading">Mulai<br>Perjalananmu<br>Bersama Kami <?= icon('wave', 40, 'inline-icon') ?></h1>
            <p class="brand-desc">Daftarkan dirimu dan nikmati program renang berkualitas bersama instruktur berpengalaman.</p>
            <div class="brand-stats">
                <div class="stat"><span class="n"><?= icon('check', 16) ?></span><span class="l">Gratis Daftar</span></div>
                <div class="stat"><span class="n">3</span><span class="l">Kelas</span></div>
                <div class="stat"><span class="n"><?= icon('lightning', 16) ?></span><span class="l">Langsung Aktif</span></div>
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

    function checkStrength(val) {
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');
        if (!val) { fill.style.width = '0%'; text.textContent = ''; return; }
        let s = 0;
        if (val.length >= 6)  s++;
        if (val.length >= 10) s++;
        if (/[A-Z]/.test(val)) s++;
        if (/[0-9]/.test(val)) s++;
        if (/[^A-Za-z0-9]/.test(val)) s++;
        const lv = [
            { w: '20%', c: '#ef4444', t: 'Sangat lemah' },
            { w: '40%', c: '#f97316', t: 'Lemah' },
            { w: '60%', c: '#eab308', t: 'Cukup' },
            { w: '80%', c: '#22c55e', t: 'Kuat' },
            { w: '100%',c: '#16a34a', t: 'Sangat kuat' },
        ][Math.min(s, 4)];
        fill.style.width = lv.w;
        fill.style.background = lv.c;
        text.textContent = lv.t;
        text.style.color = lv.c;
    }

    function checkMatch(el) {
        const match = el.value === document.getElementById('password').value;
        el.style.borderColor = el.value ? (match ? '#22c55e' : '#ef4444') : '';
    }

    document.getElementById('registerForm').addEventListener('submit', function () {
        const btn = document.getElementById('regBtn');

        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<span class="spinner"></span> Mendaftarkan...';

        if (!document.querySelector('input[name="register"]')) {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'register'; h.value = '1';
            this.appendChild(h);
        }
        setTimeout(() => root.classList.add('exiting'), 600);
    });

    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = 'opacity .4s, transform .4s';
            alertBox.style.opacity = '0';
            alertBox.style.transform = 'translateY(-4px)';
            setTimeout(() => alertBox.remove(), 400);
        }, 4000);
    }
</script>
</body>
</html>
