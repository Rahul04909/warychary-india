<?php
$page = 'earnings';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();
$partner_id = $_SESSION['senior_partner_id'];

// Pagination Configuration
$records_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// 1. Fetch Key Statistics
// Total Earnings
$stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM senior_partner_earnings WHERE senior_partner_id = :id");
$stmt->execute([':id' => $partner_id]);
$total_earnings = $stmt->fetchColumn();

// Total Payout (Paid Earnings)
$stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM senior_partner_earnings WHERE senior_partner_id = :id AND status = 'paid'");
$stmt->execute([':id' => $partner_id]);
$total_payout = $stmt->fetchColumn();

// Total Orders (Commission Count)
$stmt = $db->prepare("SELECT COUNT(*) FROM senior_partner_earnings WHERE senior_partner_id = :id");
$stmt->execute([':id' => $partner_id]);
$total_orders = $stmt->fetchColumn();

// Total Partners (Team Size)
$stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE senior_partner_id = :id");
$stmt->execute([':id' => $partner_id]);
$total_partners = $stmt->fetchColumn();


// 2. Fetch Earnings History
$count_stmt = $db->prepare("SELECT COUNT(*) FROM senior_partner_earnings WHERE senior_partner_id = :id");
$count_stmt->execute([':id' => $partner_id]);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

$sql = "SELECT spe.*, p.name as partner_name 
        FROM senior_partner_earnings spe 
        LEFT JOIN partners p ON spe.source_partner_id = p.id 
        WHERE spe.senior_partner_id = :id 
        ORDER BY spe.created_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $partner_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$earnings_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">My Earnings</h1>
        <p class="text-muted">Track your commissions and payout history.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 opacity-75">Total Earnings</h6>
                        <h3 class="mb-0 fw-bold">₹<?php echo number_format($total_earnings, 2); ?></h3>
                    </div>
                    <div class="fs-1 opacity-25"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 opacity-75">Total Payout</h6>
                        <h3 class="mb-0 fw-bold">₹<?php echo number_format($total_payout, 2); ?></h3>
                    </div>
                    <div class="fs-1 opacity-25"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 opacity-75">Total Orders</h6>
                        <h3 class="mb-0 fw-bold"><?php echo number_format($total_orders); ?></h3>
                    </div>
                    <div class="fs-1 opacity-25"><i class="fas fa-shopping-cart"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 opacity-75">Total Partners</h6>
                        <h3 class="mb-0 fw-bold"><?php echo number_format($total_partners); ?></h3>
                    </div>
                    <div class="fs-1 opacity-25"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Earnings History Table -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">Earnings History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Order ID</th>
                        <th>Source Partner</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($earnings_history) > 0): ?>
                        <?php foreach ($earnings_history as $row): ?>
                            <tr>
                                <td class="ps-4"><?php echo date('d M, Y h:i A', strtotime($row['created_at'])); ?></td>
                                <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td>
                                    <?php if ($row['partner_name']): ?>
                                        <div class="fw-medium"><?php echo htmlspecialchars($row['partner_name']); ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'paid'): ?>
                                        <span class="badge bg-success-subtle text-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    + ₹<?php echo number_format($row['amount'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-coins fa-3x mb-3 text-secondary opacity-50"></i>
                                <p>No earnings records found yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="p-3 border-top">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
