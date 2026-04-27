<nav class="navbar" id="navbar">
    <div class="container">
        <div class="nav-wrapper">
            <a href="#home" class="logo">
                <span class="logo-text">Swimming Course</span>
            </a>
            
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#registration" class="nav-link">Contact</a></li>
                
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                    <li><span class="nav-link">Hai, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="dashboard/admin_dashboard.php" class="nav-link nav-cta" style="background: #2563eb;">Dashboard Admin</a></li>
                    <?php endif; ?>
                    <li><a href="proses/logout.php" class="nav-link nav-cta" style="background: #ef4444;">Logout</a></li>
                <?php else: ?>
                    <li><a href="proses/login.php" class="nav-link">Login</a></li>
                    <li><a href="proses/register.php" class="nav-link nav-cta">Daftar Sekarang</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
