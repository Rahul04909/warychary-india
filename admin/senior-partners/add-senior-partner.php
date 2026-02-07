<?php
include_once __DIR__ . '/../../database/db_config.php';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Generate Referral Code Function
function generateReferralCode($db) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    
    do {
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Check uniqueness
        $stmt = $db->prepare("SELECT id FROM senior_partners WHERE referral_code = :code");
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
    } while ($stmt->rowCount() > 0);
    
    return $code;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $commission = 2.00; // Default commission
    
    // Referral Code Generation
    $referral_code = generateReferralCode($db);
    
    // Image Upload
    $image_path = "";
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
            // Check if email already exists
            $check_email = $db->prepare("SELECT id FROM senior_partners WHERE email = :email");
            $check_email->bindParam(':email', $email);
            $check_email->execute();
            
            if ($check_email->rowCount() > 0) {
                $message = "Email already exists!";
                $messageType = "danger";
            } else {
                // Insert Senior Partner
                $sql = "INSERT INTO senior_partners (name, email, image, gender, state, city, pincode, address, password, referral_code, commission) 
                        VALUES (:name, :email, :image, :gender, :state, :city, :pincode, :address, :password, :referral_code, :commission)";
                
                $stmt = $db->prepare($sql);
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':image', $image_path);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':state', $state);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':pincode', $pincode);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':password', $password_hash);
                $stmt->bindParam(':referral_code', $referral_code);
                $stmt->bindParam(':commission', $commission);
                
                if ($stmt->execute()) {
                    $message = "Senior Partner added successfully! Referral Code: <strong>$referral_code</strong>";
                    $messageType = "success";
                } else {
                    $message = "Failed to add Senior Partner.";
                    $messageType = "danger";
                }
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
        <h1>Add Senior Partner</h1>
        <p>Create a new senior partner account</p>
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Personal Info -->
                 <div class="col-md-12 mb-4">
                    <h5 class="section-title">Personal Information</h5>
                    <hr>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required placeholder="Enter email address">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required placeholder="Create password">
                </div>
                
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Allowed types: JPG, PNG. Max size: 2MB</small>
                </div>
                
                <!-- Address Info -->
                 <div class="col-md-12 mb-4 mt-3">
                    <h5 class="section-title">Address Information</h5>
                    <hr>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control" required placeholder="Enter state">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" required placeholder="Enter city">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" class="form-control" required placeholder="Enter pincode" pattern="[0-9]{6}" title="6 digit pincode">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Full Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="3" required placeholder="Enter full address"></textarea>
                </div>
                
                <!-- System Info -->
                 <div class="col-md-12 mb-4 mt-3">
                    <h5 class="section-title">System Configuration</h5>
                    <hr>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Commission (%)</label>
                    <input type="text" class="form-control" value="2%" readonly disabled>
                    <small class="text-muted">Default commission for Senior Partners is 2%</small>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Senior Partner
                    </button>         
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
