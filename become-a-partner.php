<?php
ob_start();
$url_prefix = '';
include_once 'database/db_config.php';

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Handle AJAX Referral Verification
if (isset($_GET['action']) && $_GET['action'] == 'validate_referral' && isset($_GET['code'])) {
    // Prevent any implicit output
    ob_clean();
    header('Content-Type: application/json');
    
    $code = trim($_GET['code']);
    
    try {
        $query = "SELECT id, name FROM senior_partners WHERE referral_code = :code OR email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':email', $code);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'valid' => true,
                'id' => $row['id'],
                'name' => $row['name']
            ]);
        } else {
            echo json_encode(['valid' => false]);
        }
    } catch (Exception $e) {
        echo json_encode(['valid' => false, 'error' => $e->getMessage()]);
    }
    exit; // Stop execution after JSON response
}

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $senior_partner_id = $_POST['senior_partner_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $messageType = "danger";
    } elseif (empty($senior_partner_id)) {
        $message = "Invalid Referral Code. Please verify the Senior Partner.";
        $messageType = "danger";
    } else {
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM partners WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $message = "Email already registered.";
            $messageType = "danger";
        } else {
            // Image Upload
            $image_path = "";
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $target_dir = "uploads/partners/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
                $new_filename = "partner_reg_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                }
            }

            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO partners (senior_partner_id, name, email, mobile, gender, image, state, city, pincode, address, password, status) 
                        VALUES (:sp_id, :name, :email, :mobile, :gender, :image, :state, :city, :pincode, :address, :password, 'active')";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':sp_id', $senior_partner_id);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':image', $image_path);
                $stmt->bindParam(':state', $state);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':pincode', $pincode);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':password', $hashed_password);
                
                if ($stmt->execute()) {
                    $message = "Registration successful! You can now login.";
                    $messageType = "success";
                } else {
                    $message = "Registration failed. Please try again.";
                    $messageType = "danger";
                }
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Partner - WaryChary Care</title>
    <!-- FontAwesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/footer.css?v=<?php echo time(); ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Registration Page Specific Styles */
        body {
            background-color: #f8f9fa;
        }
        .header-main {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<?php include_once 'includes/header.php'; ?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold text-primary">Become a Partner</h2>
                    <p class="text-center text-muted mb-4">Join our growing network and start your journey with WaryChary.</p>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" id="partnerForm">
                        
                        <!-- Step 1: Referral Verification -->
                        <div class="mb-4 p-4 bg-light rounded-3 border">
                            <h5 class="mb-3 text-dark"><i class="fas fa-user-friends me-2"></i>Referral Details</h5>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-medium">Referral Code or Senior Partner Email *</label>
                                    <div class="input-group">
                                        <input type="text" id="referral_code" class="form-control" placeholder="Enter code or email" required>
                                        <button class="btn btn-dark" type="button" id="verifyBtn">Verify Code</button>
                                    </div>
                                    <input type="hidden" name="senior_partner_id" id="senior_partner_id" required>
                                </div>
                                <div class="col-md-12">
                                    <div id="referral_status" class="mt-2 small"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Personal Details -->
                        <h5 class="mb-3 mt-4 text-dark"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required placeholder="Enter email address">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number *</label>
                                <input type="text" name="mobile" class="form-control" required placeholder="Enter mobile number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender *</label>
                                <select name="gender" class="form-select" required>
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <!-- Profile Image with Preview -->
                            <div class="col-md-12">
                                <label class="form-label">Profile Picture (Optional)</label>
                                <div class="d-flex align-items-center gap-3 p-2 border rounded bg-white">
                                    <img id="imagePreview" src="https://via.placeholder.com/100" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover; display: none;">
                                    <div class="flex-grow-1">
                                        <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Supported formats: JPG, PNG.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Address Details -->
                        <h5 class="mb-3 mt-4 text-dark"><i class="fas fa-map-marker-alt me-2"></i>Address Details</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">State *</label>
                                <input type="text" name="state" class="form-control" required placeholder="State">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City *</label>
                                <input type="text" name="city" class="form-control" required placeholder="City">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode *</label>
                                <input type="text" name="pincode" class="form-control" required placeholder="Pincode">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full Address *</label>
                                <textarea name="address" class="form-control" rows="2" required placeholder="Enter full address"></textarea>
                            </div>
                        </div>

                        <!-- Step 4: Security -->
                        <h5 class="mb-3 mt-4 text-dark"><i class="fas fa-lock me-2"></i>Account Security</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Create Password *</label>
                                <input type="password" name="password" class="form-control" required placeholder="Create password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm password">
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" id="submitBtn">Register as Partner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Image Preview
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Referral Verification
    document.getElementById('verifyBtn').addEventListener('click', function() {
        // Debugging: Confirm click
        console.log("Verify button clicked");

        const codeRaw = document.getElementById('referral_code').value;
        const code = codeRaw ? codeRaw.trim() : '';
        const statusDiv = document.getElementById('referral_status');
        const hiddenId = document.getElementById('senior_partner_id');
        
        // Define API URL dynamically using PHP
        const apiUrl = "<?php echo $_SERVER['PHP_SELF']; ?>?action=validate_referral";

        if (!code) {
            statusDiv.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle"></i> Please enter a code or email.</span>';
            document.getElementById('referral_code').focus(); // Focus input
            return;
        }

        statusDiv.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> Verifying...</span>';

        // Add timestamp to prevent caching
        const fetchUrl = `${apiUrl}&code=${encodeURIComponent(code)}&_=${new Date().getTime()}`;
        console.log("Fetching:", fetchUrl);

        fetch(fetchUrl)
            .then(response => {
                console.log("Response Status:", response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.text();
            }) 
            .then(text => {
                console.log("Raw API Response:", text);
                try {
                    const cleanText = text.trim();
                    const data = JSON.parse(cleanText);
                    
                    if (data.valid) {
                        statusDiv.innerHTML = `<div class="alert alert-success py-2 mb-0"><i class="fas fa-check-circle me-1"></i> Verified! Your Senior Partner is <strong>${data.name}</strong></div>`;
                        hiddenId.value = data.id;
                        
                        // Valid Styling
                        const input = document.getElementById('referral_code');
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                        input.style.borderColor = "#198754"; // Bootstrap success green
                    } else {
                        statusDiv.innerHTML = '<div class="alert alert-danger py-2 mb-0"><i class="fas fa-times-circle me-1"></i> Invalid Referral Code or Email.</div>';
                        hiddenId.value = '';
                        
                        // Invalid Styling
                        const input = document.getElementById('referral_code');
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');
                    }
                } catch (e) {
                    console.error("JSON Parse Error:", e, "Response:", text);
                    statusDiv.innerHTML = `<div class="alert alert-warning">Server Error. Please try again or contact support.<br><small>${e.message}</small></div>`;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = `<div class="alert alert-danger">Connection Error. Please check your internet.<br><small>${error.message}</small></div>`;
                console.error('Fetch Error:', error);
            });
    });

    // Prevent form submission if referral is not verified
    document.getElementById('partnerForm').addEventListener('submit', function(e) {
        const hiddenId = document.getElementById('senior_partner_id').value;
        if (!hiddenId) {
            e.preventDefault();
            alert("Please verify your Referral Code before registering.");
            document.getElementById('referral_code').focus();
            
            // Scroll to referral section
            document.getElementById('referral_code').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Mobile Menu Fix (Re-implement logic if needed, but header.php might handle it)
    const mobileBtn = document.getElementById('mobile-menu-btn');
    if (mobileBtn) {
        // Ensure listener isn't duplicated if header.php already defines it
    }
</script>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>
