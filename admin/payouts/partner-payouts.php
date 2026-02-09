<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include_once '../../database/db_config.php';
$page = 'partner-payouts';

$database = new Database();
$db = $database->getConnection();

// Handle Search & Filter
$search = $_GET['search'] ?? '';

// Pagination
$limit = 15;
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pageNum - 1) * $limit;

// Query to get Partners and their calculated earnings
// We need: Partner ID, Name, Mobile, Total Earnings, Total Paid
$sql = "SELECT 
            p.id, 
            p.name, 
            p.mobile, 
            p.email,
            COALESCE(e.total_earnings, 0) as total_earnings,
            COALESCE(ph.total_paid, 0) as total_paid,
            bd.account_number,
            bd.ifsc_code,
            bd.bank_name
        FROM partners p
        LEFT JOIN (
            SELECT partner_id, SUM(amount) as total_earnings 
            FROM partner_earnings 
            GROUP BY partner_id
        ) e ON p.id = e.partner_id
        LEFT JOIN (
            SELECT user_id, SUM(amount) as total_paid 
            FROM payout_history 
            WHERE user_type = 'partner' AND status = 'processed'
            GROUP BY user_id
        ) ph ON p.id = ph.user_id
        LEFT JOIN bank_details bd ON p.id = bd.user_id AND bd.user_type = 'partner'
        WHERE 1=1";

if ($search) {
    $sql .= " AND (p.name LIKE :search OR p.mobile LIKE :search OR p.email LIKE :search)";
}

$sql .= " ORDER BY p.id DESC";

// Count for pagination (simplified for now to avoid complex count query with joins if not needed, but for accuracy we should)
// For now, let's fetch all and paginate via LIMIT for the main query
$countSql = "SELECT COUNT(*) FROM partners p WHERE 1=1";
if ($search) {
    $countSql .= " AND (p.name LIKE :search OR p.mobile LIKE :search OR p.email LIKE :search)";
}
$countStmt = $db->prepare($countSql);
if ($search) $countStmt->bindValue(':search', "%$search%");
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

$sql .= " LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
if ($search) $stmt->bindValue(':search', "%$search%");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between card flex-sm-row border-0">
            <h4 class="mb-sm-0 font-size-18 px-3">Partner Payouts</h4>
            <div class="page-title-right px-3">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Payouts</a></li>
                    <li class="breadcrumb-item active">Partner Payouts</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form class="d-flex" method="GET">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search by name, mobile, email..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <form action="export-payouts.php" method="POST" target="_blank" class="d-inline">
                            <input type="hidden" name="type" value="partner">
                            <button type="submit" class="btn btn-success me-2"><i class="fas fa-file-excel me-1"></i> Export Excel</button>
                        </form>
                        <button type="button" class="btn btn-primary" id="btn-bulk-payout" disabled>
                            <i class="fas fa-money-bill-wave me-1"></i> Pay Selected
                        </button>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table align-middle table-nowrap table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check font-size-16">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                        <label class="form-check-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th>Partner</th>
                                <th>Bank Details</th>
                                <th>Total Earnings</th>
                                <th>Total Paid</th>
                                <th>Pending Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($partners) > 0): ?>
                                <?php foreach ($partners as $p): 
                                    $pending = $p['total_earnings'] - $p['total_paid'];
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($pending > 0): ?>
                                        <div class="form-check font-size-16">
                                            <input class="form-check-input payout-checkbox" type="checkbox" name="payout_ids[]" value="<?php echo $p['id']; ?>" data-amount="<?php echo $pending; ?>">
                                            <label class="form-check-label"></label>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <h5 class="font-size-14 mb-1"><?php echo htmlspecialchars($p['name']); ?></h5>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($p['mobile']); ?></p>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['account_number'])): ?>
                                            <small class="d-block text-muted">A/C: <?php echo htmlspecialchars($p['account_number']); ?></small>
                                            <small class="d-block text-muted">IFSC: <?php echo htmlspecialchars($p['ifsc_code']); ?></small>
                                            <small class="d-block text-muted"><?php echo htmlspecialchars($p['bank_name']); ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Not Added</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>₹<?php echo number_format($p['total_earnings'], 2); ?></td>
                                    <td>₹<?php echo number_format($p['total_paid'], 2); ?></td>
                                    <td>
                                        <?php if ($pending > 0): ?>
                                            <span class="fw-bold text-danger">₹<?php echo number_format($pending, 2); ?></span>
                                        <?php else: ?>
                                            <span class="fw-bold text-success">Paid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pending > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-pay-single" 
                                                data-id="<?php echo $p['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($p['name']); ?>" 
                                                data-amount="<?php echo $pending; ?>">
                                                Pay Now
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light" disabled>Paid</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No partners found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($i == $pageNum) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Payout Modal -->
<div class="modal fade" id="payoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Payout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process-payout.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="user_type" value="partner">
                    <input type="hidden" name="user_ids" id="modal_user_ids">
                    
                    <p id="payout-summary"></p>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Amount to Pay</label>
                        <input type="text" class="form-control fw-bold" id="modal_total_amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-select">
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="UPI">UPI</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction ID / Reference (Optional)</label>
                        <input type="text" name="transaction_id" class="form-control" placeholder="e.g. UPI Ref, Cheque No">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin Note</label>
                        <textarea name="admin_note" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i> This action cannot be undone. Amount will be deducted from user's balance.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.payout-checkbox');
    const btnBulkPayout = document.getElementById('btn-bulk-payout');
    
    // Check All
    checkAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = checkAll.checked);
        updateBulkBtn();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkBtn));

    function updateBulkBtn() {
        const checked = document.querySelectorAll('.payout-checkbox:checked').length;
        btnBulkPayout.disabled = checked === 0;
        btnBulkPayout.innerHTML = `<i class="fas fa-money-bill-wave me-1"></i> Pay Selected (${checked})`;
    }

    // Modal Handling
    const payoutModal = new bootstrap.Modal(document.getElementById('payoutModal'));
    const modalUserIds = document.getElementById('modal_user_ids');
    const modalTotal = document.getElementById('modal_total_amount');
    const modalSummary = document.getElementById('payout-summary');

    // Single Payout
    document.querySelectorAll('.btn-pay-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const amount = parseFloat(this.dataset.amount);

            modalUserIds.value = id; // Single ID
            modalTotal.value = amount.toFixed(2);
            modalSummary.innerHTML = `Initiating payout for <strong>${name}</strong>`;
            
            payoutModal.show();
        });
    });

    // Bulk Payout
    btnBulkPayout.addEventListener('click', function() {
        const checked = document.querySelectorAll('.payout-checkbox:checked');
        const ids = [];
        let total = 0;

        checked.forEach(cb => {
            ids.push(cb.value);
            total += parseFloat(cb.dataset.amount);
        });

        modalUserIds.value = ids.join(','); // Comma separated IDs
        modalTotal.value = total.toFixed(2);
        modalSummary.innerHTML = `Initiating Bulk Payout for <strong>${ids.length} Partner(s)</strong>`;
        
        payoutModal.show();
    });
});
</script>
