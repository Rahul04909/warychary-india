<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="fas fa-shield-alt"></i> WaryChary Admin
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <a href="index.php" class="menu-link">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
        </li>
        
        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-thumbtack"></i> <span>Posts</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Posts</a></li>
                <li><a href="#">Add New</a></li>
                <li><a href="#">Categories</a></li>
                <li><a href="#">Tags</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-images"></i> <span>Media</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">Library</a></li>
                <li><a href="#">Add New</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-file-alt"></i> <span>Pages</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">All Pages</a></li>
                <li><a href="#">Add New</a></li>
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
                <li><a href="#">Add New</a></li>
                <li><a href="#">Profile</a></li>
            </ul>
        </li>

        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">
                <div class="menu-text">
                    <i class="fas fa-tools"></i> <span>Tools</span>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="#">Available Tools</a></li>
                <li><a href="#">Import</a></li>
                <li><a href="#">Export</a></li>
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
                <li><a href="#">Writing</a></li>
                <li><a href="#">Reading</a></li>
                <li><a href="#">Discussion</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</aside>
