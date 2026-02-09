<?php
$page = 'orders';
$sub_page = 'completed_orders';
$url_prefix = '../';
require_once '../auth_check.php';
include_once '../../database/db_config.php';
include_once '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Pagination Setup
$limit = 10;
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page_num - 1) * $limit;

// Fetch Completed Orders (dispatched_date IS NOT NULL)
$sql_count = "SELECT COUNT(*) FROM orders WHERE dispatched_date IS NOT NULL";
$stmt = $db->prepare($sql_count);
$stmt->execute();
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.dispatched_date IS NOT NULL 
        ORDER BY o.dispatched_date DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1 class="page-title">Completed Orders</h1>
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
                        <th>Dispatched Date</th>
                        <th>Courier Info</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="table-success"> <!-- Green Highlight Row -->
                                <td class="ps-4 fw-bold">
                                    #<?php echo htmlspecialchars($order['id']); ?>
                                    <div class="small text-muted fw-normal" style="font-size: 10px;"><?php echo htmlspecialchars($order['order_id']); ?></div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold"><?php echo htmlspecialchars($order['user_name']); ?></span>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                                    </div>
                                </td>
                                <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['dispatched_date'])); ?></td>
                                <td>
                                    <div class="small">
                                        <div><strong><?php echo htmlspecialchars($order['courier_name']); ?></strong></div>
                                        <div class="text-muted">ID: <?php echo htmlspecialchars($order['tracking_id']); ?></div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end align-items-center">
                                         <span class="badge bg-success me-2">Dispatched</span>
                                        
                                        <!-- Invoice Button -->
                                        <a href="download-invoice.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary btn-icon" title="Download Invoice" target="_blank">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>

                                        <!-- Courier Receipt -->
                                        <a href="courier-receipt.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-info btn-icon" title="Courier Receipt" target="_blank">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No completed orders found.</td>
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
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
