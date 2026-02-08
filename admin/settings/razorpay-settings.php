<?php
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$message = "";
$messageType = "";

// Fetch Current Settings
$query = "SELECT * FROM razorpay_settings LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle Save
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['save_settings'])) {
        // Save Settings Logic
        $key_id = $_POST['key_id'];
        $key_secret = $_POST['key_secret'];
        $webhook_secret = $_POST['webhook_secret'];
        $currency = $_POST['currency'];
        $mode = $_POST['mode'];
        
        try {
            if ($settings) {
                // Update
                $sql = "UPDATE razorpay_settings SET key_id=:key_id, key_secret=:key_secret, webhook_secret=:webhook_secret, currency=:currency, mode=:mode, updated_at=NOW() WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $settings['id']);
            } else {
                // Insert
                $sql = "INSERT INTO razorpay_settings (key_id, key_secret, webhook_secret, currency, mode, created_at, updated_at) VALUES (:key_id, :key_secret, :webhook_secret, :currency, :mode, NOW(), NOW())";
                $stmt = $db->prepare($sql);
            }
            
            $stmt->bindParam(':key_id', $key_id);
            $stmt->bindParam(':key_secret', $key_secret);
            $stmt->bindParam(':webhook_secret', $webhook_secret);
            $stmt->bindParam(':currency', $currency);
            $stmt->bindParam(':mode', $mode);
            
            if ($stmt->execute()) {
                $message = "Razorpay Settings saved successfully!";
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
    }
}
?>

<div class="page-header">
    <div class="header-title">
        <h1>Razorpay Settings</h1>
        <p>Configure payment gateway credentials and webhooks</p>
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
                <h5 class="card-title mb-0">API Configuration</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Mode</label>
                            <select name="mode" class="form-select">
                                <option value="test" <?php echo (($settings['mode'] ?? 'test') == 'test') ? 'selected' : ''; ?>>Test Mode</option>
                                <option value="live" <?php echo (($settings['mode'] ?? '') == 'live') ? 'selected' : ''; ?>>Live Mode</option>
                            </select>
                            <small class="text-muted">Select 'Test Mode' for development and 'Live Mode' for production transactions.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Key ID</label>
                            <input type="text" name="key_id" class="form-control" value="<?php echo htmlspecialchars($settings['key_id'] ?? ''); ?>" required placeholder="rzp_test_...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Key Secret</label>
                            <input type="password" name="key_secret" class="form-control" value="<?php echo htmlspecialchars($settings['key_secret'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Webhook Secret</label>
                            <input type="text" name="webhook_secret" class="form-control" value="<?php echo htmlspecialchars($settings['webhook_secret'] ?? ''); ?>" placeholder="Optional">
                            <small class="text-muted">Used to verify webhook signature.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select">
                                <option value="INR" <?php echo (($settings['currency'] ?? 'INR') == 'INR') ? 'selected' : ''; ?>>INR (Indian Rupee)</option>
                                <option value="USD" <?php echo (($settings['currency'] ?? '') == 'USD') ? 'selected' : ''; ?>>USD (US Dollar)</option>
                            </select>
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
    
    <!-- Information Panel -->
    <div class="col-lg-4">
        <div class="card">
             <div class="card-body">
                <h6 class="card-title">Setup Instructions</h6>
                <ul class="text-muted small ps-3">
                    <li>Log in to your Razorpay Dashboard.</li>
                    <li>Go to <strong>Settings > API Keys</strong>.</li>
                    <li>Generate a Key ID and Key Secret.</li>
                    <li>For Webhooks, go to <strong>Settings > Webhooks</strong> and add a new webhook URL.</li>
                    <li>Copy the <strong>Webhook Secret</strong> and paste it here.</li>
                </ul>
             </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php
                    // Fetch recent transactions
                    $trans_query = "SELECT * FROM razorpay_transactions ORDER BY created_at DESC LIMIT 5";
                    $trans_stmt = $db->prepare($trans_query);
                    $trans_stmt->execute();
                    $transactions = $trans_stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($transactions) > 0) {
                        foreach ($transactions as $trans) {
                            $status_color = ($trans['status'] == 'captured' || $trans['status'] == 'authorized') ? 'success' : 'warning';
                            echo '<div class="list-group-item">';
                            echo '<div class="d-flex w-100 justify-content-between">';
                            echo '<h6 class="mb-1" style="font-size: 0.9rem;">' . htmlspecialchars($trans['payment_id']) . '</h6>';
                            echo '<small class="text-' . $status_color . '">' . ucfirst($trans['status']) . '</small>';
                            echo '</div>';
                            echo '<p class="mb-1" style="font-size: 0.85rem;">₹' . number_format($trans['amount'], 2) . '</p>';
                            echo '<small class="text-muted">' . date('d M H:i', strtotime($trans['created_at'])) . '</small>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-group-item text-center text-muted">No transactions found.</div>';
                    }
                    ?>
                </div>
            </div>
             <div class="card-footer text-center">
                <a href="#" class="small text-decoration-none">View All Transactions</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
