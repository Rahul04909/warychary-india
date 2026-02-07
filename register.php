<?php
include_once 'database/db_config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Fetch SMTP Settings
$smtpSettings = [];
try {
    $stmt = $db->prepare("SELECT * FROM smtp_settings LIMIT 1");
    $stmt->execute();
    $smtpSettings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching SMTP settings: " . $e->getMessage());
}

// Function to send user welcome email
function sendUserWelcomeEmail($toEmail, $toName, $password, $smtpSettings) {
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
        $mail->Subject = 'Welcome to WaryChary!';
        
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
                    <p>Congratulations! You have successfully registered with WaryChary.</p>
                    
                    <p>Here are your account credentials:</p>
                    <div class='details-box'>
                        <div class='detail-row'>
                            <span class='detail-label'>Email:</span>
                            <span class='detail-value'>$toEmail</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Password:</span>
                            <span class='detail-value'>$password</span>
                        </div>
                    </div>
                    
                    <a href='https://warychary.com/user/login.php' class='btn-login'>Login to Account</a>
                    
                    <p style='font-size: 14px; text-align: center;'>Please change your password after your first login for security.</p>
                </div>
                <div class='email-footer'>
                     <p>&copy; " . date('Y') . " WaryChary. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $emailBody;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $referral_input = trim($_POST['referral_code']); // Code, Email, or Mobile

    // Logic to find Partner ID
    $partner_id = null;
    if (!empty($referral_input)) {
        // Search by referral_code, email, or mobile
        $stmt_partner = $db->prepare("SELECT id FROM partners WHERE referral_code = :input OR email = :input OR mobile = :input LIMIT 1");
        $stmt_partner->bindParam(':input', $referral_input);
        $stmt_partner->execute();
        
        if ($stmt_partner->rowCount() > 0) {
            $partner_row = $stmt_partner->fetch(PDO::FETCH_ASSOC);
            $partner_id = $partner_row['id'];
        } else {
            $message = "Invalid Partner Referral Code/Email/Mobile. Please check and try again.";
            $messageType = "error";
        }
    } else {
        $message = "Partner Referral Code is required.";
        $messageType = "error";
    }

    if (empty($message)) {
        if ($password !== $confirm_password) {
            $message = "Passwords do not match!";
            $messageType = "error";
        } else {
            // Image Upload
            $image_path = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "assets/uploads/users/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $file_name;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                }
            }

            // Check if email or mobile exists in users table
            $check_user = $db->prepare("SELECT id FROM users WHERE email = :email OR mobile = :mobile");
            $check_user->bindParam(':email', $email);
            $check_user->bindParam(':mobile', $mobile);
            $check_user->execute();

            if ($check_user->rowCount() > 0) {
                $message = "Email or Mobile already registered!";
                $messageType = "error";
            } else {
                // Insert User
                $sql = "INSERT INTO users (partner_id, name, email, mobile, gender, image, state, city, pincode, address, password) 
                        VALUES (:partner_id, :name, :email, :mobile, :gender, :image, :state, :city, :pincode, :address, :password)";
                
                $stmt = $db->prepare($sql);
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt->bindParam(':partner_id', $partner_id); // Can be null if we allowed it, but strict here
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':image', $image_path);
                $stmt->bindParam(':state', $state);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':pincode', $pincode);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':password', $password_hash);
                
                if ($stmt->execute()) {
                    // Send Welcome Email
                    sendUserWelcomeEmail($email, $name, $password, $smtpSettings);
                    
                    $message = "Registration successful! You can now login.";
                    $messageType = "success";
                } else {
                    $message = "Registration failed. Please try again.";
                    $messageType = "error";
                }
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
    <title>User Registration - WaryChary</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;       /* Indigo */
            --primary-dark: #4f46e5;        /* Darker Indigo for hover */
            --accent-color: #8b5cf6;        /* Violet */
            --bg-color: #0f172a;            /* Deep Navy */
            --card-bg: #1e293b;             /* Slate */
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: #334155;
            --border-color: #475569;
            --success-color: #10b981;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .registration-container {
            width: 100%;
            max-width: 900px;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .registration-header {
            text-align: center;
            padding: 40px 30px 20px;
            background: linear-gradient(to right, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
            border-bottom: 1px solid var(--border-color);
        }

        .brand-logo {
            height: 50px;
            margin-bottom: 15px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, white, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-body {
            padding: 40px;
        }

        .form-section-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-muted);
        }

        .form-control, .form-select {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }
        
        /* Placeholder color */
        .form-control::placeholder {
            color: #64748b;
        }

        /* Image Upload */
        .image-upload-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            border-radius: 50%;
            border: 2px dashed var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255,255,255,0.03);
        }

        .image-upload-wrapper:hover {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }

        .image-upload-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .image-icon {
            font-size: 32px;
            color: var(--text-muted);
        }
        
        .upload-hint {
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
            margin-top: -20px;
            margin-bottom: 30px;
        }

        /* Buttons */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            font-weight: 600;
            padding: 14px;
            border-radius: 10px;
            border: none;
            width: 100%;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px var(--primary-color);
        }

        .password-toggle {
            cursor: pointer;
            color: var(--text-muted);
            z-index: 10;
        }
        
        /* Hide default password eye */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-body {
                padding: 20px;
            }
            .registration-container {
                border-radius: 15px;
            }
        }
        
        .alert {
            border-radius: 8px;
            font-size: 14px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

    </style>
</head>
<body>

<div class="registration-container">
    <div class="registration-header">
        <img src="assets/logo/logo.png" alt="WaryChary" class="brand-logo">
        <h1 class="form-title">Create User Account</h1>
        <p class="text-muted">Join our community today</p>
    </div>

    <div class="form-body">
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" id="registerForm">
            
            <!-- Profile Image -->
            <div class="image-upload-wrapper" onclick="document.getElementById('profileImage').click()">
                <i class="fas fa-camera image-icon" id="uploadIcon"></i>
                <img id="imagePreview" src="#" alt="Preview">
                <input type="file" name="image" id="profileImage" accept="image/*" style="display: none;" onchange="previewImage(this)">
            </div>
            <p class="upload-hint">Tap to upload profile photo</p>

            <!-- Personal Details -->
            <div class="form-section-title"><i class="fas fa-user"></i> Personal Details</div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" required placeholder="John Doe">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            
                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" name="mobile" class="form-control" required placeholder="9876543210" pattern="[0-9]{10}">
                    </div>
                </div>
            </div>

            <!-- Partner Info -->
            <div class="form-section-title mt-4"><i class="fas fa-handshake"></i> Referral Info</div>
            
            <div class="row">
                <div class="col-12">
                     <label class="form-label">Partner Referral <span class="text-danger">*</span></label>
                     <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                        <input type="text" name="referral_code" class="form-control" required placeholder="Enter Partner Code, Email, or Mobile">
                     </div>
                     <div class="form-text text-muted">You must provide a valid Partner Code, Email, or Mobile Number to register.</div>
                </div>
            </div>

            <!-- Location -->
            <div class="form-section-title mt-4"><i class="fas fa-map-marker-alt"></i> Address Details</div>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control" required placeholder="State">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" required placeholder="City">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" class="form-control" required placeholder="Pincode" pattern="[0-9]{6}">
                </div>
                <div class="col-12">
                    <label class="form-label">Full Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="2" required placeholder="House No, Street, Landmark..."></textarea>
                </div>
            </div>

            <!-- Security -->
            <div class="form-section-title mt-4"><i class="fas fa-lock"></i> Security</div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Create Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Create strong password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Confirm password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-submit">
                Register Account <i class="fas fa-arrow-right ms-2"></i>
            </button>
            
            <div class="text-center mt-4 text-muted">
                Already have an account? <a href="user/login.php" class="text-decoration-none" style="color: var(--accent-color);">Login Here</a>
            </div>

        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('uploadIcon').style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const icon = passwordInput.parentElement.querySelector('.password-toggle i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
