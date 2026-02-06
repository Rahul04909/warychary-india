<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="fas fa-shield-alt"></i> WaryChary Admin
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
                    <i class="fas fa-thumbtack"></i> <span>Products</span>
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
                    <i class="fas fa-images"></i> <span>Orders</span>
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
                    <i class="fas fa-file-alt"></i> <span>Senior Partners</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Senior Partners</a></li>
                <li><a href="#">Add Senior Partner</a></li>
                <li><a href="#">Senior Partner Earnings</a></li>
                <li><a href="#">Senior Partner Payouts</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-users"></i> <span>Partners</span>
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
                    <i class="fas fa-tools"></i> <span>Users</span>
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
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>
