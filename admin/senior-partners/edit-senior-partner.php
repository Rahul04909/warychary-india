<?php
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Check ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Fetch Existing Data
$stmt = $db->prepare("SELECT * FROM senior_partners WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    header("Location: index.php");
    exit;
}

$partner = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $status = $_POST['status'];
    
    // Image Upload
    $image_path = $partner['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/uploads/partners/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "sp_" . time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "assets/uploads/partners/" . $new_filename;
            } else {
                $message = "Sorry, there was an error uploading your file.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
            $messageType = "danger";
        }
    }
    
    if (empty($message)) {
        try {
            // Update Query
            $sql = "UPDATE senior_partners SET 
                    name = :name, 
                    email = :email, 
                    image = :image, 
                    gender = :gender, 
                    state = :state, 
                    city = :city, 
                    pincode = :pincode, 
                    address = :address, 
                    status = :status";
            
            if (!empty($password)) {
                $sql .= ", password = :password";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':image', $image_path);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':pincode', $pincode);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $password_hash);
            }
            
            if ($stmt->execute()) {
                $message = "Senior Partner updated successfully!";
                $messageType = "success";
                // Refresh data
                $stmt = $db->prepare("SELECT * FROM senior_partners WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $partner = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $message = "Failed to update Senior Partner.";
                $messageType = "danger";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}
?>

<div class="page-header">
    <div class="header-title">
        <h1>Edit Senior Partner</h1>
        <p>Edit details for <?php echo htmlspecialchars($partner['name']); ?></p>
    </div>
    <div class="header-actions">
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Personal Info -->
                 <div class="col-md-12 mb-4">
                    <h5 class="section-title">Personal Information</h5>
                    <hr>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($partner['name']); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($partner['email']); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
                
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="Male" <?php echo ($partner['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($partner['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($partner['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Current: <?php echo !empty($partner['image']) ? basename($partner['image']) : 'None'; ?></small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo ($partner['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($partner['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <!-- Address Info -->
                 <div class="col-md-12 mb-4 mt-3">
                    <h5 class="section-title">Address Information</h5>
                    <hr>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control" required value="<?php echo htmlspecialchars($partner['state']); ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" required value="<?php echo htmlspecialchars($partner['city']); ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" class="form-control" required value="<?php echo htmlspecialchars($partner['pincode']); ?>" pattern="[0-9]{6}">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Full Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($partner['address']); ?></textarea>
                </div>
                
                <!-- System Info -->
                 <div class="col-md-12 mb-4 mt-3">
                    <h5 class="section-title">System Configuration</h5>
                    <hr>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Referral Code</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($partner['referral_code']); ?>" readonly disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Commission (%)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($partner['commission']); ?>%" readonly disabled>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Senior Partner
                    </button>         
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
