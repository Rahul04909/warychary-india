<!-- Topbar -->
<div class="topbar">
    <div class="container">
        <div class="topbar-content">
            <div class="topbar-left">
                <p>Your One-Stop Solution Design Development Manufacturing</p>
            </div>
            <div class="topbar-center">
                <a href="tel:+917988472662"><i class="fas fa-phone-alt"></i> +91-7988472662</a>
                <a href="mailto:Support@WaryChary.com"><i class="fas fa-envelope"></i> Support@WaryChary.com</a>
            </div>
            <div class="topbar-right">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</div>

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
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Become a partner</a></li>
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
