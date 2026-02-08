<?php
$page = 'product-details';
include_once __DIR__ . '/database/db_config.php';
$url_prefix = '';

// Fetch product
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM products WHERE slug = :slug AND status = 'active' LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$prod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prod) {
    header("Location: products.php");
    exit;
}

// Images
$gallery = !empty($prod['gallery_images']) ? json_decode($prod['gallery_images'], true) : [];
// Add featured image to start of gallery for slider
if (!empty($prod['featured_image'])) {
    array_unshift($gallery, $prod['featured_image']);
}
$gallery = array_unique($gallery); // Prevent duplicates

// Discount
$discount = 0;
if ($prod['mrp'] > $prod['sales_price']) {
    $discount = round((($prod['mrp'] - $prod['sales_price']) / $prod['mrp']) * 100);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($prod['name']); ?> - WaryChary</title>
    
    <!-- FontAwesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/products.css?v=<?php echo time(); ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="product-detail-container">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="breadcrumb mb-3">
                <a href="index.php">Home</a> / <a href="products.php">Shop</a> / <?php echo htmlspecialchars($prod['name']); ?>
            </div>

            <div class="detail-grid">
                <!-- Left: Gallery -->
                <div class="product-gallery">
                    <div class="gallery-main">
                        <img id="mainImage" src="<?php echo htmlspecialchars($gallery[0] ?? 'assets/images/placeholder.jpg'); ?>" alt="Product">
                    </div>
                    <?php if (count($gallery) > 1): ?>
                        <div class="gallery-thumbs">
                            <?php foreach ($gallery as $index => $img): ?>
                                <div class="thumb-item <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo htmlspecialchars($img); ?>', this)">
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Info -->
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($prod['name']); ?></h1>
                    
                    <div class="ratings">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span class="text-muted ml-2">(4.8 Stars)</span>
                    </div>

                    <div class="detail-price-block">
                        <?php if($discount > 0): ?>
                            <span class="deal-badge"><?php echo $discount; ?>% off</span>
                        <?php endif; ?>
                        
                        <div class="d-flex align-items-baseline mt-2">
                            <span class="huge-price">₹<?php echo number_format($prod['sales_price']); ?></span>
                            <span class="text-muted ml-2">M.R.P.: <span style="text-decoration: line-through;">₹<?php echo number_format($prod['mrp']); ?></span></span>
                        </div>
                        <p class="text-muted small">Inclusive of all taxes</p>
                        
                        <div class="text-success font-weight-bold mt-2">
                            <i class="fas fa-truck"></i> FREE Delivery
                        </div>
                    </div>

                    <?php if($prod['is_free_product_active'] && !empty($prod['free_product_name'])): ?>
                        <div class="free-prod-highlight">
                            <?php if(!empty($prod['free_product_image'])): ?>
                                <img src="<?php echo htmlspecialchars($prod['free_product_image']); ?>" alt="Free Gift">
                            <?php endif; ?>
                            <div>
                                <span class="badge bg-primary text-white mb-1" style="font-size: 0.7rem;">SPECIAL OFFER</span>
                                <div class="font-weight-bold">Free <?php echo htmlspecialchars($prod['free_product_name']); ?></div>
                                <small class="text-muted">Included with your purchase</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <h4 style="font-size: 1.1rem; font-weight: 600;">About this item</h4>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($prod['short_description'])); ?></p>
                    </div>

                    <div class="actions">
                        <button class="btn-buy-now w-100">Buy Now</button>
                    </div>
                </div>
            </div>

            <!-- Full Description -->
            <div class="description-section">
                <h3 class="section-heading">Product Description</h3>
                <div class="description-content">
                    <?php echo $prod['description']; ?>
                </div>
            </div>

            <!-- Customer Reviews -->
            <div class="reviews-section mt-5">
                <h3 class="section-heading">Customer Reviews</h3>
                
                <div class="reviews-container">
                    <!-- Rating Summary -->
                    <div class="rating-summary">
                        <div class="average-rating">
                            <span class="rating-number">4.8</span>
                            <div class="stars">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="total-reviews">Based on 124 reviews</span>
                        </div>
                        <div class="rating-bars">
                            <div class="bar-row">
                                <span>5 star</span>
                                <div class="progress-bar"><div class="fill" style="width: 80%;"></div></div>
                                <span>80%</span>
                            </div>
                            <div class="bar-row">
                                <span>4 star</span>
                                <div class="progress-bar"><div class="fill" style="width: 15%;"></div></div>
                                <span>15%</span>
                            </div>
                            <div class="bar-row">
                                <span>3 star</span>
                                <div class="progress-bar"><div class="fill" style="width: 3%;"></div></div>
                                <span>3%</span>
                            </div>
                            <div class="bar-row">
                                <span>2 star</span>
                                <div class="progress-bar"><div class="fill" style="width: 1%;"></div></div>
                                <span>1%</span>
                            </div>
                            <div class="bar-row">
                                <span>1 star</span>
                                <div class="progress-bar"><div class="fill" style="width: 1%;"></div></div>
                                <span>1%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Review List -->
                    <div class="review-list">
                        <!-- Review 1 -->
                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="user-avatar">P</div>
                                    <div>
                                        <div class="user-name">Priya Sharma</div>
                                        <div class="review-date">Verified Purchase • 2 weeks ago</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="review-text">
                                Absolutely love this product! The quality is amazing and it feels so premium. Delivery was super fast too. Highly recommended for daily use.
                            </div>
                            <div class="review-images">
                                <img src="assets/images/placeholder.jpg" alt="Review Image">
                                <img src="assets/images/placeholder.jpg" alt="Review Image">
                            </div>
                        </div>

                        <!-- Review 2 -->
                        <div class="review-card">
                            <div class="review-header">
                                <div class="user-info">
                                    <div class="user-avatar" style="background: #e91e63;">A</div>
                                    <div>
                                        <div class="user-name">Anjali Gupta</div>
                                        <div class="review-date">Verified Purchase • 1 month ago</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                                </div>
                            </div>
                            <div class="review-text">
                                Good product, worth the price. The packaging was really cute.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function changeImage(src, element) {
            document.getElementById('mainImage').src = src;
            
            // Update Active State
            document.querySelectorAll('.thumb-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');
        }
    </script>

</body>
</html>
