<?php
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

// Include PHPMailer
require __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Fetch Current Settings
$query = "SELECT * FROM smtp_settings LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle Save/Test
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['save_settings'])) {
        // Save Settings Logic
        $host = $_POST['host'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $port = $_POST['port'];
        $encryption = $_POST['encryption'];
        $from_email = $_POST['from_email'];
        $from_name = $_POST['from_name'];
        
        try {
            if ($settings) {
                // Update
                $sql = "UPDATE smtp_settings SET host=:host, username=:username, password=:password, port=:port, encryption=:encryption, from_email=:from_email, from_name=:from_name WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $settings['id']);
            } else {
                // Insert
                $sql = "INSERT INTO smtp_settings (host, username, password, port, encryption, from_email, from_name) VALUES (:host, :username, :password, :port, :encryption, :from_email, :from_name)";
                $stmt = $db->prepare($sql);
            }
            
            $stmt->bindParam(':host', $host);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':port', $port);
            $stmt->bindParam(':encryption', $encryption);
            $stmt->bindParam(':from_email', $from_email);
            $stmt->bindParam(':from_name', $from_name);
            
            if ($stmt->execute()) {
                $message = "SMTP Settings saved successfully!";
                $messageType = "success";
                // Refresh settings
                $stmt = $db->prepare($query);
                $stmt->execute();
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $message = "Error saving settings: " . $e->getMessage();
            $messageType = "danger";
        }
        
    } elseif (isset($_POST['test_email'])) {
        // Test Email Logic
        $test_recipient = $_POST['test_recipient'];
        
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $settings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $settings['username'];
            $mail->Password   = $settings['password'];
            $mail->SMTPSecure = $settings['encryption'];
            $mail->Port       = $settings['port'];

            // Recipients
            $mail->setFrom($settings['from_email'], $settings['from_name']);
            $mail->addAddress($test_recipient);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'SMTP Test Email - WaryChary Admin';
            $mail->Body    = '<h1>Success!</h1><p>Your SMTP settings are configured correctly.</p><p>Sent from WaryChary Admin Panel.</p>';

            $mail->send();
            $message = "Test email sent successfully to $test_recipient";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $messageType = "danger";
        }
    }
}
?>

<div class="page-header">
    <div class="header-title">
        <h1>SMTP Settings</h1>
        <p>Configure email server settings</p>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Configuration Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Server Configuration</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="host" class="form-control" value="<?php echo htmlspecialchars($settings['host'] ?? ''); ?>" required placeholder="smtp.example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Port</label>
                            <input type="number" name="port" class="form-control" value="<?php echo htmlspecialchars($settings['port'] ?? ''); ?>" required placeholder="587">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($settings['username'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($settings['password'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Encryption</label>
                            <select name="encryption" class="form-select">
                                <option value="tls" <?php echo (($settings['encryption'] ?? '') == 'tls') ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo (($settings['encryption'] ?? '') == 'ssl') ? 'selected' : ''; ?>>SSL</option>
                                <option value="none" <?php echo (($settings['encryption'] ?? '') == 'none') ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Sender Details</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">From Email</label>
                            <input type="email" name="from_email" class="form-control" value="<?php echo htmlspecialchars($settings['from_email'] ?? ''); ?>" required placeholder="no-reply@domain.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">From Name</label>
                            <input type="text" name="from_name" class="form-control" value="<?php echo htmlspecialchars($settings['from_name'] ?? ''); ?>" required placeholder="Site Admin">
                        </div>
                        
                        <div class="col-12 mt-4">
                            <button type="submit" name="save_settings" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Configuration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Test Email Form -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Test Configuration</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Send a test email to verify your SMTP settings are working correctly.</p>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" name="test_recipient" class="form-control" required placeholder="your@email.com">
                    </div>
                    <button type="submit" name="test_email" class="btn btn-secondary w-100">
                        <i class="fas fa-paper-plane me-1"></i> Send Test Email
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
             <div class="card-body">
                <h6 class="card-title">Help & Tips</h6>
                <ul class="text-muted small ps-3">
                    <li>For Gmail, use port 587 (TLS) or 465 (SSL).</li>
                    <li>You may need to enable "Less Secure Apps" or use an App Password if using Gmail.</li>
                    <li>Check your spam folder if you don't receive the test email.</li>
                </ul>
             </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
