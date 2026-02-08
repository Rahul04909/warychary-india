<?php
include_once __DIR__ . '/database/db_config.php';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';

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
    include_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="mb-4 text-success">
                        <i class="fas fa-check-circle fa-5x"></i>
                    </div>
                    <h2 class="mb-3">Thank You for Your Order!</h2>
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
                    
                    <a href="index.php" class="btn btn-primary px-4 py-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
