<?php
ob_start();
session_start();

// Auth Check
if (!isset($_SESSION['partner_id'])) {
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
$partner_id = $_SESSION['partner_id'];

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
function sendUserEmail($toEmail, $toName, $password, $smtpSettings) {
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
                    <p>You have been registered as a user by one of our partners. Welcome to WaryChary!</p>
                    
                    <p>Here are your login credentials:</p>
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
                    
                    <p style='font-size: 14px; text-align: center;'>Please change your password after your first login.</p>
                </div>
                <div class='email-footer'>
                   <p>&copy; " . date('Y') . " WaryChary. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $emailBody;
        $mail->AltBody = "Welcome $toName! Email: $toEmail, Password: $password. Login at https://warychary.com/user/login.php";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $gender = $_POST['gender'] ?? null;
    $state = trim($_POST['state']);
    $city = trim($_POST['city']); 
    $pincode = trim($_POST['pincode']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_image = null;

    if (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }

    // Image Upload
    if (empty($error_message) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/uploads/users/"; // Adjust path relative to partner folder
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_file_type = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_file_name = uniqid() . "." . $image_file_type;
        $target_file = $target_dir . $new_file_name;
        
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $user_image = "assets/uploads/users/" . $new_file_name; // Store relative path for DB
            } else {
                $error_message = "Sorry, there was an error uploading the image.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    }

    if (empty($error_message)) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email OR mobile = :mobile");
            $stmt->execute([':email' => $email, ':mobile' => $mobile]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "A user with this email or phone number already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $db->prepare("INSERT INTO users (partner_id, name, email, mobile, gender, image, state, city, pincode, address, password) 
                                      VALUES (:pid, :name, :email, :mobile, :gender, :image, :state, :city, :pincode, :address, :password)");
                
                $result = $stmt->execute([
                    ':pid' => $partner_id, // Automatically linked
                    ':name' => $name,
                    ':email' => $email,
                    ':mobile' => $mobile,
                    ':gender' => $gender,
                    ':image' => $user_image,
                    ':state' => $state,
                    ':city' => $city,
                    ':pincode' => $pincode,
                    ':address' => $address,
                    ':password' => $hashed_password
                ]);

                if ($result) {
                    sendUserEmail($email, $name, $password, $smtpSettings);
                    $success_message = "New User added successfully! Credentials sent via email.";
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
        <h1 class="page-title">Add New User</h1>
        <p class="text-muted">Register a new user under your referral.</p>
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
                    <h5 class="card-title mb-4">User Personal Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" name="mobile" required pattern="[0-9]{10}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
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
                             <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>

                    <h5 class="card-title mb-4">Address Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" name="pincode" required pattern="[0-9]{6}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Full Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>

                    <h5 class="card-title mb-4">Security</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Create Password</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required minlength="6">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" name="add_user" class="btn btn-primary px-4">
                            <i class="fas fa-user-plus me-2"></i> Register User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
