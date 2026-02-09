<?php
$url_prefix = '../../';
$page_title = "About Us - WaryChary";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Discover WaryChary's mission to empower individuals through sustainable period products and digital earning opportunities. Learn about our vision, values, and the team driving change across India.">
    <meta name="keywords" content="WaryChary, About Us, Period Care, Digital Earnings, Women Empowerment, Sustainable Products, Network Marketing India">
    <meta name="author" content="WaryChary">
    <link rel="canonical" href="https://warychary.com/pages/about-us/">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://warychary.com/pages/about-us/">
    <meta property="og:title" content="About WaryChary - Empowering Lives">
    <meta property="og:description" content="Join our mission to revolutionize menstrual hygiene and financial independence in India.">
    <meta property="og:image" content="https://warychary.com/assets/images/about-hero.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://warychary.com/pages/about-us/">
    <meta property="twitter:title" content="About WaryChary - Empowering Lives">
    <meta property="twitter:description" content="Join our mission to revolutionize menstrual hygiene and financial independence in India.">
    <meta property="twitter:image" content="https://warychary.com/assets/images/about-hero.jpg">

    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "WaryChary",
      "url": "https://warychary.com",
      "logo": "https://warychary.com/assets/logo/logo.png",
      "sameAs": [
        "https://www.facebook.com/warychary",
        "https://twitter.com/warychary",
        "https://www.instagram.com/warychary"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+91-1234567890",
        "contactType": "customer service"
      }
    }
    </script>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/footer.css?v=<?php echo time(); ?>">
    
    <style>
        /* Custom Styles for About Us Page */
        .page-header-section {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('<?php echo $url_prefix; ?>assets/images/about-hero.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            color: #fff;
            text-align: center;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .section-title h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
            border-top: 4px solid #6366f1;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: #e0e7ff;
            color: #6366f1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1.5rem;
        }

        .team-member {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .team-img {
            height: 250px;
            width: 100%;
            object-fit: cover;
            background-color: #f1f5f9;
        }

        .team-info {
            padding: 1.5rem;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #6366f1;
        }
    </style>
</head>
<body>

<?php include_once $url_prefix . 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="page-header-section">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">About WaryChary</h1>
        <p class="lead mb-0">Empowering health, wealth, and happiness across India.</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?php echo $url_prefix; ?>assets/images/our-story.jpg" alt="Our Story" class="img-fluid rounded-3 shadow-lg" onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=WaryChary+Story'">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Who We Are</h2>
                <p class="text-muted mb-4">
                    WaryChary is more than just a brand; it's a movement designed to transform lives. Founded with a dual mission of improving menstrual hygiene awareness and providing sustainable financial independence, we connect premium quality products with a robust earning model.
                </p>
                <p class="text-muted mb-4">
                    We believe that every individual deserves access to high-quality health products and the opportunity to build a secure financial future. Our platform bridges the gap between essential care and entrepreneurship, empowering partners across the nation.
                </p>
                <div class="row g-4 mt-2">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                            <span class="fw-medium">Premium Quality</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                            <span class="fw-medium">Sustainable Income</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                            <span class="fw-medium">Nationwide Network</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                            <span class="fw-medium">Community Focus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto bg-primary-subtle text-primary">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="h4 mb-3">Our Vision</h3>
                    <p class="text-muted mb-0">To become India's most trusted platform for personal care and financial empowerment, creating a healthier and wealthier society.</p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="feature-card text-center" style="border-top-color: #10b981;">
                    <div class="feature-icon mx-auto bg-success-subtle text-success">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="h4 mb-3">Our Mission</h3>
                    <p class="text-muted mb-0">To provide accessible, high-quality sanitary products while enabling millions of individuals to achieve financial freedom through our partnership program.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="text-muted">Happy Customers</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="text-muted">Active Partners</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="text-muted">Cities Covered</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="text-muted">Support System</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container py-4">
        <h2 class="fw-bold mb-3">Ready to Start Your Journey?</h2>
        <p class="lead mb-4 op-8">Join thousands of successful partners who are changing their lives with WaryChary.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?php echo $url_prefix; ?>become-a-partner.php" class="btn btn-light btn-lg px-5 fw-bold text-primary">Become a Partner</a>
            <a href="<?php echo $url_prefix; ?>products.php" class="btn btn-outline-light btn-lg px-5 fw-bold">View Products</a>
        </div>
    </div>
</section>

<?php include_once $url_prefix . 'includes/footer.php'; ?>
</body>
</html>
