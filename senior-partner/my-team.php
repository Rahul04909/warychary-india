<?php
$page = 'team';
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

// Count Total Team Members
$count_stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE senior_partner_id = :id");
$count_stmt->bindParam(':id', $partner_id);
$count_stmt->execute();
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Fetch Team Members with Pagination
$stmt = $db->prepare("SELECT * FROM partners WHERE senior_partner_id = :id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':id', $partner_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">My Team</h1>
        <p class="text-muted">Manage and view your referred partners.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-top-0">Partner Name</th>
                        <th class="border-top-0">Contact Details</th>
                        <th class="border-top-0">Status</th>
                        <th class="border-top-0">Joined Date</th>
                        <th class="border-top-0 text-end">Total Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($team_members) > 0): ?>
                        <?php foreach ($team_members as $member): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                        $img_src = !empty($member['image']) ? "../" . htmlspecialchars($member['image']) : "https://via.placeholder.com/40";
                                        ?>
                                        <img src="<?php echo $img_src; ?>" alt="" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($member['name']); ?></div>
                                            <small class="text-muted">Code: <?php echo htmlspecialchars($member['referral_code']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope text-muted me-2"></i> <?php echo htmlspecialchars($member['email']); ?></div>
                                    <div class="mt-1"><i class="fas fa-phone text-muted me-2"></i> <?php echo htmlspecialchars($member['mobile']); ?></div>
                                </td>
                                <td>
                                    <?php if ($member['status'] == 'active'): ?>
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary"><?php echo ucfirst($member['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M, Y', strtotime($member['created_at'])); ?></td>
                                <td class="text-end fw-bold">₹<?php echo number_format($member['total_earnings'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                                <p>You haven't referred any partners yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous -->
                    <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                    </li>
                    
                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Next -->
                    <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
