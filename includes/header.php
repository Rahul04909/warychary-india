
<header class="header-main">
    <div class="container">
        <div class="header-wrapper">
            <!-- Logo -->
            <div class="logo">
                <a href="<?php echo $url_prefix; ?>index.php">
                    <img src="<?php echo $url_prefix; ?>assets/logo/logo.png" alt="WaryChary Logo">
                </a>
            </div>

            <!-- Navigation -->
            <nav class="navbar" id="navbar">
                <div class="nav-header">
                    <span class="nav-title">Menu</span>
                    <button class="mobile-menu-close" id="mobile-menu-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <ul>
                    <li><a href="<?php echo $url_prefix; ?>index.php">Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="<?php echo $url_prefix; ?>products.php">Shop</a></li>
                    <li><a href="<?php echo $url_prefix; ?>become-a-partner.php">Become a partner</a></li>
                    <li><a href="#">Period Education</a></li>
                    <li><a href="#">Contact us</a></li>
                </ul>
            </nav>

            <!-- Header Icons -->
            <!-- Header Actions -->
            <div class="header-actions">
                <?php 
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $dashboard_link = '';
                $account_label = 'My Account';

                if (isset($_SESSION['user_id'])) {
                    $dashboard_link = $url_prefix . 'user/index.php';
                } elseif (isset($_SESSION['partner_id'])) {
                    $dashboard_link = $url_prefix . 'partner/index.php';
                    $account_label = 'Partner Dashboard';
                } elseif (isset($_SESSION['senior_partner_id'])) {
                    $dashboard_link = $url_prefix . 'senior-partner/index.php';
                    $account_label = 'Senior Dashboard';
                }

                if ($dashboard_link): ?>
                    <a href="<?php echo $dashboard_link; ?>" class="header-action-btn btn-account">
                        <i class="fas fa-user-circle me-1"></i> <?php echo $account_label; ?>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $url_prefix; ?>partner/login.php" class="header-action-btn btn-login-outline">
                        Partner Login
                    </a>
                    <a href="<?php echo $url_prefix; ?>user/login.php" class="header-action-btn btn-login-outline">
                        User Login
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Toggle -->
            <button class="mobile-toggle" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

</header>

<script>
    const navbar = document.getElementById('navbar');
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('mobile-menu-close');

    function toggleMenu() {
        navbar.classList.toggle('active');
        document.body.classList.toggle('no-scroll');
    }

    mobileBtn.addEventListener('click', toggleMenu);
    
    if(closeBtn) {
        closeBtn.addEventListener('click', toggleMenu);
    }
</script>
