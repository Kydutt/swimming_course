<?php

session_start();
require_once '../function.php';

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Pengguna';

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/logout.css">

</head>

<body>

    <div class="bg-bubble"></div>
    <div class="bg-bubble"></div>
    <div class="bg-bubble"></div>
    <div class="bg-bubble"></div>

    <div class="card" id="card">
        <div class="redirect-bar"></div>

        <div class="logout-icon"><?= icon('wave', 40) ?></div>

        <h1 class="logout-title">
            Sampai jumpa,<br>
            <span class="logout-name"><?php echo htmlspecialchars($user_name); ?>!</span>
        </h1>
        <p class="logout-sub">Kamu telah berhasil logout.<br>Terima kasih sudah bersama kami hari ini.</p>

        <div class="countdown-wrap">
            <svg class="countdown-ring" viewBox="0 0 80 80">
                <defs>
                    <linearGradient id="ringGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%"   stop-color="#1e40af"/>
                        <stop offset="100%" stop-color="#0891b2"/>
                    </linearGradient>
                </defs>
                <circle class="ring-bg"       cx="40" cy="40" r="35"/>
                <circle class="ring-progress" cx="40" cy="40" r="35" id="ringProgress"/>
            </svg>
            <div class="countdown-num" id="countNum">3</div>
        </div>
        <p class="countdown-label">Mengalihkan ke halaman utama...</p>

        <a href="../index.php" class="btn btn-home" id="homeBtn">
            <?= icon('home', 16) ?> Ke Halaman Utama Sekarang
        </a>
        <a href="login.php" class="btn btn-login">
            <?= icon('key', 16) ?> Login Kembali
        </a>
    </div>

    <script>
        const circumference = 2 * Math.PI * 35;
        const ring = document.getElementById('ringProgress');
        const numEl = document.getElementById('countNum');

        ring.style.strokeDasharray  = circumference;
        ring.style.strokeDashoffset = 0;

        let count = 3;

        const interval = setInterval(() => {
            count--;
            numEl.textContent = count;

            const offset = circumference * ((3 - count) / 3);
            ring.style.strokeDashoffset = offset;

            if (count <= 0) {
                clearInterval(interval);

                document.getElementById('card').style.transition = 'opacity .4s, transform .4s';
                document.getElementById('card').style.opacity    = '0';
                document.getElementById('card').style.transform  = 'translateY(-20px)';
                setTimeout(() => { window.location.href = '../index.php'; }, 420);
            }
        }, 1000);

        document.getElementById('homeBtn').addEventListener('click', (e) => {
            clearInterval(interval);
        });
    </script>

</body>
</html>
