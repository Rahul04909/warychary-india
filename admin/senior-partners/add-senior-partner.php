<?php
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Include PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
function sendSeniorPartnerEmail($toEmail, $toName, $referralCode, $password, $smtpSettings) {
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
        $mail->Subject = 'Welcome to WaryChary Senior Partner Program';
        
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
                .footer-links a { color: #6366f1; text-decoration: none; margin: 0 10px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h1>Welcome to WaryChary</h1>
                </div>
                <div class='email-body'>
                    <p class='welcome-text'>Hello $toName,</p>
                    <p>Congratulations! You have successfully been registered as a <strong>Senior Partner</strong> with WaryChary.</p>
                    
                    <p>Here are your account credentials:</p>
                    <div class='details-box'>
                        <div class='detail-row'>
                            <span class='detail-label'>Referral Code:</span>
                            <span class='detail-value'>$referralCode</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Password:</span>
                            <span class='detail-value'>$password</span>
                        </div>
                    </div>
                    
                    <a href='https://warychary.com/admin/login.php' class='btn-login'>Login to Dashboard</a>
                    
                    <p style='font-size: 14px; text-align: center;'>Please change your password after your first login for security.</p>
                </div>
                <div class='email-footer'>
                    <p><strong>Need Help?</strong></p>
                    <p>Phone: +91-9813716032 | Email: Support@WaryChary.com</p>
                    <div class='footer-links' style='margin-top: 10px;'>
                        <a href='#'>Website</a> • <a href='#'>Privacy Policy</a>
                    </div>
                    <p style='margin-top: 15px;'>&copy; " . date('Y') . " WaryChary. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $emailBody;
        $mail->AltBody = "Welcome $toName! Referral Code: $referralCode, Password: $password. Support: +91-9813716032";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

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
    $mobile = $_POST['mobile']; // New Mobile Field
    $gender = $_POST['gender'];
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
            // Check if email or mobile already exists
            $check_exists = $db->prepare("SELECT id FROM senior_partners WHERE email = :email OR mobile = :mobile");
            $check_exists->bindParam(':email', $email);
            $check_exists->bindParam(':mobile', $mobile);
            $check_exists->execute();
            
            if ($check_exists->rowCount() > 0) {
                $message = "Email or Mobile Number already exists!";
                $messageType = "danger";
            } else {
                // Insert Senior Partner
                $sql = "INSERT INTO senior_partners (name, email, mobile, image, gender, state, city, pincode, address, password, referral_code, commission) 
                        VALUES (:name, :email, :mobile, :image, :gender, :state, :city, :pincode, :address, :password, :referral_code, :commission)";
                
                $stmt = $db->prepare($sql);
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
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
                    // Send Welcome Email
                    sendSeniorPartnerEmail($email, $name, $referral_code, $password, $smtpSettings);
                    
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
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" required placeholder="Enter mobile number" pattern="[0-9]{10}" title="10 digit mobile number">
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
