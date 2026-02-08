<?php
$page = 'orders';
include 'includes/header.php';

// Database Connection
if (!isset($db)) {
    include_once __DIR__ . '/../database/db_config.php';
    $database = new Database();
    $db = $database->getConnection();
}

$user_id = $_SESSION['user_id'];

// Fetch Orders
$query = "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">My Orders</h1>
        <p class="text-muted">Track and manage your purchases</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Items</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php 
                                    $status_class = 'bg-secondary';
                                    if ($order['payment_status'] === 'paid') $status_class = 'bg-success';
                                    elseif ($order['payment_status'] === 'pending') $status_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                                </td>
                                <td class="fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <!-- Optional: Fetch item count or names here if needed -->
                                    <small class="text-muted">View Details</small>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($order['payment_status'] === 'paid'): ?>
                                    <a href="../generate-invoice.php?id=<?php echo $order['id']; ?>&download=true" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-file-invoice me-1"></i> Invoice
                                    </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Invoice Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-shopping-bag fa-3x mb-3 text-light"></i>
                                <p>You haven't placed any orders yet.</p>
                                <a href="../products.php" class="btn btn-primary btn-sm mt-2">Browse Products</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
