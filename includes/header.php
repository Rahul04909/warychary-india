
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
            <div class="header-icons">
                <a href="#" class="icon-btn" title="Profile">
                    <!-- SVG Profile Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
                <a href="#" class="icon-btn" title="Track Order">
                    <!-- SVG Truck/Track Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </a>
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
