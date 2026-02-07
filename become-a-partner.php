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
        return false; 
    }

    $mail = new PHPMailer(true);
    try {
        if (!empty($smtpSettings)) {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = $smtpSettings['smtp_host'] ?? '';
            $mail->SMTPAuth = true;
            $mail->Username = $smtpSettings['smtp_username'] ?? '';
            $mail->Password = $smtpSettings['smtp_password'] ?? '';
            $mail->SMTPSecure = $smtpSettings['smtp_encryption'] ?? '';
            $mail->Port = $smtpSettings['smtp_port'] ?? 587;
    
            $mail->setFrom($smtpSettings['smtp_from_email'] ?? 'noreply@warychary.com', $smtpSettings['smtp_from_name'] ?? 'WaryChary');
            $mail->addAddress($toEmail, $toName);
    
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to WaryCharyCare - Your Partner Account Details';
            $mail->Body    = "Welcome $toName! Your Partner ID is: $partnerId. Your Referral Code is: " . $partnerId; // Simplified logic, assuming ID is used or separate code
    
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
    $partner_district = trim($_POST['partner_district']); 
    $partner_pincode = trim($_POST['partner_pincode']);
    $partner_full_address = trim($_POST['partner_full_address']);
    $partner_password = trim($_POST['partner_password']);
    $partner_confirm_password = trim($_POST['partner_confirm_password']);
    $referral_code = trim($_POST['referral_code'] ?? '');
    $partner_image = null;

    if (strlen($partner_password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($partner_password !== $partner_confirm_password) {
        $error_message = "Passwords do not match.";
    }

    if (empty($error_message) && !empty($referral_code)) {
        $referral_info = validateReferralCode($db, $referral_code);
        if (!$referral_info) {
            $error_message = "Invalid referral code.";
        }
    }

    if (isset($_FILES['partner_image']) && $_FILES['partner_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/partners/"; 
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
            $stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE email = :email OR mobile = :mobile");
            $stmt->execute([':email' => $partner_email, ':mobile' => $partner_phone]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "A partner with this email or phone number already exists.";
            } else {
                $referred_by_senior_partner = $referral_info ? $referral_info['id'] : null;
                $hashed_password = password_hash($partner_password, PASSWORD_DEFAULT);
                
                // Generate unique Referral Code
                $new_referral_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                $checkRef = $db->prepare("SELECT id FROM partners WHERE referral_code = :ref");
                $checkRef->execute([':ref' => $new_referral_code]);
                if($checkRef->rowCount() > 0) {
                     $new_referral_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                }

                $commission = 15.00;
                
                $stmt = $db->prepare("INSERT INTO partners (senior_partner_id, name, email, mobile, gender, image, state, city, pincode, address, password, status, referral_code, commission) 
                                      VALUES (:sp_id, :name, :email, :mobile, :gender, :image, :state, :city, :pincode, :address, :password, 'active', :ref_code, :commission)");
                
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
                    ':password' => $hashed_password,
                    ':ref_code' => $new_referral_code,
                    ':commission' => $commission
                ]);

                if ($result) {
                    $new_partner_id = $db->lastInsertId();
                    // Attempt to send email but don't block success
                    sendPartnerEmail($partner_email, $partner_name, $new_referral_code, $partner_password, []);
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
    <!-- Dependencies -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>assets/css/footer.css?v=<?php echo time(); ?>">
    
    <style>
        :root {
            --primary-color: #0f172a; /* Deep Navy */
            --accent-color: #6366f1; /* Soft Purple/Indigo */
            --bg-color: #f7f9fc;
            --input-border: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        .main-wrapper {
            padding: 4rem 0;
            min-height: 100vh;
        }

        .registration-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); /* Premium soft shadow */
            overflow: hidden;
            border: 1px solid #f0f0f0;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header Section */
        .form-header {
            background: #ffffff;
            padding: 3rem 2rem 2rem;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .form-header h2 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }
        
        .trust-note {
            font-size: 0.9rem;
            color: var(--accent-color);
            font-weight: 500;
            background: rgba(99, 102, 241, 0.1);
            display: inline-block;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            margin-top: 1rem;
        }

        /* Form Body */
        .form-body {
            padding: 3rem;
        }

        .form-section-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
            margin-top: 1rem;
        }
        
        .form-section-title:first-child {
            margin-top: 0;
        }

        /* Input Styling */
        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group-custom .form-label {
            font-weight: 500;
            font-size: 0.95rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.3s;
            pointer-events: none;
        }

        .form-control, .form-select {
            height: 52px;
            padding-left: 48px; /* Space for icon */
            padding-right: 16px;
            border: 1px solid var(--input-border);
            border-radius: 10px;
            font-size: 1rem;
            color: var(--text-dark);
            background-color: #fff;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-control:focus + i,
        .input-wrapper:focus-within i {
            color: var(--accent-color);
        }

        /* Profile Preview */
        .profile-upload-container {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #e2e8f0;
            transition: all 0.3s;
        }
        
        .profile-upload-container:hover {
            border-color: var(--accent-color);
            background: #fff;
        }

        .image-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview i {
            font-size: 2.5rem;
            color: #cbd5e1;
        }
        
        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .btn-upload {
            color: var(--accent-color);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            height: 100%;
            width: 100%;
        }

        /* CTA Button */
        .btn-cta {
            background: linear-gradient(135deg, var(--accent-color) 0%, #4f46e5 100%);
            color: white;
            height: 56px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
            color: white;
        }
        
        .btn-cta i {
            font-size: 1rem;
        }

        /* Links */
        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-muted);
        }
        
        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }

        /* Password Toggle */
        .input-wrapper .password-toggle {
            position: absolute;
            right: 16px;
            left: auto; /* Override generic icon style */
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            pointer-events: auto; /* Enable clicks */
            z-index: 10;
        }

        /* Feedback Messages */
        .referral-feedback, .password-feedback {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }
        .referral-valid, .password-match { color: #10b981; }
        .referral-invalid, .password-mismatch { color: #ef4444; }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .main-wrapper {
                padding: 1rem 0;
            }
            .form-body {
                padding: 1.5rem;
            }
            .form-header {
                padding: 2rem 1.5rem;
            }
            .form-header h2 {
                font-size: 1.75rem;
            }
            .image-preview {
                width: 80px;
                height: 80px;
            }
            .btn-cta {
                position: sticky;
                bottom: 20px;
                z-index: 100;
                box-shadow: 0 4px 25px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body>

<?php include_once 'includes/header.php'; ?>

    <div class="main-wrapper">
        <div class="container">
            
            <?php if ($success_message): ?>
                <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4 text-center">
                    <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4 text-center">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="registration-card">
                <div class="form-header">
                    <h2>Become a Partner</h2>
                    <p>Earn commission on every successful referral</p>
                    <div class="trust-note"><i class="fas fa-shield-alt me-1"></i> Secure Registration</div>
                </div>

                <div class="form-body">
                    <form action="" method="POST" enctype="multipart/form-data" id="partnerForm">
                        <div class="row gx-5">
                            
                            <!-- Left Column: Personal Info -->
                            <div class="col-lg-7">
                                <!-- Personal Section -->
                                <div class="form-section-title">Personal Information</div>
                                
                                <div class="input-group-custom">
                                    <label for="partner_name" class="form-label">Full Name</label>
                                    <div class="input-wrapper">
                                        <i class="far fa-user"></i>
                                        <input type="text" class="form-control" id="partner_name" name="partner_name" required placeholder="John Doe">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group-custom">
                                            <label for="partner_email" class="form-label">Email Address</label>
                                            <div class="input-wrapper">
                                                <i class="far fa-envelope"></i>
                                                <input type="email" class="form-control" id="partner_email" name="partner_email" required placeholder="john@example.com">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group-custom">
                                            <label for="partner_phone" class="form-label">Phone Number</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-phone-alt"></i>
                                                <input type="tel" class="form-control" id="partner_phone" name="partner_phone" required pattern="[0-9]{10}" placeholder="9876543210">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group-custom">
                                    <label for="partner_gender" class="form-label">Gender</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-venus-mars"></i>
                                        <select class="form-select" id="partner_gender" name="partner_gender" required>
                                            <option value="" selected disabled>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Security Section -->
                                <div class="form-section-title mt-4">Security</div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group-custom">
                                            <label for="partner_password" class="form-label">Password</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-lock"></i>
                                                <input type="password" class="form-control" id="partner_password" name="partner_password" required minlength="6" placeholder="******">
                                                <i class="far fa-eye-slash password-toggle" id="togglePassword1"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group-custom">
                                            <label for="partner_confirm_password" class="form-label">Confirm Password</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-lock"></i>
                                                <input type="password" class="form-control" id="partner_confirm_password" name="partner_confirm_password" required minlength="6" placeholder="******">
                                                <i class="far fa-eye-slash password-toggle" id="togglePassword2"></i>
                                            </div>
                                            <div id="passwordFeedback" class="password-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Location & Profile -->
                            <div class="col-lg-5">
                                <div class="d-none d-lg-block" style="margin-top: 2.2rem;"></div> <!-- Spacer -->
                                
                                <!-- Profile Photo -->
                                <div class="profile-upload-container">
                                    <div class="image-preview" id="imagePreview">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="upload-btn-wrapper">
                                        <span class="btn-upload"><i class="fas fa-cloud-upload-alt me-1"></i> Upload Profile Photo</span>
                                        <input type="file" class="upload-input" id="partner_image" name="partner_image" accept="image/*">
                                    </div>
                                    <div class="text-muted small mt-2">Optional. Max 2MB.</div>
                                </div>

                                <!-- Location Section -->
                                <div class="form-section-title">Location Details</div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group-custom">
                                            <label for="partner_pincode" class="form-label">Pincode</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-map-pin"></i>
                                                <input type="text" class="form-control" id="partner_pincode" name="partner_pincode" required pattern="[0-9]{6}" placeholder="110001">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group-custom">
                                            <label for="partner_district" class="form-label">City</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-city"></i>
                                                <input type="text" class="form-control" id="partner_district" name="partner_district" required placeholder="Delhi">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group-custom">
                                    <label for="partner_state" class="form-label">State</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-map"></i>
                                        <input type="text" class="form-control" id="partner_state" name="partner_state" required placeholder="New Delhi">
                                    </div>
                                </div>

                                <div class="input-group-custom">
                                    <label for="partner_full_address" class="form-label">Full Address</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-home" style="top: 20px; transform: none;"></i>
                                        <textarea class="form-control" id="partner_full_address" name="partner_full_address" rows="3" required placeholder="House No, Street, Area..." style="height: auto; padding-top: 15px; padding-bottom: 15px;"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Referral Section -->
                                <div class="form-section-title mt-4">Referral (Optional)</div>
                                <div class="input-group-custom">
                                    <div class="input-wrapper">
                                        <i class="fas fa-gift"></i>
                                        <input type="text" class="form-control" id="referral_code" name="referral_code" placeholder="Enter Referral Code">
                                    </div>
                                    <div id="referralFeedback" class="referral-feedback"></div>
                                </div>

                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" name="register_partner" class="btn btn-cta">
                                    Register as Partner <i class="fas fa-arrow-right"></i>
                                </button>
                                <div class="login-link">
                                    Already have an account? <a href="partner/login.php">Login here</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include_once 'includes/footer.php'; ?>

    <!-- JS Scripts -->
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
                preview.innerHTML = '<i class="fas fa-user"></i>';
            }
        });

        // Referral code validation (Preserved Logic)
        let referralTimeout;
        document.getElementById('referral_code').addEventListener('input', function(e) {
            clearTimeout(referralTimeout);
            const referralCode = e.target.value.trim();
            const feedback = document.getElementById('referralFeedback');
            
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
                            feedback.innerHTML = `<i class="fas fa-check-circle me-1"></i> Referral by: ${data.name}`;
                        } else {
                            feedback.className = 'referral-feedback referral-invalid';
                            feedback.innerHTML = `<i class="fas fa-times-circle me-1"></i> Invalid referral code`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        feedback.className = 'referral-feedback referral-invalid';
                        feedback.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Error validating code`;
                    });
                }, 500);
            } 
        });

        // Toggle Password
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.className = type === 'password' ? 'far fa-eye-slash password-toggle' : 'far fa-eye password-toggle';
            });
        }
        
        setupPasswordToggle('togglePassword1', 'partner_password');
        setupPasswordToggle('togglePassword2', 'partner_confirm_password');

        // Password Match
        const pass1 = document.getElementById('partner_password');
        const pass2 = document.getElementById('partner_confirm_password');
        const feedback = document.getElementById('passwordFeedback');

        function checkPasswords() {
            if(pass2.value) {
                if (pass1.value === pass2.value) {
                    feedback.className = 'password-feedback password-match';
                    feedback.innerHTML = '<i class="fas fa-check"></i> Passwords match';
                } else {
                    feedback.className = 'password-feedback password-mismatch';
                    feedback.innerHTML = '<i class="fas fa-times"></i> Passwords do not match';
                }
            } else {
                feedback.textContent = '';
            }
        }

        pass1.addEventListener('input', checkPasswords);
        pass2.addEventListener('input', checkPasswords);

        // Form Validation
        document.getElementById('partnerForm').addEventListener('submit', function(e) {
            const referralFeedback = document.getElementById('referralFeedback');
            if (referralFeedback.classList.contains('referral-invalid')) {
                e.preventDefault();
                alert('Please check your referral code.');
            }
        });
    </script>
</body>
</html>
