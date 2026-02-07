<?php
$page = 'profile';
$url_prefix = '';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();
$partner_id = $_SESSION['senior_partner_id'];

$message = "";
$messageType = "";

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'] ?? '';
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    
    // Image Upload Logic
    $image_path = $_POST['existing_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "../uploads/partners/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "partner_" . $partner_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/partners/" . $new_filename;
        } else {
            $message = "Error uploading image.";
            $messageType = "danger";
        }
    }

    if (empty($message)) {
        try {
            $sql = "UPDATE senior_partners SET name=:name, phone=:phone, gender=:gender, address=:address, state=:state, city=:city, pincode=:pincode, image=:image WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':pincode', $pincode);
            $stmt->bindParam(':image', $image_path);
            $stmt->bindParam(':id', $partner_id);
            
            if ($stmt->execute()) {
                $_SESSION['senior_partner_name'] = $name; // Update session name
                $message = "Profile updated successfully!";
                $messageType = "success";
            } else {
                $message = "Failed to update profile.";
                $messageType = "danger";
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $stmt = $db->prepare("SELECT password FROM senior_partners WHERE id = :id");
    $stmt->bindParam(':id', $partner_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $db->prepare("UPDATE senior_partners SET password = :password WHERE id = :id");
            $update_stmt->bindParam(':password', $hashed_password);
            $update_stmt->bindParam(':id', $partner_id);
            
            if ($update_stmt->execute()) {
                $message = "Password changed successfully!";
                $messageType = "success";
            } else {
                $message = "Failed to change password.";
                $messageType = "danger";
            }
        } else {
            $message = "New passwords do not match.";
            $messageType = "danger";
        }
    } else {
        $message = "Incorrect current password.";
        $messageType = "danger";
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT * FROM senior_partners WHERE id = :id");
$stmt->bindParam(':id', $partner_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">My Profile</h1>
        <p class="text-muted">Manage your account settings and password.</p>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Profile Details -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row mb-4 align-items-center">
                        <div class="col-auto">
                            <?php 
                            $img_src = !empty($user['image']) ? "../" . htmlspecialchars($user['image']) : "https://via.placeholder.com/100";
                            ?>
                            <img src="<?php echo $img_src; ?>" alt="Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_image" class="form-control form-control-sm">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($user['image']); ?>">
                            <small class="text-muted">Allowed types: JPG, PNG. Max size: 2MB</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>
                            <small class="text-muted">Contact admin to change email</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Address Details</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($user['state']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($user['pincode']); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Full Address</label>
                            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Security</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning w-100 text-white">
                        <i class="fas fa-key me-1"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
