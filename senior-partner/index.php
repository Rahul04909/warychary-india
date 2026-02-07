<?php
$page = 'dashboard';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();
$partner_id = $_SESSION['senior_partner_id'];

// Fetch Partner Details
$stmt = $db->prepare("SELECT * FROM senior_partners WHERE id = :id");
$stmt->bindParam(':id', $partner_id);
$stmt->execute();
$partner = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($partner['name']); ?>!</h1>
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
                    <h2 class="mb-0 fw-bold">₹0.00</h2>
                    <div class="widget-icon bg-warning-subtle text-warning rounded-circle p-3">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none small">View Payout History <i class="fas fa-arrow-right ms-1"></i></a>
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
                                <th class="border-top-0">Date</th>
                                <th class="border-top-0">Description</th>
                                <th class="border-top-0">Amount</th>
                                <th class="border-top-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No recent activity found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
