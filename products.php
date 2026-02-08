<?php
$page = 'products';
include_once __DIR__ . '/database/db_config.php';
$url_prefix = '';

// Fetch products
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Products - WaryChary</title>
    
    <!-- FontAwesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/products.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <!-- Hero / Title -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">Our Premium Products</h1>
            <div class="breadcrumb">Home / Shop</div>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="container">
        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $prod): 
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
                <div class="col-12 text-center py-5">
                    <h3>No products available at the moment.</h3>
                    <p>Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
