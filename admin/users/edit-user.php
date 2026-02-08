<?php
$page = 'users';
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $status = $_POST['status'];
    $partner_id = !empty($_POST['partner_id']) ? $_POST['partner_id'] : null;
    
    // Optional Password Update
    $password_sql = "";
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password = :password";
    }

    $query = "UPDATE users SET name = :name, email = :email, mobile = :mobile, status = :status, partner_id = :partner_id $password_sql WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':id', $id);
    
    if (!empty($_POST['password'])) {
        $stmt->bindParam(':password', $password_hash);
    }
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>User updated successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Unable to update user.</div>";
    }
}

// Fetch User Data
$query = "SELECT * FROM users WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Partners for Dropdown
$partners_query = "SELECT id, name FROM partners ORDER BY name ASC";
$partners_stmt = $db->prepare($partners_query);
$partners_stmt->execute();

if (!$row) {
    echo "User not found.";
    include '../includes/footer.php';
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php echo $message; ?>
        
        <form action="edit-user.php?id=<?php echo $id; ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($row['mobile']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Assigned Partner</label>
                    <select name="partner_id" class="form-control">
                        <option value="">-- No Partner (Direct) --</option>
                        <?php while ($partner = $partners_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $partner['id']; ?>" <?php echo ($row['partner_id'] == $partner['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($partner['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo ($row['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($row['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>New Password <small class="text-muted">(Leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
