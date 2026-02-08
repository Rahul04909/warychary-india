<?php
$page = 'profile';
include 'includes/header.php';

// Database Connection
if (!isset($db)) {
    include_once __DIR__ . '/../database/db_config.php';
    $database = new Database();
    $db = $database->getConnection();
}

$user_id = $_SESSION['user_id'];
$message = $messageType = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    
    // Image Upload
    $image_path = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/uploads/users/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "assets/uploads/users/" . $file_name;
            // Update session image
            $_SESSION['user_image'] = $image_path;
        }
    }

    $update_sql = "UPDATE users SET name=:name, mobile=:mobile, gender=:gender, address=:address, city=:city, state=:state, pincode=:pincode, image=:image WHERE id=:id";
    $stmt = $db->prepare($update_sql);
    $stmt->execute([
        ':name' => $name,
        ':mobile' => $mobile,
        ':gender' => $gender,
        ':address' => $address,
        ':city' => $city,
        ':state' => $state,
        ':pincode' => $pincode,
        ':image' => $image_path,
        ':id' => $user_id
    ]);

    if ($stmt->rowCount() >= 0) { // >= 0 because if no changes, rowCount is 0 but success
        $message = "Profile updated successfully!";
        $messageType = "success";
        $_SESSION['user_name'] = $name; // Update session name
    } else {
        $message = "Failed to update profile.";
        $messageType = "danger";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
        $messageType = "danger";
    } else {
        // Verify Old Password
        $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $upd_stmt = $db->prepare("UPDATE users SET password = :pass WHERE id = :id");
            if ($upd_stmt->execute([':pass' => $new_hash, ':id' => $user_id])) {
                $message = "Password changed successfully!";
                $messageType = "success";
            } else {
                $message = "Failed to change password.";
                $messageType = "danger";
            }
        } else {
            $message = "Incorrect current password.";
            $messageType = "danger";
        }
    }
}

// Fetch Latest User Data
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12 mb-4">
        <h1 class="page-title">My Profile</h1>
        <p class="text-muted">Manage your account settings</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Profile Edit Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 card-title"><i class="fas fa-user-edit text-primary me-2"></i>Edit Details</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($user['image']); ?>">

                    <div class="row mb-4 align-items-center">
                        <div class="col-auto">
                            <?php 
                            $img_src = !empty($user['image']) ? "../" . $user['image'] : "../assets/images/avatar-placeholder.png"; 
                            // Fallback if file doesn't exist check could be nice but simple logic for now
                            if (empty($user['image'])) $img_src = "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&background=random";
                            else $img_src = "../" . $user['image'];
                            ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f1f5f9;">
                        </div>
                        <div class="col">
                            <label for="image" class="form-label fw-bold">Change Profile Photo</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <div class="form-text">Allowed JPG, GIF or PNG. Max size of 800K</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly title="Email cannot be changed">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Full Address</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($user['city']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($user['state']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($user['pincode']); ?>">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 card-title"><i class="fas fa-lock text-primary me-2"></i>Change Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    
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

                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Referral Info Widget -->
        <?php if (!empty($user['partner_id'])): 
            $p_stmt = $db->prepare("SELECT name, referral_code, mobile, email FROM partners WHERE id = :pid");
            $p_stmt->execute([':pid' => $user['partner_id']]);
            $partner = $p_stmt->fetch(PDO::FETCH_ASSOC);
            if ($partner):
        ?>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 text-muted uppercase">Your Partner Support</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3">
                        <i class="fas fa-headset fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-1"><?php echo htmlspecialchars($partner['name']); ?></h6>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($partner['referral_code']); ?></span>
                    </div>
                </div>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="fas fa-phone me-2 text-muted"></i> <?php echo htmlspecialchars($partner['mobile']); ?></li>
                    <li><i class="fas fa-envelope me-2 text-muted"></i> <?php echo htmlspecialchars($partner['email']); ?></li>
                </ul>
            </div>
        </div>
        <?php endif; endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
