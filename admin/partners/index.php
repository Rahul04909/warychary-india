<?php
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Delete Action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $db->prepare("DELETE FROM partners WHERE id = :id");
        $stmt->bindParam(':id', $delete_id);
        
        if ($stmt->execute()) {
            $msg = "Partner deleted successfully.";
            $msgClass = "success";
        } else {
            $msg = "Failed to delete Partner.";
            $msgClass = "danger";
        }
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
        $msgClass = "danger";
    }
}
?>

<div class="page-header">
    <div class="header-title">
        <h1>Partners</h1>
        <p>Manage all registered partners</p>
    </div>
    <!-- Add New Partner Button (Optional, can link to front-end or admin generic add) -->
    <!-- <div class="header-actions">
        <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Partner</a>
    </div> -->
</div>

<?php if (isset($msg)): ?>
    <div class="alert alert-<?php echo $msgClass; ?>">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Contact Info</th>
                        <th>Ref. Code</th>
                        <th>Referred By</th>
                        <th>Commission</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Join with senior_partners to get referrer name
                    $query = "SELECT p.*, sp.name as senior_partner_name, sp.referral_code as sp_code 
                              FROM partners p 
                              LEFT JOIN senior_partners sp ON p.senior_partner_id = sp.id 
                              ORDER BY p.id DESC";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Image
                            $image_src = !empty($row['image']) ? '../../' . $row['image'] : '../../assets/images/default-avatar.png';
                            
                            // Referrer Info
                            $referred_by = '<span class="badge bg-secondary">Direct</span>';
                            if (!empty($row['senior_partner_name'])) {
                                $referred_by = '<span class="badge bg-primary" title="Code: '.$row['sp_code'].'">' . htmlspecialchars($row['senior_partner_name']) . '</span>';
                            }
                            
                            // Status Badge
                            $status_badge = '<span class="badge bg-' . ($row['status'] == 'active' ? 'success' : 'danger') . '">' . ucfirst($row['status']) . '</span>';
                            
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td><img src='{$image_src}' class='rounded-circle' width='40' height='40' onerror=\"this.src='https://via.placeholder.com/40'\"></td>
                                <td>
                                    <strong>" . htmlspecialchars($row['name']) . "</strong><br>
                                    <small class='text-muted'>" . ucfirst($row['gender']) . "</small>
                                </td>
                                <td>
                                    <i class='fas fa-envelope text-muted me-1'></i> {$row['email']}<br>
                                    <i class='fas fa-phone text-muted me-1'></i> {$row['mobile']}
                                </td>
                                <td><span class='badge bg-info text-dark'>{$row['referral_code']}</span></td>
                                <td>{$referred_by}</td>
                                <td>{$row['commission']}%</td>
                                <td>{$status_badge}</td>
                                <td>
                                    <!-- Edit Link (Placeholder) -->
                                    <div class='d-flex gap-2'>
                                        <a href='#' class='btn btn-sm btn-warning' title='Edit'><i class='fas fa-edit'></i></a>
                                        <a href='index.php?delete_id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this partner?\")' title='Delete'><i class='fas fa-trash'></i></a>
                                    </div>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No partners found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
