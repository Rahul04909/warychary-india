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


<?php
// Fetch Reviews
$reviews_query = "SELECT * FROM reviews WHERE product_id = :pid AND status = 'approved' ORDER BY created_at DESC";
$stmt_rev = $db->prepare($reviews_query);
$stmt_rev->bindParam(':pid', $prod['id']);
$stmt_rev->execute();
$reviews = $stmt_rev->fetchAll(PDO::FETCH_ASSOC);

// Calculate Ratings
$total_reviews = count($reviews);
$average_rating = 0;
$rating_counts = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];

if ($total_reviews > 0) {
    $sum_ratings = 0;
    foreach ($reviews as $rev) {
        $sum_ratings += $rev['rating'];
        $rating_counts[$rev['rating']]++;
    }
    $average_rating = round($sum_ratings / $total_reviews, 1);
}
?>

            <!-- Customer Reviews -->
            <div class="reviews-section mt-5" id="reviews">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="section-heading mb-0">Customer Reviews</h3>
                    <button class="btn btn-primary" onclick="toggleReviewForm()">Write a Review</button>
                </div>

                <!-- Review Form (Hidden by default) -->
                <div id="reviewForm" style="display: none; background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                    <h4>Write your review</h4>
                    <form action="process-review.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                        <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">
                        <input type="hidden" name="submit_review" value="1">
                        
                        <div class="form-group mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Rating</label>
                            <select name="rating" class="form-control" required>
                                <option value="5">5 Stars (Excellent)</option>
                                <option value="4">4 Stars (Good)</option>
                                <option value="3">3 Stars (Average)</option>
                                <option value="2">2 Stars (Poor)</option>
                                <option value="1">1 Star (Terrible)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Review</label>
                            <textarea name="review_text" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Images (Optional)</label>
                            <input type="file" name="review_images[]" class="form-control" multiple accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">Submit Review</button>
                    </form>
                </div>
                
                <div class="reviews-container">
                    <!-- Rating Summary -->
                    <div class="rating-summary">
                        <div class="average-rating">
                            <span class="rating-number"><?php echo $average_rating; ?></span>
                            <div class="stars">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="<?php echo $i <= $average_rating ? 'fas' : 'far'; ?> fa-star" style="color: #ffd700;"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="total-reviews">Based on <?php echo $total_reviews; ?> reviews</span>
                        </div>
                        <div class="rating-bars">
                            <?php for($i=5; $i>=1; $i--): 
                                $percent = $total_reviews > 0 ? ($rating_counts[$i] / $total_reviews) * 100 : 0;
                            ?>
                            <div class="bar-row">
                                <span><?php echo $i; ?> star</span>
                                <div class="progress-bar"><div class="fill" style="width: <?php echo $percent; ?>%;"></div></div>
                                <span><?php echo round($percent); ?>%</span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Review List -->
                    <div class="review-list">
                        <?php if ($total_reviews > 0): ?>
                            <?php foreach ($reviews as $rev): 
                                $rev_images = !empty($rev['review_images']) ? json_decode($rev['review_images'], true) : [];
                            ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="user-info">
                                        <div class="user-avatar"><?php echo strtoupper(substr($rev['user_name'], 0, 1)); ?></div>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($rev['user_name']); ?></div>
                                            <div class="review-date">Verified Purchase • <?php echo date('M d, Y', strtotime($rev['created_at'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="<?php echo $i <= $rev['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="review-text">
                                    <?php echo nl2br(htmlspecialchars($rev['review_text'])); ?>
                                </div>
                                <?php if (!empty($rev_images)): ?>
                                <div class="review-images">
                                    <?php foreach ($rev_images as $img_path): ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Review Image">
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No reviews yet. Be the first to write a review!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script>
            function toggleReviewForm() {
                var form = document.getElementById('reviewForm');
                if (form.style.display === 'none') {
                    form.style.display = 'block';
                } else {
                    form.style.display = 'none';
                }
            }
            </script>

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
