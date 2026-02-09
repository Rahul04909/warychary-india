<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include_once '../../database/db_config.php';
$url_prefix = '../';
$page = 'payout-history';

$database = new Database();
$db = $database->getConnection();

// Filters
$month = $_GET['month'] ?? date('Y-m');
$user_type = $_GET['user_type'] ?? 'all';

// Pagination
$limit = 20;
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pageNum - 1) * $limit;

// Base Query
$sql = "SELECT ph.*, 
        CASE 
            WHEN ph.user_type = 'partner' THEN p.name 
            WHEN ph.user_type = 'senior_partner' THEN sp.name 
        END as user_name
        FROM payout_history ph
        LEFT JOIN partners p ON ph.user_id = p.id AND ph.user_type = 'partner'
        LEFT JOIN senior_partners sp ON ph.user_id = sp.id AND ph.user_type = 'senior_partner'
        WHERE DATE_FORMAT(ph.created_at, '%Y-%m') = :month";

if ($user_type !== 'all') {
    $sql .= " AND ph.user_type = :utype";
}

$sql .= " ORDER BY ph.created_at DESC";

// Count Query
$countSql = "SELECT COUNT(*) FROM payout_history ph WHERE DATE_FORMAT(ph.created_at, '%Y-%m') = :month";
if ($user_type !== 'all') $countSql .= " AND ph.user_type = :utype";

$countStmt = $db->prepare($countSql);
$countStmt->bindValue(':month', $month);
if ($user_type !== 'all') $countStmt->bindValue(':utype', $user_type);
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Final Query
$sql .= " LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':month', $month);
if ($user_type !== 'all') $stmt->bindValue(':utype', $user_type);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between card flex-sm-row border-0">
            <h4 class="mb-sm-0 font-size-18 px-3">Payout History</h4>
            <div class="page-title-right px-3">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Payouts</a></li>
                    <li class="breadcrumb-item active">History</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="row gy-2 gx-3 align-items-center mb-4" method="GET">
                    <div class="col-auto">
                        <label class="visually-hidden">Month</label>
                        <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>">
                    </div>
                    <div class="col-auto">
                        <label class="visually-hidden">User Type</label>
                        <select name="user_type" class="form-select">
                            <option value="all" <?php echo ($user_type == 'all') ? 'selected' : ''; ?>>All Users</option>
                            <option value="partner" <?php echo ($user_type == 'partner') ? 'selected' : ''; ?>>Partners</option>
                            <option value="senior_partner" <?php echo ($user_type == 'senior_partner') ? 'selected' : ''; ?>>Senior Partners</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Order ID</th> <!-- Assuming ID as Transaction ID for Payout if applicable, or Payout ID -->
                                <th>User</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Transaction ID</th>
                                <th>Note</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($payouts) > 0): ?>
                                <?php foreach ($payouts as $p): ?>
                                <tr>
                                    <td><?php echo date('d M, Y h:i A', strtotime($p['created_at'])); ?></td>
                                    <td>#P<?php echo $p['id']; ?></td>
                                    <td><?php echo htmlspecialchars($p['user_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($p['user_type'] == 'partner') ? 'bg-info' : 'bg-warning'; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $p['user_type'])); ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold">₹<?php echo number_format($p['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($p['payment_mode']); ?></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($p['transaction_id']); ?></small></td>
                                    <td><small><?php echo htmlspecialchars($p['admin_note']); ?></small></td>
                                    <td>
                                        <span class="badge bg-success"><?php echo ucfirst($p['status']); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No payout records found for this period.</td>
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
                                <a class="page-link" href="?page=<?php echo $i; ?>&month=<?php echo $month; ?>&user_type=<?php echo $user_type; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
