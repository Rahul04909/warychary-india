<?php
// Start output buffering and session
ob_start();
session_start();

$url_prefix = ''; // For header/footer includes
require_once 'database/db_config.php';

// Try to load PHPMailer if available via Composer, otherwise skip or implement simpler mail()
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection();

// Function to generate an 8-digit alphanumeric ID
function generatePartnerId($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Function to validate referral code
function validateReferralCode($db, $referral_code) {
    try {
        // Check if referral code matches referral_code, email, or phone in senior_partners table
        // Adjust column names based on actual DB schema (assuming 'referral_code' exists in 'senior_partners')
        $stmt = $db->prepare("SELECT id, name FROM senior_partners WHERE referral_code = :code OR email = :code");
        $stmt->execute([':code' => $referral_code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error validating referral code: " . $e->getMessage());
        return false;
    }
}

// Function to send welcome email
function sendPartnerEmail($toEmail, $toName, $partnerId, $password, $smtpSettings) {
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        // Fallback or just return false if PHPMailer is not installed
        return false; 
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings, if $smtpSettings array is populated
        if (!empty($smtpSettings)) {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = $smtpSettings['smtp_host'] ?? '';
            $mail->SMTPAuth = true;
            $mail->Username = $smtpSettings['smtp_username'] ?? '';
            $mail->Password = $smtpSettings['smtp_password'] ?? '';
            $mail->SMTPSecure = $smtpSettings['smtp_encryption'] ?? '';
            $mail->Port = $smtpSettings['smtp_port'] ?? 587;
    
            // Recipients
            $mail->setFrom($smtpSettings['smtp_from_email'] ?? 'noreply@warychary.com', $smtpSettings['smtp_from_name'] ?? 'WaryChary');
            $mail->addAddress($toEmail, $toName);
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to WaryCharyCare - Your Partner Account Details';
            // Simplified body for now
            $mail->Body    = "Welcome $toName! Your Partner ID is: $partnerId";
    
            $mail->send();
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

$success_message = '';
$error_message = '';
$referral_info = null;

// Handle AJAX request for referral code validation
if (isset($_POST['action']) && $_POST['action'] === 'validate_referral') {
    // Clear buffer to ensure clean JSON
    ob_clean();
    header('Content-Type: application/json');
    $referral_code = trim($_POST['referral_code']);
    
    $result = validateReferralCode($db, $referral_code);
    
    echo json_encode([
        'valid' => (bool)$result, 
        'name' => $result ? $result['name'] : '',
        'senior_partner_id' => $result ? $result['id'] : null
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_partner'])) {
    $partner_name = trim($_POST['partner_name']);
    $partner_email = trim($_POST['partner_email']);
    $partner_phone = trim($_POST['partner_phone']);
    $partner_gender = $_POST['partner_gender'] ?? null;
    $partner_state = trim($_POST['partner_state']);
    $partner_district = trim($_POST['partner_district']); // Using as district/city
    $partner_pincode = trim($_POST['partner_pincode']);
    $partner_full_address = trim($_POST['partner_full_address']);
    $partner_password = trim($_POST['partner_password']);
    $partner_confirm_password = trim($_POST['partner_confirm_password']);
    $referral_code = trim($_POST['referral_code'] ?? '');
    $partner_image = null;

    // Validate passwords
    if (strlen($partner_password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($partner_password !== $partner_confirm_password) {
        $error_message = "Passwords do not match.";
    }

    // Validate referral code if provided
    if (empty($error_message) && !empty($referral_code)) {
        $referral_info = validateReferralCode($db, $referral_code);
        if (!$referral_info) {
            $error_message = "Invalid referral code.";
        }
    }

    // Handle image upload
    if (isset($_FILES['partner_image']) && $_FILES['partner_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/partners/"; // Adjusted path to match existing structure
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_file_type = strtolower(pathinfo($_FILES['partner_image']['name'], PATHINFO_EXTENSION));
        $new_file_name = uniqid('partner_') . "." . $image_file_type;
        $target_file = $target_dir . $new_file_name;
        
        $check = getimagesize($_FILES['partner_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['partner_image']['tmp_name'], $target_file)) {
                $partner_image = $target_file;
            } else {
                $error_message = "Sorry, there was an error uploading your image.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    }

    if (empty($error_message)) {
        try {
            // Check if email or phone already exists in partners table
            // Note: Schema uses 'mobile' not 'phone' based on previous file, adjusting query
            $stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE email = :email OR mobile = :phone");
            $stmt->execute([':email' => $partner_email, ':phone' => $partner_phone]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "A partner with this email or phone number already exists.";
            } else {
                // Generate ID if not auto-increment, but usually IDs are auto-increment or UUIDs. 
                // The sample code generates a Partner ID. Let's assume we need to store it if the schema supports it.
                // Assuming 'partners' table has columns: specific to previous schema:
                // senior_partner_id, name, email, mobile, gender, image, state, city, pincode, address, password, status
                
                // Mapping:
                // partner_id -> ?? (Maybe not in schema, skipping or using valid column if exists)
                // earning -> ?? (Maybe not in schema)
                // referred_by_senior_partner -> senior_partner_id
                
                $referred_by_senior_partner = $referral_info ? $referral_info['id'] : null;
                $hashed_password = password_hash($partner_password, PASSWORD_DEFAULT);
                
                // Using known schema columns
                $stmt = $db->prepare("INSERT INTO partners (senior_partner_id, name, email, mobile, gender, image, state, city, pincode, address, password, status) 
                                      VALUES (:sp_id, :name, :email, :mobile, :gender, :image, :state, :city, :pincode, :address, :password, 'active')");
                
                $result = $stmt->execute([
                    ':sp_id' => $referred_by_senior_partner,
                    ':name' => $partner_name,
                    ':email' => $partner_email,
                    ':mobile' => $partner_phone,
                    ':gender' => $partner_gender,
                    ':image' => $partner_image,
                    ':state' => $partner_state,
                    ':city' => $partner_district,
                    ':pincode' => $partner_pincode,
                    ':address' => $partner_full_address,
                    ':password' => $hashed_password
                ]);

                if ($result) {
                    // Try to send email if SMTP settings exist, otherwise just success
                    $success_message = "Registration successful! You can now login.";
                } else {
                     $error_message = "Registration failed. Database insert error.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
            error_log($e->getMessage());
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
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/footer.css?v=<?php echo time(); ?>">
    
    <style>
        .registration-form {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .form-header {
            background: linear-gradient(45deg, #6f42c1, #8e44ad);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .form-body {
            padding: 2rem;
        }
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #6f42c1, #8e44ad);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            width: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.4);
        }
        .image-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6f42c1;
        }
        .referral-feedback {
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }
        .referral-valid {
            color: #198754;
        }
        .referral-invalid {
            color: #dc3545;
        }
        .password-feedback {
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }
        .password-match {
            color: #198754;
        }
        .password-mismatch {
            color: #dc3545;
        }
    </style>
</head>
<body>

<?php include_once 'includes/header.php'; ?>

    <div class="container">
        <?php if ($success_message): ?>
            <div class="alert alert-success mt-4" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="registration-form">
            <div class="form-header">
                <h2>Become a Partner</h2>
                <p>Join our growing network of successful partners</p>
            </div>
            <div class="form-body">
                <form action="" method="POST" enctype="multipart/form-data" id="partnerForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="partner_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="partner_name" name="partner_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="partner_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="partner_email" name="partner_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="partner_phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="partner_phone" name="partner_phone" required pattern="[0-9]{10}">
                            </div>
                            <div class="mb-3">
                                <label for="partner_gender" class="form-label">Gender *</label>
                                <select class="form-select" id="partner_gender" name="partner_gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="partner_password" class="form-label">Create Password *</label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="partner_password" name="partner_password" required minlength="6">
                                    <i class="bi bi-eye-slash password-toggle" id="togglePassword1"></i>
                                </div>
                                <small class="text-muted">Password must be at least 6 characters long</small>
                            </div>
                            <div class="mb-3">
                                <label for="partner_confirm_password" class="form-label">Confirm Password *</label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="partner_confirm_password" name="partner_confirm_password" required minlength="6">
                                    <i class="bi bi-eye-slash password-toggle" id="togglePassword2"></i>
                                </div>
                                <div id="passwordFeedback" class="password-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="partner_image" class="form-label">Profile Picture</label>
                                <div class="image-preview" id="imagePreview">
                                    <i class="bi bi-person-circle" style="font-size: 3rem; color: #ddd;"></i>
                                </div>
                                <input type="file" class="form-control" id="partner_image" name="partner_image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="partner_state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="partner_state" name="partner_state" required>
                            </div>
                            <div class="mb-3">
                                <label for="partner_district" class="form-label">City/District *</label>
                                <input type="text" class="form-control" id="partner_district" name="partner_district" required>
                            </div>
                            <div class="mb-3">
                                <label for="partner_pincode" class="form-label">Pincode *</label>
                                <input type="text" class="form-control" id="partner_pincode" name="partner_pincode" required pattern="[0-9]{6}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="partner_full_address" class="form-label">Full Address *</label>
                                <textarea class="form-control" id="partner_full_address" name="partner_full_address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="referral_code" class="form-label">Referral Code (Optional)</label>
                                <input type="text" class="form-control" id="referral_code" name="referral_code">
                                <div id="referralFeedback" class="referral-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="register_partner" class="btn btn-primary">Register as Partner</button>
                </form>
            </div>
        </div>
        <div class="form-group text-center mt-3 mb-5">
            <p>Already a partner? <a href="partner/login.php" class="btn btn-outline-primary">Login</a></p>
        </div>
    </div>

<?php include_once 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview
        document.getElementById('partner_image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<i class="bi bi-person-circle" style="font-size: 3rem; color: #ddd;"></i>';
            }
        });

        // Referral code validation
        let referralTimeout;
        document.getElementById('referral_code').addEventListener('input', function(e) {
            clearTimeout(referralTimeout);
            const referralCode = e.target.value.trim();
            const feedback = document.getElementById('referralFeedback');
            
            // Allow user to clear the code without error
            if (referralCode === "") {
                feedback.textContent = '';
                feedback.className = 'referral-feedback';
                return;
            }

            if (referralCode) {
                referralTimeout = setTimeout(() => {
                    const formData = new FormData();
                    formData.append('action', 'validate_referral');
                    formData.append('referral_code', referralCode);

                    fetch('', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            feedback.className = 'referral-feedback referral-valid';
                            feedback.textContent = `Referral by: ${data.name}`;
                        } else {
                            feedback.className = 'referral-feedback referral-invalid';
                            feedback.textContent = 'Invalid referral code';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        feedback.className = 'referral-feedback referral-invalid';
                        feedback.textContent = 'Error validating referral code';
                    });
                }, 500);
            } 
        });

        // Form validation
        document.getElementById('partnerForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('partner_phone').value;
            const pincode = document.getElementById('partner_pincode').value;
            const password = document.getElementById('partner_password').value;
            const confirmPassword = document.getElementById('partner_confirm_password').value;
            const referralFeedback = document.getElementById('referralFeedback'); // Check referral status
            
            // Check if referral code is explicitly invalid before submitting
            if (referralFeedback.classList.contains('referral-invalid')) {
                e.preventDefault();
                alert('Please enter a valid Referral Code or clear the field.');
                return;
            }

            if (phone.length !== 10 || !/^\d+$/.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number');
                return;
            }
            
            if (pincode.length !== 6 || !/^\d+$/.test(pincode)) {
                e.preventDefault();
                alert('Please enter a valid 6-digit pincode');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
        });

        // Password toggle functionality
        document.getElementById('togglePassword1').addEventListener('click', function() {
            const passwordField = document.getElementById('partner_password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        document.getElementById('togglePassword2').addEventListener('click', function() {
            const passwordField = document.getElementById('partner_confirm_password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Password matching validation
        document.getElementById('partner_confirm_password').addEventListener('input', function() {
            const password = document.getElementById('partner_password').value;
            const confirmPassword = this.value;
            const feedback = document.getElementById('passwordFeedback');
            
            if (confirmPassword) {
                if (password === confirmPassword) {
                    feedback.className = 'password-feedback password-match';
                    feedback.textContent = 'Passwords match';
                } else {
                    feedback.className = 'password-feedback password-mismatch';
                    feedback.textContent = 'Passwords do not match';
                }
            } else {
                feedback.textContent = '';
            }
        });

        document.getElementById('partner_password').addEventListener('input', function() {
            const password = this.value;
            const confirmPassword = document.getElementById('partner_confirm_password').value;
            const feedback = document.getElementById('passwordFeedback');
            
            if (confirmPassword) {
                if (password === confirmPassword) {
                    feedback.className = 'password-feedback password-match';
                    feedback.textContent = 'Passwords match';
                } else {
                    feedback.className = 'password-feedback password-mismatch';
                    feedback.textContent = 'Passwords do not match';
                }
            }
        });
    </script>
</body>
</html>
