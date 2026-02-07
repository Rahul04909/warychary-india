<?php
$page = $page ?? 'dashboard';
?>
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?php echo $url_prefix; ?>../assets/logo/logo.png" alt="WaryChary" class="sidebar-logo">
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <a href="<?php echo $url_prefix; ?>index.php" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </div>
            </a>
        </li>
        
        <li class="menu-item <?php echo ($page == 'profile') ? 'active' : ''; ?>">
            <a href="<?php echo $url_prefix; ?>profile.php" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-user-circle"></i> <span>My Profile</span>
                </div>
            </a>
        </li>

        <li class="menu-item <?php echo ($page == 'earnings') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-wallet"></i> <span>My Earnings</span>
                </div>
            </a>
        </li>

        <li class="menu-item <?php echo ($page == 'team') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-users"></i> <span>My Team</span>
                </div>
            </a>
        </li>

         <li class="menu-item">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-question-circle"></i> <span>Support</span>
                </div>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="<?php echo $url_prefix; ?>logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>
