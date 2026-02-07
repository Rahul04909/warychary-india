<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="../assets/logo/logo.png" alt="WaryChary" class="sidebar-logo">
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <a href="index.php" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </div>
            </a>
        </li>
        
        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-handshake"></i> <span>My Partners</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Partners</a></li>
                <li><a href="#">Add New Partner</a></li>
                <li><a href="#">Partner Performance</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-wallet"></i> <span>Earnings</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">My Commission</a></li>
                <li><a href="#">Payout History</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-chart-line"></i> <span>Reports</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">Sales Report</a></li>
                <li><a href="#">Team Activity</a></li>
            </ul>
        </li>

        <li class="menu-item <?php echo ($page == 'profile') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-user-circle"></i> <span>My Profile</span>
                </div>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>
