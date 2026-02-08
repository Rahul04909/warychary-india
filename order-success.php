<?php
include_once __DIR__ . '/database/db_config.php';
$url_prefix = '';
// include_once __DIR__ . '/includes/header.php'; // Moved below

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$database = new Database();
$db = $database->getConnection();

// Fetch Order Details
$query = "SELECT * FROM orders WHERE id = :id AND payment_status = 'paid'";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='container py-5 text-center'><h3>Order not found or payment pending.</h3><a href='index.php' class='btn btn-primary mt-3'>Go Home</a></div>";
    // include_once __DIR__ . '/includes/footer.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - WaryChary</title>
    
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
</head>
<body>

    <?php include_once __DIR__ . '/includes/topbar.php'; ?>
    <?php include_once __DIR__ . '/includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x" style="color: #000;"></i>
                        </div>
                        <h2 class="mb-2 fw-bold" style="color: #000;">Thank You for Your Order!</h2>
                        <p class="text-muted mb-4">Your order has been placed successfully. A confirmation email has been sent to <strong><?php echo htmlspecialchars($order['customer_email']); ?></strong>.</p>
                        
                        <div class="bg-light p-4 rounded mb-4 text-start">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <small class="text-muted d-block">Order ID:</small>
                                    <strong>#<?php echo htmlspecialchars($order['id']); ?></strong>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <small class="text-muted d-block">Date:</small>
                                    <strong><?php echo date('F j, Y', strtotime($order['created_at'])); ?></strong>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <small class="text-muted d-block">Amount Paid:</small>
                                    <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <small class="text-muted d-block">Payment ID:</small>
                                    <strong><?php echo htmlspecialchars($order['payment_id']); ?></strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                        <a href="index.php" class="btn btn-primary px-4 py-2">Continue Shopping</a>
                        <a href="generate-invoice.php?id=<?php echo $order_id; ?>&download=true" class="btn btn-dark px-4 py-2" target="_blank"><i class="fas fa-file-invoice me-2"></i> Download Invoice</a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
