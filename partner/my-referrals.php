<?php
$page = 'my-referrals';
$url_prefix = '../';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

// Auth Check
if (!isset($_SESSION['partner_id'])) {
    header("Location: login.php");
    exit;
}

$partner_id = $_SESSION['partner_id'];
$database = new Database();
$db = $database->getConnection();

// Pagination Setup
$limit = 10;
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page_num - 1) * $limit;

// Count Total Referrals
$count_sql = "SELECT COUNT(*) FROM users WHERE partner_id = :pid";
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute([':pid' => $partner_id]);
$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch Users
$sql = "SELECT id, name, email, mobile, state, city, created_at, status 
        FROM users 
        WHERE partner_id = :pid 
        ORDER BY id DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindParam(':pid', $partner_id, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">My Referrals</h1>
        <p class="text-muted">View and manage users referred by you.</p>
    </div>
    <div class="header-action">
        <a href="add-user.php" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i> Add New User
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 ps-4">User Details</th>
                                <th class="border-top-0">Contact Info</th>
                                <th class="border-top-0">Location</th>
                                <th class="border-top-0">Joined Date</th>
                                <th class="border-top-0 text-end pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3 bg-primary-subtle text-primary">
                                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-medium text-dark"><?php echo htmlspecialchars($user['name']); ?></div>
                                                    <small class="text-muted">ID: #<?php echo $user['id']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span><i class="far fa-envelope text-muted me-2"></i><?php echo htmlspecialchars($user['email']); ?></span>
                                                <span class="mt-1"><i class="fas fa-phone-alt text-muted me-2"></i><?php echo htmlspecialchars($user['mobile']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($user['city']); ?>,</div>
                                            <small class="text-muted"><?php echo htmlspecialchars($user['state']); ?></small>
                                        </td>
                                        <td>
                                            <div><?php echo date('d M, Y', strtotime($user['created_at'])); ?></div>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime($user['created_at'])); ?></small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php 
                                            $status = $user['status'] ?? 'active'; // Default to active if column missing or null
                                            $badgeClass = ($status == 'active') ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> text-uppercase"><?php echo htmlspecialchars($status); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No referrals found yet.</p>
                                        </div>
                                    </td>
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
                        <li class="page-item <?php echo ($page_num <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page_num - 1; ?>">Previous</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page_num == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($page_num >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page_num + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}
</style>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
