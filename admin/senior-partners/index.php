<?php
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Delete Action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM senior_partners WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    
    if ($stmt->execute()) {
        $msg = "Senior Partner deleted successfully.";
        $msgClass = "success";
    } else {
        $msg = "Failed to delete Senior Partner.";
        $msgClass = "danger";
    }
}
?>

<div class="page-header">
    <div class="header-title">
        <h1>Senior Partners</h1>
        <p>Manage all senior partners</p>
    </div>
    <div class="header-actions">
        <a href="add-senior-partner.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Partner</a>
    </div>
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
                        <th>Email</th>
                        <th>Ref. Code</th>
                        <th>Commission</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM senior_partners ORDER BY id DESC";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $image_src = !empty($row['image']) ? '../../' . $row['image'] : '../../assets/images/default-avatar.png';
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td><img src='{$image_src}' class='rounded-circle' width='40' height='40' onerror=\"this.src='https://via.placeholder.com/40'\"></td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td><span class='badge bg-info text-dark'>{$row['referral_code']}</span></td>
                                <td>{$row['commission']}%</td>
                                <td><span class='badge bg-" . ($row['status'] == 'active' ? 'success' : 'danger') . "'>{$row['status']}</span></td>
                                <td>
                                    <a href='edit-senior-partner.php?id={$row['id']}' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a>
                                    <a href='index.php?delete_id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this partner?\")'><i class='fas fa-trash'></i></a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No senior partners found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
