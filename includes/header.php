<header class="header-main">
    <div class="container">
        <div class="header-wrapper">
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <img src="assets/logo/logo.png" alt="WaryChary Logo">
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button class="mobile-toggle" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navigation -->
            <nav class="navbar" id="navbar">
                <ul>
                    <li><a href="#">Shop By Category</a></li>
                    <li><a href="#">Shop By Size</a></li>
                    <li><a href="#">Best Sellers</a></li>
                    <li class="nav-item-badge">
                        <a href="#">Combos</a>
                        <span class="nav-badge">Best Deals</span>
                    </li>
                    <li><a href="#">Shop All</a></li>
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
                <a href="#" class="icon-btn" title="Search">
                    <!-- SVG Search Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </a>
                <a href="#" class="icon-btn" title="Cart">
                    <!-- SVG Cart Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </a>
            </div>
        </div>
    </div>
</header>
<!-- FontAwesome for the burger icon (CDN) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('navbar').classList.toggle('active');
        const icon = this.querySelector('i');
        if (icon.classList.contains('fa-bars')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
</script>
