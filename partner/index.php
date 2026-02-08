<?php
$page = 'dashboard';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();
$partner_id = $_SESSION['partner_id'];

// Fetch Partner Details
$stmt = $db->prepare("SELECT * FROM partners WHERE id = :id");
$stmt->bindParam(':id', $partner_id);
$stmt->execute();
$partner = $stmt->fetch(PDO::FETCH_ASSOC);

$current_hour = date('H');
if ($current_hour < 12) {
    $greeting = "Good Morning";
} elseif ($current_hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// 1. Calculate Total Earnings Dynamically from Earnings Table
$e_stmt = $db->prepare("SELECT SUM(amount) as real_total FROM partner_earnings WHERE partner_id = :pid");
$e_stmt->execute([':pid' => $partner_id]);
$earning_data = $e_stmt->fetch(PDO::FETCH_ASSOC);
$total_earnings = $earning_data['real_total'] ?? 0;

// 2. Fetch Recent Activity (Latest 10 Earnings)
$recent_sql = "SELECT pe.*
               FROM partner_earnings pe 
               WHERE pe.partner_id = :pid 
               ORDER BY pe.created_at DESC 
               LIMIT 10";
$r_stmt = $db->prepare($recent_sql);
$r_stmt->execute([':pid' => $partner_id]);
$recent_activity = $r_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="header-title">
    <div class="header-title">
        <h1 class="page-title"><?php echo $greeting; ?>, <?php echo htmlspecialchars($partner['name']); ?>!</h1>
        <p class="text-muted">Here's an overview of your partner account.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Referral Code Widget -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-2">Your Referral Code</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 text-primary fw-bold"><?php echo htmlspecialchars($partner['referral_code']); ?></h2>
                    <div class="widget-icon bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="fas fa-tag fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-primary-subtle text-primary">Share this code</span>
                    <small class="text-muted ms-2">to earn commissions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Widget -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-2">Commission Rate</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold"><?php echo htmlspecialchars($partner['commission']); ?>%</h2>
                    <div class="widget-icon bg-success-subtle text-success rounded-circle p-3">
                        <i class="fas fa-percentage fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Applied on every successful referral</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Widget (Placeholder) -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-2">Total Earnings</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">₹<?php echo number_format($total_earnings, 2); ?></h2>
                    <div class="widget-icon bg-warning-subtle text-warning rounded-circle p-3">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $url_prefix; ?>my-earnings.php" class="text-decoration-none small">View Payout History <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 ps-4">Date</th>
                                <th class="border-top-0">Order ID</th>
                                <th class="border-top-0">Description</th>
                                <th class="border-top-0 text-end pe-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_activity) > 0): ?>
                                <?php foreach ($recent_activity as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-medium"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></div>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">#<?php echo htmlspecialchars($row['order_id']); ?></span>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?php echo htmlspecialchars($row['description']); ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold text-success">+ ₹<?php echo number_format($row['amount'], 2); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-2x mb-3 opacity-50"></i>
                                            <p class="mb-0">No recent activity found.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 text-center py-3">
                <a href="<?php echo $url_prefix; ?>my-earnings.php" class="text-primary text-decoration-none fw-medium">View Full History <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
