<?php
include_once __DIR__ . '/database/db_config.php';
$url_prefix = '';
// include_once __DIR__ . '/includes/header.php'; // Moved below

$database = new Database();
$db = $database->getConnection();

// Fetch product details if slug is provided
$product = null;
$product_slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

if ($product_slug) {
    $query = "SELECT * FROM products WHERE slug = :slug AND status = 'active' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':slug', $product_slug);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Redirect if no product found (and not a cart checkout - assuming direct buy for now as per prompt)
if (!$product) {
    echo "<script>alert('Product not found!'); window.location.href='index.php';</script>";
    exit;
}

// Calculate Totals
$price = $product['sales_price'];
$mrp = $product['mrp'];
$total = $price * $qty;
$discount = $mrp > $price ? round((($mrp - $price) / $mrp) * 100) : 0;
$savings = ($mrp - $price) * $qty;

// Fetch Razorpay Key
$rzp_query = "SELECT key_id FROM razorpay_settings WHERE mode = 'test' LIMIT 1"; // Default to test, should switch based on setting
$rzp_stmt = $db->prepare("SELECT key_id, mode FROM razorpay_settings LIMIT 1");
$rzp_stmt->execute();
$rzp_settings = $rzp_stmt->fetch(PDO::FETCH_ASSOC);
$razorpay_key = $rzp_settings ? $rzp_settings['key_id'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - WaryChary</title>
    
    <!-- FontAwesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/products.css?v=<?php echo time(); ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .checkout-section { background: #f9fafb; min-height: 100vh; padding: 40px 0; }
        .checkout-container { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 25px; border: 1px solid #e5e7eb; }
        .card-header { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .card-header h3 { font-size: 1.25rem; color: #1f2937; font-weight: 600; margin: 0; }
        
        .form-group { margin-bottom: 1rem; }
        .form-label { font-weight: 500; font-size: 0.9rem; color: #374151; margin-bottom: 6px; display: block; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.15s; }
        .form-control:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        
        .order-item { display: flex; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid #f3f4f6; margin-bottom: 15px; }
        .order-item img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        .item-info h4 { font-size: 1rem; margin: 0 0 5px; color: #111827; }
        .item-price { font-weight: 600; color: #111827; }
        .item-qty { color: #6b7280; font-size: 0.85rem; }
        
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; color: #4b5563; }
        .summary-row.total { font-weight: 700; color: #111827; font-size: 1.2rem; border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 10px; }
        .free-badge { background: #dcfce7; color: #15803d; font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
        
        .btn-checkout { width: 100%; background: #10b981; color: #fff; padding: 14px; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .btn-checkout:hover { background: #059669; }
        
        .secure-badge { text-align: center; margin-top: 15px; color: #6b7280; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 6px; }

        @media (max-width: 768px) {
            .checkout-container { grid-template-columns: 1fr; }
            .checkout-section { padding: 20px 0; }
        }
    </style>
</head>
<body>

    <?php include_once __DIR__ . '/includes/topbar.php'; ?>
    <?php include_once __DIR__ . '/includes/header.php'; ?>

    <div class="checkout-section">
        <div class="container">
            <form id="checkoutForm">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="qty" value="<?php echo $qty; ?>">
                <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

                <div class="checkout-container">
                    <!-- Billing Details -->
                    <div class="checkout-details">
                        <div class="card">
                            <div class="card-header">
                                <h3>Billing Details</h3>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" required placeholder="John Doe">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="tel" name="mobile" class="form-control" required placeholder="9876543210" pattern="[0-9]{10}">
                                </div>
                                <div class="col-12 form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                                </div>
                                <div class="col-12 form-group">
                                    <label class="form-label">Full Address</label>
                                    <textarea name="address" class="form-control" rows="2" required placeholder="House No, Street, Area"></textarea>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control" required placeholder="110001" pattern="[0-9]{6}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">City/District</label>
                                    <input type="text" name="city" class="form-control" required placeholder="New Delhi">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control" required placeholder="Delhi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <div class="card">
                            <div class="card-header">
                                <h3>Order Summary</h3>
                            </div>
                            
                            <div class="order-item">
                                <img src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Product">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <div class="item-price">₹<?php echo number_format($price); ?> <span class="text-muted" style="font-weight:400; font-size:0.9em;">x <?php echo $qty; ?></span></div>
                                    <?php if($product['is_free_product_active']): ?>
                                        <small class="text-success"><i class="fas fa-gift"></i> Free: <?php echo htmlspecialchars($product['free_product_name']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span>₹<?php echo number_format($total); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Delivery Charges</span>
                                    <span class="free-badge">FREE</span>
                                </div>
                                <?php if($savings > 0): ?>
                                <div class="summary-row text-success">
                                    <span>Total Savings</span>
                                    <span>- ₹<?php echo number_format($savings); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="summary-row total">
                                    <span>Total Payable</span>
                                    <span>₹<?php echo number_format($total); ?></span>
                                </div>
                            </div>

                            <button type="button" id="payBtn" class="btn-checkout mt-3">
                                Proceed to Pay ₹<?php echo number_format($total); ?>
                            </button>
                            
                            <div class="secure-badge">
                                <i class="fas fa-lock"></i> Secure Payment by Razorpay
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
    document.getElementById('payBtn').onclick = function(e){
        e.preventDefault();
        
        // Basic Verification
        var form = document.getElementById('checkoutForm');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var formData = new FormData(form);
        
        // 1. Create Order on Backend
        fetch('process-checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // 2. Open Razorpay Checkout
                var options = {
                    "key": "<?php echo $razorpay_key; ?>", 
                    "amount": data.order.amount, 
                    "currency": "INR",
                    "name": "WaryChary",
                    "description": "Order Payment",
                    "image": "assets/logo/logo.png",
                    "order_id": data.order.id, 
                    "handler": function (response){
                        // 3. Verify Payment
                        verifyPayment(response, data.internal_order_id);
                    },
                    "prefill": {
                        "name": formData.get('name'),
                        "email": formData.get('email'),
                        "contact": formData.get('mobile')
                    },
                    "theme": {
                        "color": "#10b981"
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong!');
        });
    }

    function verifyPayment(response, internalOrderId) {
        fetch('verify-payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_signature: response.razorpay_signature,
                internal_order_id: internalOrderId
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.href = "order-success.php?id=" + internalOrderId;
            } else {
                alert("Payment Verification Failed");
            }
        });
    }
    </script>
</body>
</html>
