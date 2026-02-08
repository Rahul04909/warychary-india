<?php
$page = 'dashboard';
include 'includes/header.php';

// Database Connection
if (!isset($db)) {
    include_once __DIR__ . '/../database/db_config.php';
    $database = new Database();
    $db = $database->getConnection();
}

$user_id = $_SESSION['user_id'];

// Fetch Stats
// 1. Total Orders
$stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$total_orders = $stmt->fetchColumn();

// 2. Recent Orders (Limit 5)
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5");
$stmt->execute([':uid' => $user_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">My Dashboard</h1>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    </div>
    <div>
        <span class="badge bg-success px-3 py-2">Active Member</span>
    </div>
</div>

<!-- Stats Cards -->
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <!-- My Orders Widget -->
    <div class="col-md-4 col-xl-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-2">My Orders</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 text-primary fw-bold"><?php echo $total_orders; ?></h2>
                    <div class="widget-icon bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="orders.php" class="badge bg-primary-subtle text-primary text-decoration-none">View All</a>
                    <small class="text-muted ms-2">Check your order history</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wishlist Widget -->
    <div class="col-md-4 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-2">My Wishlist</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">0</h2>
                    <div class="widget-icon bg-danger-subtle text-danger rounded-circle p-3">
                        <i class="fas fa-heart fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Saved items for later</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Widget -->
    <div class="col-md-4 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-2">Notifications</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">0</h2>
                    <div class="widget-icon bg-warning-subtle text-warning rounded-circle p-3">
                        <i class="fas fa-bell fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none small">View all alerts <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 card-title"><i class="fas fa-history text-primary me-2"></i>Recent Orders</h5>
        <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_orders) > 0): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
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
                                    <a href="orders.php" class="btn btn-sm btn-light border">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                                <p>No recent orders found.</p>
                                <a href="../products.php" class="btn btn-primary btn-sm mt-2">Start Shopping</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
