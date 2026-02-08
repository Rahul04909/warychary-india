<?php
// Check if DB is already connected
if (!isset($db)) {
    include_once __DIR__ . '/../database/db_config.php';
    $database = new Database();
    $db = $database->getConnection();
}

// Fetch limit 2 products for homepage (Center aligned, premium look)
$query = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 2";
$stmt = $db->prepare($query);
$stmt->execute();
$home_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="products-section py-5" id="home-products">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Our Best Selling Products</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Discover our premium range of wellness products</p>
        </div>

        <div class="products-grid home-grid">
            <?php if (count($home_products) > 0): ?>
                <?php foreach ($home_products as $prod): 
                    // Calculate Discount Logic
                    $discount = 0;
                    if ($prod['mrp'] > $prod['sales_price']) {
                        $discount = round((($prod['mrp'] - $prod['sales_price']) / $prod['mrp']) * 100);
                    }
                ?>
                <div class="product-card">
                    <div class="badge-overlay">
                        <span class="badge-delivery"><i class="fas fa-truck-fast"></i> Free Delivery</span>
                        <?php if($discount > 0): ?>
                            <span class="badge-offer"><?php echo $discount; ?>% OFF</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-img-wrapper">
                        <a href="product-details.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>">
                            <?php if(!empty($prod['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($prod['featured_image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="product-img">
                            <?php else: ?>
                                <img src="assets/images/placeholder.jpg" alt="No Image" class="product-img">
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="product-content">
                        <a href="product-details.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" class="product-title">
                            <?php echo htmlspecialchars($prod['name']); ?>
                        </a>
                        
                        <?php if($prod['is_free_product_active'] && !empty($prod['free_product_name'])): ?>
                            <div class="free-product-label">
                                <i class="fas fa-gift"></i> Includes Free: <?php echo htmlspecialchars($prod['free_product_name']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="price-block">
                            <div class="price-row">
                                <span class="sales-price">₹<?php echo number_format($prod['sales_price']); ?></span>
                                <?php if($prod['mrp'] > $prod['sales_price']): ?>
                                    <span class="mrp-price">₹<?php echo number_format($prod['mrp']); ?></span>
                                    <span class="discount-text">(<?php echo $discount; ?>% off)</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">M.R.P. incl. of all taxes</small>
                        </div>
                        
                        <a href="product-details.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" class="btn-buy">Buy Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No products available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</section>
