document.addEventListener('DOMContentLoaded', function () {

    // Sidebar Toggle Logic
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.createElement('div');

    // Add overlay for mobile
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Overlay CSS dynamically
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.5);
        z-index: 990;
        display: none;
        opacity: 0;
        transition: opacity 0.3s;
    `;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                overlay.style.display = 'block';
                setTimeout(() => overlay.style.opacity = '1', 10);
            } else {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 300);
            }
        });
    }

    // Close Sidebar when clicking overlay
    overlay.addEventListener('click', function () {
        sidebar.classList.remove('active');
        overlay.style.opacity = '0';
        setTimeout(() => overlay.style.display = 'none', 300);
    });

    // Close sidebar on window resize if switching to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            overlay.style.display = 'none';
        }
    });

    // Sidebar Sub-menu Toggle (Accordion)
    const submenuToggles = document.querySelectorAll('.has-submenu > .menu-link');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();

            const parent = this.parentElement;
            const isOpen = parent.classList.contains('open');

            // Close all other submenus? (Optional accordion style)
            // Uncomment below if you want only one open at a time
            /*
            document.querySelectorAll('.menu-item.has-submenu').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('open');
                }
            });
            */

            // Toggle current
            parent.classList.toggle('open');
        });
    });

    // Simple Dropdown interactions (if any added later)
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            this.nextElementSibling.classList.toggle('show');
        });
    });

});
