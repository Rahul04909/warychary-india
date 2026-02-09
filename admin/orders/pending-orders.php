<?php
$page = 'orders';
$sub_page = 'pending_orders';
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

// Fetch Pending Orders (payment_status = 'captured' AND dispatch_status IS NULL or 'pending')
// Assuming we check for dispatched_date as indicator or add a status column. Let's rely on payment_status='captured' and dispatched_date IS NULL
// Fetch Pending Orders (payment_status in captured/paid/success AND dispatched_date IS NULL)
$sql_count = "SELECT COUNT(*) FROM orders WHERE payment_status IN ('captured', 'paid', 'success') AND dispatched_date IS NULL";
$stmt = $db->prepare($sql_count);
$stmt->execute();
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.payment_status IN ('captured', 'paid', 'success') AND o.dispatched_date IS NULL 
        ORDER BY o.created_at DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1 class="page-title">Pending Orders</h1>
    <!-- Filters could go here -->
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
                        <th>Status</th>
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
                                <td><span class="badge bg-warning text-dark">Pending Dispatch</span></td>
                                <td class="text-end pe-4">
                                <td class="text-end pe-4">
                                     <div class="d-flex gap-2 justify-content-end">
                                        <!-- Invoice Button (For Paid Orders) -->
                                        <a href="download-invoice.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Download Invoice" target="_blank">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>

                                        <!-- Courier Receipt (For Paid Orders) -->
                                        <a href="courier-receipt.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-info" title="Courier Receipt" target="_blank">
                                            <i class="fas fa-receipt"></i>
                                        </a>

                                        <!-- Dispatch Button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-primary dispatch-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#dispatchModal"
                                                data-order-id="<?php echo $order['id']; ?>"
                                                data-public-id="<?php echo $order['order_id']; ?>">
                                            Dispatch Order
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No pending orders found.</td>
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

<!-- Dispatch Modal -->
<div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dispatch Order <span id="modalOrderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="dispatch-order.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="inputOrderId">
                    
                    <div class="mb-3">
                        <label for="courier_name" class="form-label">Courier Name</label>
                        <input type="text" class="form-control" id="courier_name" name="courier_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tracking_id" class="form-label">Tracking ID</label>
                        <input type="text" class="form-control" id="tracking_id" name="tracking_id" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dispatched_from" class="form-label">Dispatched From</label>
                        <input type="text" class="form-control" id="dispatched_from" name="dispatched_from" placeholder="Warehouse location" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Dispatch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dispatchModal = document.getElementById('dispatchModal');
        if (dispatchModal) {
            dispatchModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                const publicId = button.getAttribute('data-public-id');
                
                document.getElementById('inputOrderId').value = orderId;
                document.getElementById('modalOrderId').textContent = '#' + publicId;
            });
        }
    });
</script>

<?php include_once '../includes/footer.php'; ?>
