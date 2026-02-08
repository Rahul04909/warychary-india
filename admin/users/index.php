<?php
$page = 'users';
$url_prefix = '../../';
require_once '../includes/header.php';
require_once '../../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

// Handle Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = "";
$params = [];

if (!empty($search)) {
    $where_clause = "WHERE u.name LIKE :search OR u.email LIKE :search OR u.mobile LIKE :search OR p.name LIKE :search";
    $params[':search'] = "%$search%";
}

// Pagination logic
$limit = 10;
$page_num = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page_num - 1) * $limit;

// Count total records
$count_query = "SELECT COUNT(*) FROM users u LEFT JOIN partners p ON u.partner_id = p.id $where_clause";
$stmt = $db->prepare($count_query);
$stmt->execute($params);
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch Users with Partner Name
$query = "SELECT u.*, p.name as partner_name 
          FROM users u 
          LEFT JOIN partners p ON u.partner_id = p.id 
          $where_clause 
          ORDER BY u.created_at DESC 
          LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">User Management</h1>
    <a href="add-user.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New User
    </a>
</div>

<!-- Search Box -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form method="GET" action="" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
                <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" value="<?php echo htmlspecialchars($search); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Info</th>
                        <th>Contact</th>
                        <th>Referred By</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stmt->rowCount() > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($row['image'])): ?>
                                            <img src="../../<?php echo htmlspecialchars($row['image']); ?>" alt="User" class="rounded-circle mr-2" width="30" height="30" style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2" style="width: 30px; height: 30px;">
                                                <i class="fas fa-user fa-xs"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="font-weight-bold ml-2"><?php echo htmlspecialchars($row['name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope fa-sm text-gray-400 mr-1"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                    <div><i class="fas fa-phone fa-sm text-gray-400 mr-1"></i> <?php echo htmlspecialchars($row['mobile']); ?></div>
                                </td>
                                <td>
                                    <?php if ($row['partner_name']): ?>
                                        <span class="badge badge-info"><?php echo htmlspecialchars($row['partner_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">Direct</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'active'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="edit-user.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm btn-circle" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete-user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Are you sure you want to delete this user?');" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page_num == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page_num=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
