<?php
ob_start();
session_start();

// Auth Check
if (!isset($_SESSION['senior_partner_id'])) {
    header("Location: login.php");
    exit;
}

$url_prefix = '../'; // Go up one level
require_once '../database/db_config.php';

// Load PHPMailer
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection();
$senior_partner_id = $_SESSION['senior_partner_id'];

// Initial Fetch of Partner's Data (optional, maybe for reference)
// ...

// Fetch SMTP Settings
$smtpSettings = [];
try {
    $stmt = $db->prepare("SELECT * FROM smtp_settings LIMIT 1");
    $stmt->execute();
    $smtpSettings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching SMTP settings: " . $e->getMessage());
}

// Function to send welcome email
function sendPartnerEmail($toEmail, $toName, $partnerId, $password, $smtpSettings) {
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer') || empty($smtpSettings)) {
        return false; 
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtpSettings['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpSettings['username'];
        $mail->Password   = $smtpSettings['password'];
        $mail->SMTPSecure = $smtpSettings['encryption'];
        $mail->Port       = $smtpSettings['port'];

        $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to WaryChary Partner Program';
        
        $emailBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f7f9fc; margin: 0; padding: 0; }
                .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
                .email-header { background: #0f172a; padding: 30px; text-align: center; }
                .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; }
                .email-body { padding: 30px; }
                .welcome-text { font-size: 18px; color: #0f172a; margin-bottom: 20px; }
                .details-box { background: #f8fafc; border: 1px dashed #6366f1; border-radius: 8px; padding: 20px; margin: 20px 0; }
                .detail-row { margin-bottom: 10px; font-size: 15px; }
                .detail-label { font-weight: 600; color: #64748b; width: 120px; display: inline-block; }
                .detail-value { color: #0f172a; font-weight: 500; }
                .btn-login { display: block; width: 200px; margin: 30px auto; background: #6366f1; color: #ffffff; text-align: center; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: 600; }
                .email-footer { background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h1>Welcome to WaryChary</h1>
                </div>
                <div class='email-body'>
                    <p class='welcome-text'>Hello $toName,</p>
                    <p>You have been registered as a partner by your Senior Partner. Welcome to the team!</p>
                    
                    <p>Here are your account credentials:</p>
                    <div class='details-box'>
                        <div class='detail-row'>
                            <span class='detail-label'>Partner ID:</span>
                            <span class='detail-value'>$partnerId</span>
                        </div>
                         <div class='detail-row'>
                            <span class='detail-label'>Referral Code:</span>
                            <span class='detail-value'>$partnerId</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Password:</span>
                            <span class='detail-value'>$password</span>
                        </div>
                    </div>
                    
                    <a href='https://warychary.com/partner/login.php' class='btn-login'>Login to Dashboard</a>
                    
                    <p style='font-size: 14px; text-align: center;'>Please change your password after your first login.</p>
                </div>
                <div class='email-footer'>
                    <p>&copy; " . date('Y') . " WaryChary. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $emailBody;
        $mail->AltBody = "Welcome $toName! Partner ID: $partnerId, Password: $password. Login at https://warychary.com/partner/login.php";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_partner'])) {
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
    $partner_image = null;

    if (strlen($partner_password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($partner_password !== $partner_confirm_password) {
        $error_message = "Passwords do not match.";
    }

    // Image Upload
    if (empty($error_message) && isset($_FILES['partner_image']) && $_FILES['partner_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/partners/"; // Go up to root then uploads
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_file_type = strtolower(pathinfo($_FILES['partner_image']['name'], PATHINFO_EXTENSION));
        $new_file_name = uniqid('partner_') . "." . $image_file_type;
        $target_file = $target_dir . $new_file_name;
        
        $check = getimagesize($_FILES['partner_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['partner_image']['tmp_name'], $target_file)) {
                $partner_image = "uploads/partners/" . $new_file_name; // Store relative path for DB
            } else {
                $error_message = "Sorry, there was an error uploading the image.";
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
                    ':sp_id' => $senior_partner_id, // Automatically linked
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
                    sendPartnerEmail($partner_email, $partner_name, $new_referral_code, $partner_password, $smtpSettings);
                    $success_message = "New Partner added successfully! Credentials sent via email.";
                } else {
                     $error_message = "Registration failed. Database insert error.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">Add New Partner</h1>
        <p class="text-muted">Register a new partner under your team.</p>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <h5 class="card-title mb-4">Personal Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="partner_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="partner_email" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="partner_phone" required pattern="[0-9]{10}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="partner_gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                             <label class="form-label">Profile Image (Optional)</label>
                             <input type="file" class="form-control" name="partner_image" accept="image/*">
                        </div>
                    </div>

                    <h5 class="card-title mb-4">Location Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" name="partner_pincode" required pattern="[0-9]{6}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="partner_district" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="partner_state" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Full Address</label>
                        <textarea class="form-control" name="partner_full_address" rows="2" required></textarea>
                    </div>

                    <h5 class="card-title mb-4">Security</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="partner_password" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="partner_confirm_password" required minlength="6">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" name="add_partner" class="btn btn-primary px-4">
                            <i class="fas fa-plus-circle me-2"></i> Add Partner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
