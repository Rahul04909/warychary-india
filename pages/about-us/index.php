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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/footer.css?v=<?php echo time(); ?>">
    
    <style>
        /* Custom Styles for About Us Page */
        :root {
            --brand-primary: #7e4bbb;
            --brand-secondary: #6366f1; /* Keeping accent */
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        .page-header-section {
            background: linear-gradient(135deg, var(--brand-primary), #4c1d95);
            padding: 80px 0;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('<?php echo $url_prefix; ?>assets/images/pattern.png');
            opacity: 0.1;
        }
        
        .section-title h2 {
            font-weight: 700;
            color: var(--brand-primary);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .feature-card {
            background: #fff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            border-top: 5px solid var(--brand-primary);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(126, 75, 187, 0.15);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: rgba(126, 75, 187, 0.1);
            color: var(--brand-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            background: var(--brand-primary);
            color: #fff;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--brand-primary);
            margin-bottom: 0.5rem;
        }
        
        .cert-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s;
            border: 1px solid #eee;
        }
        
        .cert-card:hover {
            transform: translateY(-5px);
            border-color: var(--brand-primary);
        }
        
        .cert-img {
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<?php include_once $url_prefix . 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="page-header-section">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">About WaryChary</h1>
        <p class="lead mb-0 opacity-75">Empowering health, wealth, and happiness across India.</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="position-relative">
                    <img src="<?php echo $url_prefix; ?>assets/images/about-hero.jpg" alt="Our Story" class="img-fluid rounded-4 shadow-lg" onerror="this.src='https://placehold.co/600x400/7e4bbb/ffffff?text=WaryChary+Story'">
                    <div class="position-absolute bottom-0 start-0 bg-white p-3 rounded-top-end-4 shadow-sm d-none d-md-block">
                        <span class="fw-bold text-primary">Est. 2024</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <span class="text-uppercase text-primary fw-bold small tracking-wider">Our Story</span>
                <h2 class="fw-bold mb-4 display-6">Who We Are</h2>
                <p class="text-muted mb-4 lead" style="font-size: 1.1rem;">
                    WaryChary is a movement designed to transform lives. Founded with a dual mission of improving menstrual hygiene awareness and providing sustainable financial independence.
                </p>
                <p class="text-muted mb-4">
                    We connect premium quality products with a robust earning model, bridging the gap between essential care and entrepreneurship. We believe that every individual deserves access to high-quality health products and the opportunity to build a secure financial future.
                </p>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-2"></i> Premium Quality Products
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-2"></i> Sustainable Income Model
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-2"></i> Nationwide Network
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-2"></i> Women Empowerment
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certifications Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <span class="text-uppercase text-primary fw-bold small">Trust & Quality</span>
            <h2 class="mb-0">Our Certifications</h2>
            <p class="mt-2">Recognized for excellence and compliance.</p>
        </div>
        
        <div class="row justify-content-center g-4">
            <div class="col-md-3 col-6">
                <div class="cert-card">
                    <img src="<?php echo $url_prefix; ?>assets/images/gst.png" alt="GST Registered" class="cert-img" onerror="this.src='https://placehold.co/150x80/png?text=GST+Registered'">
                    <h5 class="fw-bold mb-0 text-dark">GST Registered</h5>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="cert-card">
                    <img src="<?php echo $url_prefix; ?>assets/images/gmp.png" alt="GMP Certified" class="cert-img" onerror="this.src='https://placehold.co/150x80/png?text=GMP+Certified'">
                    <h5 class="fw-bold mb-0 text-dark">GMP Certified</h5>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="cert-card">
                    <img src="<?php echo $url_prefix; ?>assets/images/iso.png" alt="ISO Certified" class="cert-img" onerror="this.src='https://placehold.co/150x80/png?text=ISO+9001:2015'">
                    <h5 class="fw-bold mb-0 text-dark">ISO 9001:2015</h5>
                </div>
            </div>
             <div class="col-md-3 col-6">
                <div class="cert-card">
                    <img src="<?php echo $url_prefix; ?>assets/images/fssai.png" alt="FSSAI" class="cert-img" onerror="this.src='https://placehold.co/150x80/png?text=FSSAI'">
                    <h5 class="fw-bold mb-0 text-dark">MSME</h5>
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
<section class="py-5 text-white text-center" style="background: var(--brand-primary);">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
