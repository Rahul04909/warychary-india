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
        <li class="menu-item <?php echo ($page == 'posts') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <i class="fas fa-thumbtack"></i> <span>Posts</span>
            </a>
        </li>
        <li class="menu-item <?php echo ($page == 'media') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <i class="fas fa-images"></i> <span>Media</span>
            </a>
        </li>
        <li class="menu-item <?php echo ($page == 'pages') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <i class="fas fa-file-alt"></i> <span>Pages</span>
            </a>
        </li>
        <li class="menu-item <?php echo ($page == 'users') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <i class="fas fa-users"></i> <span>Users</span>
            </a>
        </li>
        <li class="menu-item <?php echo ($page == 'tools') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <i class="fas fa-tools"></i> <span>Tools</span>
            </a>
        </li>
        <li class="menu-item <?php echo ($page == 'settings') ? 'active' : ''; ?>">
            <a href="#" class="menu-link">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="../index.php" class="menu-link">
            <i class="fas fa-external-link-alt"></i> <span>Visit Site</span>
        </a>
    </div>
</aside>
