<?php
$page = 'dashboard';
include 'includes/header.php';
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
                    <h2 class="mb-0 text-primary fw-bold">0</h2>
                    <div class="widget-icon bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-primary-subtle text-primary">View All</span>
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
        <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
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
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                            <p>No recent orders found.</p>
                            <a href="../index.php" class="btn btn-primary btn-sm mt-2">Start Shopping</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
