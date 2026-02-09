<?php
$page = 'dashboard';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

// --- 1. Fetch Statistics ---

// Total Sales
$stmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'captured'");
$stmt->execute();
$total_sales = $stmt->fetchColumn();

// Total Orders
$stmt = $db->prepare("SELECT COUNT(*) FROM orders");
$stmt->execute();
$total_orders = $stmt->fetchColumn();

// Monthly Sales (Current Month)
$stmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'captured' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stmt->execute();
$monthly_sales = $stmt->fetchColumn();

// Monthly Orders
$stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stmt->execute();
$monthly_orders = $stmt->fetchColumn();

// --- New Order Stats ---

// Today's Orders
$stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$today_orders = $stmt->fetchColumn();

// Pending Orders (Captured but not dispatched)
$stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE payment_status = 'captured' AND dispatched_date IS NULL");
$stmt->execute();
$pending_orders = $stmt->fetchColumn();

// Completed/Dispatched Orders
$stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE dispatched_date IS NOT NULL");
$stmt->execute();
$completed_orders = $stmt->fetchColumn();

// Users Count
$stmt = $db->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$total_users = $stmt->fetchColumn();

// Partners Count
$stmt = $db->prepare("SELECT COUNT(*) FROM partners");
$stmt->execute();
$total_partners = $stmt->fetchColumn();

// Senior Partners Count
$stmt = $db->prepare("SELECT COUNT(*) FROM senior_partners");
$stmt->execute();
$total_senior_partners = $stmt->fetchColumn();

?>

<div class="page-header mb-4">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="text-muted">Overview of your store performance.</p>
    </div>
    <div>
        <a href="<?php echo $url_prefix; ?>products/add-product.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Product
        </a>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="row g-3 mb-4">
    <!-- Total Sales -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="text-muted small text-uppercase fw-bold">Total Sales</div>
                    <div class="icon-shape bg-success-subtle text-success rounded-circle p-2">
                        <i class="fas fa-indian-rupee-sign"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">₹<?php echo number_format($total_sales, 2); ?></h3>
                <small class="text-muted">Lifetime revenue</small>
            </div>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="text-muted small text-uppercase fw-bold">Total Orders</div>
                    <div class="icon-shape bg-primary-subtle text-primary rounded-circle p-2">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold"><?php echo number_format($total_orders); ?></h3>
                <small class="text-muted">All time orders</small>
            </div>
        </div>
    </div>

    <!-- Monthly Sales -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="text-muted small text-uppercase fw-bold">Monthly Sales</div>
                    <div class="icon-shape bg-info-subtle text-info rounded-circle p-2">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">₹<?php echo number_format($monthly_sales, 2); ?></h3>
                <small class="text-muted">This month</small>
            </div>
        </div>
    </div>

    <!-- Monthly Orders -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="text-muted small text-uppercase fw-bold">Monthly Orders</div>
                    <div class="icon-shape bg-warning-subtle text-warning rounded-circle p-2">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold"><?php echo number_format($monthly_orders); ?></h3>
                <small class="text-muted">This month</small>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Metrics -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-lg bg-light rounded-circle p-3 text-center">
                        <i class="fas fa-calendar-day fa-2x text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 text-muted">Today's Orders</h5>
                    <h2 class="mb-0 fw-bold"><?php echo number_format($today_orders); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-lg bg-danger-subtle rounded-circle p-3 text-center">
                        <i class="fas fa-clock fa-2x text-danger"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 text-muted">Pending Dispatch</h5>
                    <h2 class="mb-0 fw-bold text-danger"><?php echo number_format($pending_orders); ?></h2>
                    <a href="<?php echo $url_prefix; ?>orders/pending-orders.php" class="text-decoration-none small">View Pending &rarr;</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-lg bg-success-subtle rounded-circle p-3 text-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 text-muted">Completed Orders</h5>
                    <h2 class="mb-0 fw-bold text-success"><?php echo number_format($completed_orders); ?></h2>
                    <a href="<?php echo $url_prefix; ?>orders/completed-orders.php" class="text-decoration-none small">View History &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-lg bg-light rounded-circle p-3 text-center">
                        <i class="fas fa-user-shield fa-2x text-dark"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 text-muted">Senior Partners</h5>
                    <h2 class="mb-0 fw-bold"><?php echo number_format($total_senior_partners); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-lg bg-light rounded-circle p-3 text-center">
                        <i class="fas fa-handshake fa-2x text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 text-muted">Partners</h5>
                    <h2 class="mb-0 fw-bold"><?php echo number_format($total_partners); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-lg bg-light rounded-circle p-3 text-center">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 text-muted">Total Users</h5>
                    <h2 class="mb-0 fw-bold"><?php echo number_format($total_users); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="row g-3">
    <!-- Quick Actions -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="<?php echo $url_prefix; ?>products/add-product.php" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2 h-100 justify-content-center">
                            <i class="fas fa-plus-circle fa-2x"></i>
                            <span>Add Product</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo $url_prefix; ?>products/index.php" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-2 h-100 justify-content-center">
                            <i class="fas fa-boxes-stacked fa-2x"></i>
                            <span>Manage Stock</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo $url_prefix; ?>users/index.php" class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center gap-2 h-100 justify-content-center">
                            <i class="fas fa-users-cog fa-2x"></i>
                            <span>Users</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo $url_prefix; ?>partners/index.php" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2 h-100 justify-content-center">
                            <i class="fas fa-handshake fa-2x"></i>
                            <span>Partners</span>
                        </a>
                    </div>
                     <div class="col-6">
                        <a href="<?php echo $url_prefix; ?>senior-partners/index.php" class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center gap-2 h-100 justify-content-center">
                            <i class="fas fa-user-tie fa-2x"></i>
                            <span>Sr. Partners</span>
                        </a>
                    </div>
                     <div class="col-6">
                        <a href="<?php echo $url_prefix; ?>settings/razorpay-settings.php" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 h-100 justify-content-center">
                            <i class="fas fa-cog fa-2x"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Actions (Placeholder/Static for now, could be dynamic logs) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
             <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">System Activity</h5>
                <small class="text-muted">Last 5 events</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Time</th>
                                <th>Event</th>
                                <th class="text-end pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- This could be fetched from a system_logs table in future -->
                            <tr>
                                <td class="ps-4 text-muted small">Just now</td>
                                <td>Admin Dashboard Accessed</td>
                                <td class="text-end pe-4"><span class="badge bg-success-subtle text-success">Active</span></td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted small">1 hour ago</td>
                                <td>System Backup Completed</td>
                                <td class="text-end pe-4"><span class="badge bg-primary-subtle text-primary">Done</span></td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted small">Yesterday</td>
                                <td>New Order #1024 Received</td>
                                <td class="text-end pe-4"><span class="badge bg-info-subtle text-info">Order</span></td>
                            </tr>
                             <tr>
                                <td class="ps-4 text-muted small">Yesterday</td>
                                <td>Partner Registration (Rahul)</td>
                                <td class="text-end pe-4"><span class="badge bg-warning-subtle text-warning">New User</span></td>
                            </tr>
                             <tr>
                                <td class="ps-4 text-muted small">2 days ago</td>
                                <td>Weekly Report Generated</td>
                                <td class="text-end pe-4"><span class="badge bg-secondary-subtle text-secondary">System</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Specific Tweaks for Bootstrap Cards */
.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
</style>

<?php include 'includes/footer.php'; ?>
