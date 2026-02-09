<?php
$page = 'orders';
$sub_page = 'all_orders';
$url_prefix = '../';
require_once '../auth_check.php';
include_once '../../database/db_config.php';
include_once '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Filter & Pagination Setup
$limit = 10;
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page_num - 1) * $limit;
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build Query
$where_clauses = ["1=1"];
$params = [];

if ($filter_status == 'captured') {
    $where_clauses[] = "payment_status = 'captured' AND dispatched_date IS NULL";
} elseif ($filter_status == 'completed') {
    $where_clauses[] = "dispatched_date IS NOT NULL";
} elseif ($filter_status == 'pending_payment') {
    $where_clauses[] = "payment_status != 'captured'";
}

$where_sql = implode(' AND ', $where_clauses);

// Count Query
$sql_count = "SELECT COUNT(*) FROM orders WHERE $where_sql";
$stmt = $db->prepare($sql_count);
$stmt->execute();
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Main Query
$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE $where_sql 
        ORDER BY o.created_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">All Orders</h1>
    
    <!-- Filter Dropdown -->
    <form method="GET" class="d-flex align-items-center gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="captured" <?php echo ($filter_status == 'captured') ? 'selected' : ''; ?>>Pending Dispatch</option>
            <option value="completed" <?php echo ($filter_status == 'completed') ? 'selected' : ''; ?>>Dispatched</option>
            <option value="pending_payment" <?php echo ($filter_status == 'pending_payment') ? 'selected' : ''; ?>>Payment Pending/Failed</option>
        </select>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Payment</th>
                        <th>Dispatch Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold"><?php echo htmlspecialchars($order['user_name']); ?></span>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                                    </div>
                                </td>
                                <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                
                                <!-- Payment Status -->
                                <td>
                                    <?php if ($order['payment_status'] == 'captured'): ?>
                                        <span class="badge bg-success-subtle text-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning"><?php echo ucfirst($order['payment_status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Dispatch Status -->
                                <td>
                                    <?php if (!empty($order['dispatched_date'])): ?>
                                        <span class="badge bg-success">Dispatched</span>
                                    <?php elseif ($order['payment_status'] == 'captured'): ?>
                                        <span class="badge bg-danger-subtle text-danger">Pending</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-end pe-4">
                                    <!-- Only show dispatch button if paid and not yet dispatched -->
                                    <?php if ($order['payment_status'] == 'captured' && empty($order['dispatched_date'])): ?>
                                        <a href="pending-orders.php" class="btn btn-sm btn-outline-primary">Dispatch</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="card-footer bg-white border-top-0 py-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page_num) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $filter_status; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
