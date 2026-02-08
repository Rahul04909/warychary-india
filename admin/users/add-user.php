<?php
$page = 'users';
$url_prefix = '../../';
require_once '../includes/header.php';
require_once '../../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $status = $_POST['status'];
    $partner_id = !empty($_POST['partner_id']) ? $_POST['partner_id'] : null;
    $gender = $_POST['gender']; // Added gender as it's required in users table schema
    
    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $message = "<div class='alert alert-danger'>Email already exists!</div>";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (name, email, mobile, password, status, partner_id, gender, state, city, pincode, address) VALUES (:name, :email, :mobile, :password, :status, :partner_id, :gender, '', '', '', '')";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mobile', $mobile);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':gender', $gender);
        
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>User added successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add user.</div>";
        }
    }
}

// Fetch Partners for Dropdown
$partners_query = "SELECT id, name FROM partners ORDER BY name ASC";
$partners_stmt = $db->prepare($partners_query);
$partners_stmt->execute();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add User</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php echo $message; ?>
        
        <form action="add-user.php" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Assigned Partner</label>
                    <select name="partner_id" class="form-control">
                        <option value="">-- No Partner (Direct) --</option>
                        <?php while ($partner = $partners_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $partner['id']; ?>">
                                <?php echo htmlspecialchars($partner['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
