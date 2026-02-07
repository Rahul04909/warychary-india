<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?php echo $url_prefix; ?>../assets/logo/logo.png" alt="WaryChary Admin" class="sidebar-logo">
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
        
        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-box"></i> <span>Products</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Products</a></li>
                <li><a href="#">Add New</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-shopping-cart"></i> <span>Orders</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Orders</a></li>
                <li><a href="#">Pending Orders</a></li>
                <li><a href="#">Completed Orders</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-crown"></i> <span>Senior Partners</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $url_prefix; ?>senior-partners/index.php">All Senior Partners</a></li>
                <li><a href="<?php echo $url_prefix; ?>senior-partners/add-senior-partner.php">Add Senior Partner</a></li>
                <li><a href="#">Senior Partner Earnings</a></li>
                <li><a href="#">Senior Partner Payouts</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-handshake"></i> <span>Partners</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Partners</a></li>
                <li><a href="#">Add Partner</a></li>
                <li><a href="#">Partner Earnings</a></li>
                <li><a href="#">Partner Payouts</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-users"></i> <span>Users</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Users</a></li>
                <li><a href="#">Add User</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-cog"></i> <span>Settings</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">General</a></li>
                <li><a href="#">Smtp Settings</a></li>
                <li><a href="#">Razorpay Settings</a></li>
                <li><a href="#">Manage Commisions</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="<?php echo $url_prefix; ?>logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>
