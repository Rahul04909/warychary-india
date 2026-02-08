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
<div class="row g-4 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-info">
                <h3>0</h3>
                <p>My Orders</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-info">
                <h3>0</h3>
                <p>Wishlist</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-12">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-info">
                <h3>0</h3>
                <p>Notifications</p>
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
