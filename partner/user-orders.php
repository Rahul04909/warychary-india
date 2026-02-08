<?php
$page = 'user-orders';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';

// Database Connection
if (!isset($db)) {
    include_once __DIR__ . '/../database/db_config.php';
    $database = new Database();
    $db = $database->getConnection();
}

$partner_id = $_SESSION['partner_id'];

// Fetch Orders referred by this partner
// stored in 'orders' table with 'partner_id' column
$query = "SELECT o.*, u.name as user_name, u.email as user_email, u.mobile as user_mobile 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.partner_id = :pid 
          ORDER BY o.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':pid', $partner_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Total Sales & Commission from these orders
$total_sales = 0;
$total_commission = 0; // This would ideally come from partner_earnings table, but for now we can sum up
?>

<div class="row">
    <div class="col-12 mb-4">
        <h1 class="page-title">User Orders</h1>
        <p class="text-muted">Track orders placed by your referred users</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>User Details</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th class="text-end pe-4">Commission Status</th>
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
                                        <small class="text-muted"><?php echo htmlspecialchars($order['user_mobile']); ?></small>
                                    </div>
                                </td>
                                <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php 
                                    $status_class = 'bg-secondary';
                                    if ($order['payment_status'] === 'paid') $status_class = 'bg-success';
                                    elseif ($order['payment_status'] === 'pending') $status_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                                </td>
                                <td class="fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="text-end pe-4">
                                    <!-- Check Commission Logic specific to order/partner_earnings if needed -->
                                    <?php if ($order['payment_status'] === 'paid'): ?>
                                        <span class="badge bg-success-subtle text-success"><i class="fas fa-check-circle me-1"></i> Earned</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-shopping-basket fa-3x mb-3 text-light"></i>
                                <p>No orders found from your referrals yet.</p>
                                <p class="small">Share your referral code to get started!</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
