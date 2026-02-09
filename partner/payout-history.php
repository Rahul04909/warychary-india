<?php
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();
$partner_id = $_SESSION['partner_id'];

// Pagination
$limit = 10;
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pageNum - 1) * $limit;

// Fetch Payouts
$sql = "SELECT * FROM payout_history 
        WHERE user_id = :id AND user_type = 'partner'
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $partner_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count for pagination
$countStmt = $db->prepare("SELECT COUNT(*) FROM payout_history WHERE user_id = :id AND user_type = 'partner'");
$countStmt->bindValue(':id', $partner_id, PDO::PARAM_INT);
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">Payout History</h1>
        <p class="text-muted">View your received payments and transaction details.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($payouts) > 0): ?>
                        <?php foreach ($payouts as $p): ?>
                            <tr>
                                <td class="ps-4"><?php echo date('d M, Y', strtotime($p['created_at'])); ?></td>
                                <td class="fw-bold text-success">₹<?php echo number_format($p['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($p['payment_mode']); ?></td>
                                <td>
                                    <?php if (!empty($p['transaction_id'])): ?>
                                        <span class="text-muted text-uppercase"><?php echo htmlspecialchars($p['transaction_id']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = match($p['status']) {
                                        'processed' => 'bg-success',
                                        'pending' => 'bg-warning',
                                        'failed' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($p['status']); ?></span>
                                </td>
                                <td class="text-end pe-4 text-muted small">
                                    <?php echo htmlspecialchars($p['admin_note']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                                <p>No payout history found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="p-3 border-top">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?php echo ($pageNum <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $pageNum - 1; ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($pageNum == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($pageNum >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $pageNum + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
